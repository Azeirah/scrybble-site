import * as React from 'react';
import {useEffect} from 'react';

import {createRoot} from 'react-dom/client';
import {Provider} from "react-redux";
import {store} from "./store/store";
import {BrowserRouter, Route, Routes, useNavigate} from "react-router-dom";
import LoginCard from "./components/feature/LoginCard/LoginCard";
import {useAppDispatch} from "./store/hooks";
import {useGetUserQuery} from "./store/api/apiRoot";
import {setCredentials} from "./store/AuthSlice";
import {AuthPage} from "./layout/AuthLayout";
import {MainLayout} from "./layout/MainLayout";
import {Roadmap} from "./pages/Roadmap";
import {RegisterCard} from "./components/feature/RegisterCard/RegisterCard";
import {LandingPage} from "./pages/LandingPage";


function Dashboard() {
    return null;
}

function AppRoutes() {
    return (
        <Routes>
            <Route path="/" element={<MainLayout/>}>
                <Route path="auth" element={<AuthPage/>}>
                    <Route path="login" element={<LoginCard/>}/>
                    <Route path="register" element={<RegisterCard/>}/>
                </Route>
                <Route path="dashboard" element={<Dashboard/>}/>
                <Route path="roadmap" element={<Roadmap/>}/>
                <Route index element={<LandingPage/>}/>
            </Route>
        </Routes>
    )
}

function Auth() {
    const {data, isSuccess} = useGetUserQuery();
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

    return <Provider store={store}>
        <BrowserRouter>
            <AppRoutes/>
            <Auth/>
        </BrowserRouter>
    </Provider>
}

const root = document.querySelector("#root");
if (root) {
    createRoot(root).render(<App/>);
}
