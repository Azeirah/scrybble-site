import * as React from 'react';
import {useEffect} from 'react';

import {createRoot} from 'react-dom/client';
import {Provider} from "react-redux";
import {store} from "./store/store";
import {BrowserRouter, Link, Outlet, Route, Routes, useNavigate} from "react-router-dom";
import LoginCard from "./components/feature/LoginCard/LoginCard";
import {useAppDispatch, useAppSelector} from "./store/hooks";
import {useGetUserQuery, useLogoutMutation} from "./store/api/apiRoot";
import {setCredentials} from "./store/AuthSlice";


function AuthPage() {
    return <div className="page-centering-container">
        <Outlet/>
    </div>
}

function Layout() {
    const user = useAppSelector((state) => state.auth.user);
    const [logout, {}] = useLogoutMutation();

    return <>
        <nav className="navbar navbar-expand-md navbar-dark shadow-sm">
            <div className="container">
                <Link className="navbar-brand" to="/">
                    Scrybble
                </Link>
                <button className="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span className="navbar-toggler-icon"/>
                </button>

                <div className="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul className="navbar-nav me-auto">
                        <li className="nav-item">
                            <Link to="/dashboard" className="nav-link">Dashboard</Link>
                        </li>
                        <li className="nav-item">
                            <Link to="/roadmap" className="nav-link">Roadmap</Link>
                        </li>
                    </ul>

                    <ul className="navbar-nav ms-auto">
                        {!user ?
                            <>
                                <li className="nav-item">
                                    <Link className="nav-link" to="/auth/login">Login</Link>
                                </li>

                                <li className="nav-item">
                                    <Link className="nav-link" to="/auth/register">Register</Link>
                                </li>
                            </>
                            :
                            <li className="nav-item dropdown">
                                <Link id="navbarDropdown" className="nav-link dropdown-toggle" to="#" role="button"
                                      data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {user.name}
                                </Link>

                                <div className="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a className="dropdown-item"
                                          onClick={(e) => {
                                              e.preventDefault();
                                              logout()
                                          }}
                                    >Logout
                                    </a>
                                </div>
                            </li>
                        }
                    </ul>
                </div>
            </div>
        </nav>
        <Outlet/>
    </>
}

function AppRoutes() {
    return (
        <Routes>
            <Route path="/" element={<Layout/>}>
                <Route path="auth" element={<AuthPage/>}>
                    <Route path="login" element={<LoginCard/>}></Route>
                </Route>
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
