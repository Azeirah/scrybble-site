import * as React from 'react';

import {createRoot} from 'react-dom/client';
import {Provider} from "react-redux";
import {store} from "./store/store";
import {BrowserRouter, Outlet, Route, Routes, useNavigate} from "react-router-dom";
import LoginCard from "./components/feature/LoginCard/LoginCard";
import {useAppDispatch, useAppSelector} from "./store/hooks";
import {useEffect} from "react";
import {useGetUserQuery, useLazyGetUserQuery} from "./store/api/apiRoot";
import {setCredentials} from "./store/AuthSlice";


function AuthPage() {
    return <div className="page-centering-container">
        <Outlet/>
    </div>
}

function AppRoutes() {
    return (
        <Routes>
            <Route path="/" element={<div>hi</div>}></Route>
            <Route path="/auth" element={<AuthPage/>}>
                <Route path="login" element={<LoginCard/>}></Route>
            </Route>
        </Routes>
    )
}

function Auth() {
    const {data, isSuccess} = useGetUserQuery();
    const user = useAppSelector((state) => state.auth.user);
    const navigate = useNavigate();
    const dispatch = useAppDispatch();

    useEffect(() => {
        if (isSuccess) {
            if (data) {
                dispatch(setCredentials(data));
            } else {
                navigate("/auth/login");
            }
        }
    }, [isSuccess]);

    return null;
}

export default function App() {
    fetch("/sanctum/csrf-cookie")

    return <Provider store={store}>
        <BrowserRouter>
            <AppRoutes/>
            <Auth/>
        </BrowserRouter>
        {/*<div className="page-centering-container">*/}
        {/*    <LoginCard/>*/}
        {/*</div>*/}
    </Provider>
}

const root = document.querySelector("#root");
if (root) {
    createRoot(root).render(<App/>);
}
