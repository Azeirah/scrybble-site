import {createApi, fetchBaseQuery} from "@reduxjs/toolkit/query/react"
import {setCredentials, User} from "../AuthSlice.ts"
import {useNavigate} from "react-router-dom"
import {useAppDispatch} from "../hooks.ts"
import {useEffect} from "react"
import * as Sentry from "@sentry/react"
import {BaseQueryArg} from "@reduxjs/toolkit/dist/query/baseQueryTypes.js";

function getCookie(name) {
    const value = `; ${document.cookie}`
    const parts = value.split(`; ${name}=`)
    if (parts.length === 2) return parts.pop().split(";").shift()
}

export type LoginData = {
    email: string;
    password: string;
    remember?: true;
}

export type RequestPasswordResetData = {
    email: string;
}

export type ResetPasswordData = {
    email: string;
    password: string;
    password_confirmation: string;
    token: string;
}

export interface RegisterForm {
    name: string,
    email: string,
    password: string
}

interface RMTreeItem {
    type: "f" | "d"
    name: string,
    path: string
}

export interface File extends RMTreeItem {
    type: "f",
}

export interface Directory extends RMTreeItem {
    type: "d",
}

export type LicenseInformation = {
    license: string;
    lifetime: boolean;
} & {
    exists: false;
    lifetime: boolean;
} | {
    exists: true;
    lifetime: boolean;
    licenseInformation: {
        active: boolean;
        uses: number;
        order_number: number;
        sale_id: string;
        subscription_id: string;
    }
}

export type OnboardingState = "setup-gumroad" | "setup-one-time-code" | "setup-one-time-code-again" | "ready";

export type OnetimecodeQuery = { code }
export type SyncStatus = { id: number; filename: string, created_at: string, completed: boolean, error: boolean }
export const apiRoot = createApi({
    reducerPath: "api",
    baseQuery: fetchBaseQuery({
        prepareHeaders: async (headers) => {
            headers.set("Accept", "application/json")
            headers.set("X-XSRF-TOKEN", decodeURIComponent(getCookie("XSRF-TOKEN")))
            return headers
        }
    }),
    tagTypes: ["sync-status"],

    endpoints: (builder) => ({
        login: builder.mutation<void, LoginData>({
            query: (user) => {
                return ({
                    url: "/login",
                    method: "POST",
                    body: user
                })
            }
        }),
        requestPasswordReset: builder.mutation<void, RequestPasswordResetData>({
            query: (email) => {
                return {
                    url: "/forgot-password",
                    method: "POST",
                    body: email
                }
            }
        }),
        resetPassword: builder.mutation<{ message: string }, ResetPasswordData>({
            query: (body) => {
                return {
                    url: "/reset-password",
                    method: "POST",
                    body
                }
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
            query: () => "/sanctum/user",
            async onQueryStarted(_, {queryFulfilled}) {
                try {
                    const result = await queryFulfilled;
                    Sentry.setTags({
                        name: result.data.name,
                        email: result.data.email
                    });
                } catch (e) {
                    Sentry.setTags({
                        name: null,
                        email: null
                    })
                }
            }
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
                const {data: {newState}} = await queryFulfilled
                dispatch(apiRoot.util.updateQueryData("onboardingState", undefined, () => {
                    return newState
                }))
            }
        }),
        licenseInformation: builder.query<LicenseInformation, void>({
            query: () => ({
                url: "/api/licenseInformation",
                method: "GET"
            })
        }),
        sendOnetimecode: builder.mutation<{ newState: OnboardingState }, OnetimecodeQuery>({
            query: (body) => ({
                url: "api/onetimecode",
                method: "POST",
                body
            }),
            async onQueryStarted(license, {dispatch, queryFulfilled}) {
                const {data: {newState}} = await queryFulfilled
                dispatch(apiRoot.util.updateQueryData("onboardingState", undefined, () => {
                    return newState
                }))
            }
        }),
        RMFileTree: builder.query<{ items: ReadonlyArray<RMTreeItem>, cwd: string }, string | void>({
            query(path = "/") {
                return {
                    url: `api/RMFileTree`,
                    method: "POST",
                    body: {
                        path
                    }
                }
            }
        }),
        selectFileForSync: builder.mutation<unknown, string>({
            query(file) {
                return {
                    url: "api/file",
                    method: "POST",
                    body: {
                        file
                    }
                }
            },
            invalidatesTags: ["sync-status"]
        }),
        syncStatus: builder.query<SyncStatus[], void>({
            query() {
                return {
                    url: "api/inspect-sync",
                    method: "GET"
                }
            },
            providesTags: ["sync-status"]
        }),
        gumroadSaleInfo: builder.query<{ email: string, license_key: string }, string>({
            query(sale_id) {
                return {
                    url: `/api/gumroadSale/${sale_id}`,
                    method: "GET"
                }
            }
        }),
        posts: builder.query<{ title: string, slug: string }[], void>({
            query() {
                return {
                    url: `/api/posts`,
                    method: "GET"
                }
            }
        }),
        post: builder.query<{ title: string; content: string; created_at: string }, string>({
            query(slug) {
                return {
                    url: `/api/posts/${slug}`,
                    method: "GET"
                }
            }
        }),
        shareRemarkableDocument: builder.mutation<void, {
            sync_id: number,
            feedback?: string,
            developer_access_consent_granted: boolean,
            open_access_consent_granted: boolean
        }>({
            query(body) {
                return {
                    url: `/api/remarkable-document-share`,
                    method: "POST",
                    body
                }
            }
        })
    })
})

function useLogin(to = "/") {
    const [getUser, {isSuccess: loggedIn, data: userData}] = useLazyGetUserQuery()
    const navigate = useNavigate()
    const dispatch = useAppDispatch()

    useEffect(() => {
        if (loggedIn && userData) {
            dispatch(setCredentials(userData))
            navigate(to)
        }
    }, [loggedIn, userData])

    return getUser
}

export const {
    useLoginMutation,
    useLazyGetUserQuery,
    useGetUserQuery,
    useLogoutMutation,
    useRegisterMutation,
    useOnboardingStateQuery,
    useSendGumroadLicenseMutation,
    useSendOnetimecodeMutation,
    useRMFileTreeQuery,
    useSelectFileForSyncMutation,
    useSyncStatusQuery,
    useRequestPasswordResetMutation,
    useResetPasswordMutation,
    useGumroadSaleInfoQuery,
    useLicenseInformationQuery,
    usePostsQuery,
    usePostQuery,

    useShareRemarkableDocumentMutation
} = apiRoot

export {useLogin}
