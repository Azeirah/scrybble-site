import {BaseQueryFn, createApi, FetchArgs, fetchBaseQuery, FetchBaseQueryError} from "@reduxjs/toolkit/query/react";
import {User} from "../AuthSlice";

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

// const baseQuery = fetchBaseQuery({baseUrl: '/'})
// const scrybbleBaseQuery: BaseQueryFn<string | FetchArgs,
//     unknown,
//     FetchBaseQueryError> = async (args, api, extraOptions) => {
//     console.log("Cookie not available yet")
//     const response = await fetch("sanctum/csrf-cookie")
//     await response.text();
//     console.log("cookie available");
//     const sendArgs = typeof args === "string" ? {url: args} : args;
//
//     return baseQuery(args, api, extraOptions);
// }

async function sleep(time = 1000) {
    return new Promise<void>((resolve) => {
        window.setTimeout(() => {
            resolve();
        }, time);
    });
}

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
    useLogoutMutation
} = apiRoot;
