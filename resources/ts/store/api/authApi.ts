import {
  LoginData,
  RegisterForm,
  RequestPasswordResetData,
  ResetPasswordData,
  User,
} from '../../@types/Authentication.ts'
import * as Sentry from '@sentry/react'
import { apiRoot } from './apiRoot.ts'
import { useNavigate } from 'react-router-dom'
import { useEffect } from 'react'
import { createSelector } from '@reduxjs/toolkit'

const authenticationApi = apiRoot.injectEndpoints({
  endpoints: (builder) => ({
    login: builder.mutation<void, LoginData>({
      query: (user) => {
        return {
          url: '/login',
          method: 'POST',
          body: user,
        }
      },
    }),
    requestPasswordReset: builder.mutation<void, RequestPasswordResetData>({
      query: (email) => {
        return {
          url: '/forgot-password',
          method: 'POST',
          body: email,
        }
      },
    }),
    resetPassword: builder.mutation<{ message: string }, ResetPasswordData>({
      query: (body) => {
        return {
          url: '/reset-password',
          method: 'POST',
          body,
        }
      },
    }),
    register: builder.mutation<unknown, RegisterForm>({
      query: (registration) => ({
        url: '/register',
        method: 'POST',
        body: registration,
      }),
    }),
    getUser: builder.query<User, void>({
      query: () => '/sanctum/user',
      async onQueryStarted(_, { queryFulfilled }) {
        try {
          const result = await queryFulfilled
          Sentry.setTags({
            name: result.data.name,
            email: result.data.email,
          })
        } catch (e) {
          Sentry.setTags({
            name: null,
            email: null,
          })
        }
      },
      providesTags: ['user'],
    }),
    logout: builder.mutation<void, void>({
      query: () => ({ url: '/logout', method: 'POST' }),
      invalidatesTags: ['user'],
    }),
  }),
})

export function useLogin(to = '/') {
  const [getUser, { isSuccess: loggedIn, data: userData }] =
    useLazyGetUserQuery()
  const navigate = useNavigate()

  useEffect(() => {
    if (loggedIn && userData) {
      navigate(to)
    }
  }, [loggedIn, userData])

  return getUser
}

const selectUserResult = authenticationApi.endpoints.getUser.select()

export const selectUser = createSelector([selectUserResult], (userData) => {
  if (userData.isError) {
    return null
  }
  return userData.data
})

export const {
  useLoginMutation,
  useLazyGetUserQuery,
  useGetUserQuery,
  useLogoutMutation,
  useRegisterMutation,
  useRequestPasswordResetMutation,
  useResetPasswordMutation,
} = authenticationApi
