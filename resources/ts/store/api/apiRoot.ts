import {BaseQueryFn, createApi, FetchArgs, fetchBaseQuery, FetchBaseQueryError} from "@reduxjs/toolkit/query/react";
import {User} from "../AuthSlice";
import {ErrorResponse} from "../../laravelTypes";

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

export type LoginData = {
    email: string;
    password: string;
    remember?: true;
}

export interface RegisterForm {
    name: string,
    email: string,
    password: string
}

type RegisterResponse = ErrorResponse;

export const apiRoot = createApi({
    reducerPath: 'api',
    baseQuery: fetchBaseQuery({
        prepareHeaders: async (headers, {getState}) => {
            headers.set("Accept", "application/json");
            headers.set("X-XSRF-TOKEN", decodeURIComponent(getCookie("XSRF-TOKEN")));
            return headers;
        }
    }),

    endpoints: (builder) => ({
        login: builder.mutation<void, LoginData>({
            query: (user) => {
                return ({
                    url: "/login",
                    method: "POST",
                    body: user
                });
            }
        }),
        register: builder.mutation<RegisterResponse, RegisterForm>({
            query: (registration) => ({
                url: "/register",
                method: "POST",
                body: registration
            })
        }),
        getUser: builder.query<User, void>({
            query: () => "/sanctum/user"
        }),
        logout: builder.mutation<void, void>({
            query: () => ({url: "/logout", method: "POST"})
        })
    })
});

export const {
    useLoginMutation,
    useLazyGetUserQuery,
    useGetUserQuery,
    useLogoutMutation,
    useRegisterMutation
} = apiRoot;
