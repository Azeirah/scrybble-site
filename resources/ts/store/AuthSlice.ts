
import {createSlice, PayloadAction} from "@reduxjs/toolkit";
import {RootState} from "./store";

export type User = {
    name: string;
    email: string;
}

type AuthState = {
    user: User | null
    token: string | null
}

const slice = createSlice({
    name: 'auth',
    initialState: { user: null, token: null } as AuthState,
    reducers: {
        setCredentials: (
            state,
            { payload: user }: PayloadAction<User>
        ) => {
            state.user = user;
        },
    },
})

export const { setCredentials } = slice.actions

export default slice.reducer

export const selectCurrentUser = (state: RootState) => state.auth.user
