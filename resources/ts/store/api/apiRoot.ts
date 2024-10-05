import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react'
import { useNavigate } from 'react-router-dom'
import { useEffect } from 'react'
import * as Sentry from '@sentry/react'
import { createSelector } from '@reduxjs/toolkit'
import {
  LoginData,
  RegisterForm,
  RequestPasswordResetData,
  ResetPasswordData,
  User,
} from '../../@types/Authentication.ts'
import { RMTreeItem } from '../../@types/ReMarkable.ts'

function getCookie(name) {
  const value = `; ${document.cookie}`
  const parts = value.split(`; ${name}=`)
  if (parts.length === 2) return parts.pop().split(';').shift()
}

export type LicenseInformation =
  | ({
      license: string
      lifetime: boolean
    } & {
      exists: false
      lifetime: boolean
    })
  | {
      exists: true
      lifetime: boolean
      licenseInformation: {
        active: boolean
        uses: number
        order_number: number
        sale_id: string
        subscription_id: string
      }
    }

export type OnboardingState =
  | 'setup-gumroad'
  | 'setup-one-time-code'
  | 'setup-one-time-code-again'
  | 'ready'

export type OnetimecodeQuery = { code }
export type SyncStatus = {
  id: number
  filename: string
  created_at: string
  completed: boolean
  error: boolean
}

export const apiRoot = createApi({
  reducerPath: 'api',
  baseQuery: fetchBaseQuery({
    prepareHeaders: async (headers) => {
      headers.set('Accept', 'application/json')
      headers.set('X-XSRF-TOKEN', decodeURIComponent(getCookie('XSRF-TOKEN')))
      return headers
    },
  }),
  tagTypes: ['sync-status', 'user'],

  endpoints: (builder) => ({
    onboardingState: builder.query<OnboardingState, void>({
      query: () => '/api/onboardingState',
    }),
    sendGumroadLicense: builder.mutation<{ newState: OnboardingState }, string>(
      {
        query: (license) => ({
          url: '/api/gumroadLicense',
          method: 'POST',
          body: { license },
        }),
        async onQueryStarted(license, { dispatch, queryFulfilled }) {
          const {
            data: { newState },
          } = await queryFulfilled
          dispatch(
            apiRoot.util.updateQueryData('onboardingState', undefined, () => {
              return newState
            })
          )
        },
      }
    ),
    licenseInformation: builder.query<LicenseInformation, void>({
      query: () => ({
        url: '/api/licenseInformation',
        method: 'GET',
      }),
    }),
    sendOnetimecode: builder.mutation<
      { newState: OnboardingState },
      OnetimecodeQuery
    >({
      query: (body) => ({
        url: 'api/onetimecode',
        method: 'POST',
        body,
      }),
      async onQueryStarted(license, { dispatch, queryFulfilled }) {
        const {
          data: { newState },
        } = await queryFulfilled
        dispatch(
          apiRoot.util.updateQueryData('onboardingState', undefined, () => {
            return newState
          })
        )
      },
    }),
    RMFileTree: builder.query<
      { items: ReadonlyArray<RMTreeItem>; cwd: string },
      string | void
    >({
      query(path = '/') {
        return {
          url: `api/RMFileTree`,
          method: 'POST',
          body: {
            path,
          },
        }
      },
    }),
    selectFileForSync: builder.mutation<unknown, string>({
      query(file) {
        return {
          url: 'api/file',
          method: 'POST',
          body: {
            file,
          },
        }
      },
      invalidatesTags: ['sync-status'],
    }),
    syncStatus: builder.query<SyncStatus[], void>({
      query() {
        return {
          url: 'api/inspect-sync',
          method: 'GET',
        }
      },
      providesTags: ['sync-status'],
    }),
    gumroadSaleInfo: builder.query<
      { email: string; license_key: string },
      string
    >({
      query(sale_id) {
        return {
          url: `/api/gumroadSale/${sale_id}`,
          method: 'GET',
        }
      },
    }),
    posts: builder.query<{ title: string; slug: string }[], void>({
      query() {
        return {
          url: `/api/posts`,
          method: 'GET',
        }
      },
    }),
    post: builder.query<
      { title: string; content: string; created_at: string },
      string
    >({
      query(slug) {
        return {
          url: `/api/posts/${slug}`,
          method: 'GET',
        }
      },
    }),
    shareRemarkableDocument: builder.mutation<
      void,
      {
        sync_id: number
        feedback?: string
        developer_access_consent_granted: boolean
        open_access_consent_granted: boolean
      }
    >({
      query(body) {
        return {
          url: `/api/remarkable-document-share`,
          method: 'POST',
          body,
        }
      },
    }),
  }),
})

export const {
  useOnboardingStateQuery,
  useSendGumroadLicenseMutation,
  useSendOnetimecodeMutation,
  useRMFileTreeQuery,
  useSelectFileForSyncMutation,
  useSyncStatusQuery,
  useGumroadSaleInfoQuery,
  useLicenseInformationQuery,
  usePostsQuery,
  usePostQuery,

  useShareRemarkableDocumentMutation,
} = apiRoot
