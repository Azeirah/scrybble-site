import {configureStore} from "@reduxjs/toolkit"
import {apiRoot} from "./api/apiRoot"
import authSlice from "./AuthSlice"
import * as Sentry from "@sentry/react"

const sentryReduxEnhancer = Sentry.createReduxEnhancer()

export const store = configureStore({
    reducer: {
        [apiRoot.reducerPath]: apiRoot.reducer,
        auth: authSlice
    },
    middleware: (getDefaultMiddleware) => getDefaultMiddleware().concat(apiRoot.middleware),
    enhancers: [sentryReduxEnhancer]
})

export type RootState = ReturnType<typeof store.getState>
export type AppDispatch = typeof store.dispatch
