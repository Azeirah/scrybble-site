import * as React from 'react';
import {useEffect} from 'react';

import {createRoot} from 'react-dom/client';
import {Provider} from "react-redux";
import {store} from "./store/store";
import {BrowserRouter, Route, Routes, useNavigate} from "react-router-dom";
import LoginCard from "./components/feature/LoginCard/LoginCard";
import {useAppDispatch} from "./store/hooks";
import {RegisterForm, useGetUserQuery, useLazyGetUserQuery, useRegisterMutation} from "./store/api/apiRoot";
import {setCredentials} from "./store/AuthSlice";
import {AuthPage} from "./layout/AuthLayout";
import {MainLayout} from "./layout/MainLayout";
import {Roadmap} from "./pages/Roadmap";


function Dashboard() {
    return null;
}

function useLogin() {
    const [getUser, {isSuccess: loggedIn, data: userData}] = useLazyGetUserQuery();
    const navigate = useNavigate();
    const dispatch = useAppDispatch();

    useEffect(() => {
        if (loggedIn && userData) {
            dispatch(setCredentials(userData));
            navigate('/');
        }
    }, [loggedIn, userData]);

    return getUser;
}

function RegisterCard() {
    const [register, {error, isSuccess}] = useRegisterMutation();
    const login = useLogin();

    function hasError(name: string): boolean {
        return error?.data.errors.hasOwnProperty(name) ?? false;
    }

    function errMsg(name: string): string {
        return error?.data.errors[name][0] ?? "";
    }

    useEffect(() => {
       if (isSuccess) {
           login();
       }
    }, [isSuccess]);

    return <div className="page-centering-container">
        <div className="col-md-8">
            <div className="card-dark">
                <div className="card-header">Register</div>

                <div className="card-body">
                    <form onSubmit={(e) => {
                        e.preventDefault();
                        const registration = new FormData(e.currentTarget) as unknown as RegisterForm;

                        register(registration);
                    }}>
                        <div className="row mb-3">
                            <label htmlFor="name" className="col-md-4 col-form-label text-md-end">Name</label>

                            <div className="col-md-6">
                                <input id="name" type="text"
                                       className={`form-control${hasError('name') ? ' is-invalid' : ''}`}
                                       name="name" required autoComplete="name" autoFocus/>

                                {hasError('name') ?
                                    <span className="invalid-feedback" role="alert">
                                    <strong>{errMsg('name')}</strong>
                                </span> : null}
                            </div>
                        </div>

                        <div className="row mb-3">
                            <label htmlFor="email"
                                   className="col-md-4 col-form-label text-md-end">Email</label>

                            <div className="col-md-6">
                                <input id="email" type="email"
                                       className={`form-control${hasError('email') ? ' is-invalid' : ''}`}
                                       name="email" required autoComplete="email"/>

                                {hasError('email') ?
                                    <span className="invalid-feedback" role="alert">
                                    <strong>{errMsg('email')}</strong>
                                </span> : null}
                            </div>
                        </div>

                        <div className="row mb-3">
                            <label htmlFor="password"
                                   className="col-md-4 col-form-label text-md-end">Password</label>

                            <div className="col-md-6">
                                <input id="password" type="password"
                                       className={`form-control${hasError('password') ? ' is-invalid' : ''}`}
                                       name="password"
                                       required autoComplete="new-password"/>

                                {hasError('password') ?
                                    <span className="invalid-feedback" role="alert">
                                    <strong>{errMsg('password')}</strong>
                                </span>
                                    : null}
                            </div>
                        </div>

                        <div className="row mb-3">
                            <label htmlFor="password-confirm"
                                   className="col-md-4 col-form-label text-md-end">Confirm password</label>

                            <div className="col-md-6">
                                <input id="password-confirm" type="password" className="form-control"
                                       name="password_confirmation" required autoComplete="new-password"/>
                            </div>
                        </div>

                        <div className="row mb-0">
                            <div className="col-md-6 offset-md-4">
                                <button type="submit" className="btn btn-primary">Register
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
    fetch("/sanctum/csrf-cookie")

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
