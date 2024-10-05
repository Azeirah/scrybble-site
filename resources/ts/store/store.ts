import { configureStore } from "@reduxjs/toolkit";
import { apiRoot } from "./api/apiRoot.ts";
import * as Sentry from "@sentry/react";

const sentryReduxEnhancer = Sentry.createReduxEnhancer();

export const store = configureStore({
    reducer: {
        [apiRoot.reducerPath]: apiRoot.reducer
    },
    middleware: (getDefaultMiddleware) =>
        getDefaultMiddleware().concat(apiRoot.middleware),
    enhancers: (getDefaultEnhancers) => {
        return getDefaultEnhancers().concat(sentryReduxEnhancer);
    },
});

export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;
