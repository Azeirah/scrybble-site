import * as React from "react"
import {useEffect, useState} from "react"

import {createRoot} from "react-dom/client"
import {Provider} from "react-redux"
import {store} from "./store/store"
import {BrowserRouter, Route, Routes, useNavigate, useParams} from "react-router-dom"
import LoginCard from "./components/feature/LoginCard/LoginCard"
import {useAppDispatch} from "./store/hooks"
import {ResetPasswordData, useGetUserQuery, useResetPasswordMutation} from "./store/api/apiRoot"
import {setCredentials} from "./store/AuthSlice"
import {AuthPage} from "./layout/AuthLayout"
import {MainLayout} from "./layout/MainLayout/MainLayout"
import {Roadmap} from "./pages/Roadmap"
import {RegisterCard} from "./components/feature/RegisterCard/RegisterCard"
import {LandingPage} from "./pages/LandingPage/LandingPage"
import {Toaster} from "react-hot-toast"
import ResetPasswordCard from "./components/feature/ResetPasswordCard/ResetPasswordCard"
import {ErrorResponse} from "./laravelTypes"
import {BrowserTracing} from "@sentry/tracing"
import * as Sentry from "@sentry/react"

let Dashboard = React.lazy(() => import("./pages/Dashboard"))
let InspectSync = React.lazy(() => import("./pages/InspectSync/InspectSync"))
let PurchasedPage = React.lazy(() => import("./pages/PurchasedPage"))

Sentry.init({
    dsn: "https://4201915825194ef6ab9263518b836ee4@o199243.ingest.sentry.io/4504527483305984",
    integrations: [new BrowserTracing()],

    // Set tracesSampleRate to 1.0 to capture 100%
    // of transactions for performance monitoring.
    // We recommend adjusting this value in production
    tracesSampleRate: 1.0
})

function ResetPasswordTokenCard() {
    const [resetPassword, {isSuccess, error, data}] = useResetPasswordMutation()
    const {token} = useParams()
    const urlParams = new URLSearchParams(window.location.search)
    const [email, setEmail] = useState(urlParams.get("email"))
    const [csrfFetched, setCsrfFetched] = useState(false)

    fetch("/sanctum/csrf-cookie").then(() => {
        setCsrfFetched(true)
    })

    // function hasAnyErrors(): boolean {
    //     let err = error as ErrorResponse
    //     return Boolean(err?.data.errors)
    // }
    //
    // function allErrors(): [] {
    //     let err = error as ErrorResponse
    //     return err?.data.errors ?? []
    // }

    function hasError(name: string): boolean {
        let err = error as ErrorResponse
        return err?.data.errors.hasOwnProperty(name) ?? false
    }

    function errMsg(name: string): string {
        let err = error as ErrorResponse
        return err?.data.errors[name][0] ?? ""
    }

    return <div className="page-centering-container">
        <div className="card-dark col-md-6">
            <div className="card-header">Choose a new password</div>
            <form className="card-body" onSubmit={(e) => {
                e.preventDefault()
                const formData = new FormData(e.currentTarget) as unknown as ResetPasswordData
                resetPassword(formData)
            }}>
                <input type="hidden" name="token" value={token}/>

                <div>
                    <label htmlFor="email">Your email</label>
                    <input value={email} onChange={(e) => {
                        setEmail(e.currentTarget.value)
                    }} id="email" className={`form-control${hasError("email") ? " is-invalid" : ""}`} type="email"
                           name="email" required autoFocus/>
                    {hasError("email") ?
                        <span className="invalid-feedback" role="alert">
                            <strong>{errMsg("email")}</strong>
                        </span>
                        : null}
                </div>

                <div className="mt-4">
                    <label htmlFor="password">Pick a new password</label>
                    <input id="password" className={`form-control${hasError("password") ? " is-invalid" : ""}`}
                           type="password" name="password" required/>
                    {hasError("password") ?
                        <span className="invalid-feedback" role="alert">
                            <strong>{errMsg("password")}</strong>
                        </span>
                        : null}
                </div>

                <div className="mt-4 mb-3">
                    <label htmlFor="password_confirmation">Confirm your password</label>
                    <input id="password_confirmation" className="form-control"
                           type="password"
                           name="password_confirmation" required/>
                </div>

                <button type="submit" className="btn btn-primary" disabled={!csrfFetched}>Reset password</button>
                {isSuccess ? <div><strong className="text-success">{data.message}</strong></div> : null}
            </form>
        </div>
    </div>
}

function AppRoutes() {
    return (
        <React.Suspense>
            <Routes>
                <Route path="/" element={<MainLayout/>}>
                    <Route path="purchased" element={<PurchasedPage/>}/>
                    <Route path="dashboard" element={<Dashboard/>}/>
                    <Route path="inspect-sync" element={<InspectSync/>}/>
                    <Route path="auth" element={<AuthPage/>}>
                        <Route path="login" element={<LoginCard/>}/>
                        <Route path="register" element={<RegisterCard/>}/>
                        <Route path="reset-password" element={<ResetPasswordCard/>}/>
                    </Route>
                    <Route path="base" element={<AuthPage/>}>
                        <Route path="reset-password/:token" element={<ResetPasswordTokenCard/>}/>
                    </Route>
                    <Route path="roadmap" element={<Roadmap/>}/>
                    <Route index element={<LandingPage/>}/>
                </Route>
            </Routes>
        </React.Suspense>
    )
}

function Auth() {
    const {data, isSuccess} = useGetUserQuery()
    const navigate = useNavigate()
    const dispatch = useAppDispatch()

    useEffect(() => {
        if (isSuccess) {
            if (data) {
                dispatch(setCredentials(data))
            } else {
                navigate("/auth/login")
            }
        }
    }, [isSuccess])

    return null
}

export default function App() {

    return <Provider store={store}>
        <BrowserRouter>
            <AppRoutes/>
            <Auth/>
            <Toaster/>
        </BrowserRouter>
    </Provider>
}

const root = document.querySelector("#root")
if (root) {
    createRoot(root).render(<App/>)
}
