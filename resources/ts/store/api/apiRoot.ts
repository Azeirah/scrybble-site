import {createApi, fetchBaseQuery} from "@reduxjs/toolkit/query/react";
import {setCredentials, User} from "../AuthSlice";
import {useNavigate} from "react-router-dom";
import {useAppDispatch} from "../hooks";
import {useEffect} from "react";

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

export type OnboardingState = "setup-gumroad" | "setup-one-time-code" | "ready";

export const apiRoot = createApi({
    reducerPath: 'api',
    baseQuery: fetchBaseQuery({
        prepareHeaders: async (headers, {getState}) => {
            headers.set("Accept", "application/json");
            headers.set("X-XSRF-TOKEN", decodeURIComponent(getCookie("XSRF-TOKEN")));
            return headers;
        },
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
        register: builder.mutation<unknown, RegisterForm>({
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
        }),
        onboardingState: builder.query<OnboardingState, void>({
            query: () => "/api/onboardingState"
        }),
        sendGumroadLicense: builder.mutation<{ newState: OnboardingState }, string>({
            query: (license) => ({
                url: "/api/gumroadLicense",
                method: "POST",
                body: {license}
            }),
            async onQueryStarted(license, {dispatch, queryFulfilled}) {
                const {data: {newState}} = await queryFulfilled;
                dispatch(apiRoot.util.updateQueryData('onboardingState', undefined, () => {
                    return newState
                }))
            }
        })
    })
});

function useLogin(to = '/') {
    const [getUser, {isSuccess: loggedIn, data: userData}] = useLazyGetUserQuery();
    const navigate = useNavigate();
    const dispatch = useAppDispatch();

    useEffect(() => {
        if (loggedIn && userData) {
            dispatch(setCredentials(userData));
            navigate(to);
        }
    }, [loggedIn, userData]);

    return getUser;
}

export const {
    useLoginMutation,
    useLazyGetUserQuery,
    useGetUserQuery,
    useLogoutMutation,
    useRegisterMutation,
    useOnboardingStateQuery,
    useSendGumroadLicenseMutation
} = apiRoot;

export {useLogin};
