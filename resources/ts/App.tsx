import * as React from "react"
import {useEffect} from "react"

import {createRoot} from "react-dom/client"
import {Provider} from "react-redux"
import {store} from "./store/store"
import {
    BrowserRouter,
    createRoutesFromChildren,
    Link,
    matchRoutes,
    Route,
    Routes,
    useLocation,
    useNavigate,
    useNavigationType,
    useParams
} from "react-router-dom"
import LoginCard from "./components/feature/LoginCard/LoginCard"
import {useAppDispatch} from "./store/hooks"
import {useGetUserQuery, usePostQuery, usePostsQuery} from "./store/api/apiRoot"
import {setCredentials} from "./store/AuthSlice"
import {AuthPage} from "./layout/AuthLayout"
import {MainLayout} from "./layout/MainLayout/MainLayout"
import {Roadmap} from "./pages/Roadmap"
import {RegisterCard} from "./components/feature/RegisterCard/RegisterCard"
import {LandingPage} from "./pages/LandingPage/LandingPage"
import {Toaster} from "react-hot-toast"
import ResetPasswordCard from "./components/feature/ResetPasswordCard/ResetPasswordCard"
import {BrowserTracing} from "@sentry/tracing"
import * as Sentry from "@sentry/react"
import {ResetPasswordTokenCard} from "./components/feature/ResetPasswordTokenCard"
import UserProfile from "./pages/UserProfile/UserProfile";
import ReactMarkdown from "react-markdown";

let Dashboard = React.lazy(() => import("./pages/Dashboard"))
let InspectSync = React.lazy(() => import("./pages/InspectSync/InspectSync"))
let PurchasedPage = React.lazy(() => import("./pages/PurchasedPage"))

Sentry.init({
    dsn: "https://4201915825194ef6ab9263518b836ee4@o199243.ingest.sentry.io/4504527483305984",
    integrations: [new BrowserTracing({
        routingInstrumentation: Sentry.reactRouterV6Instrumentation(
            React.useEffect,
            useLocation,
            useNavigationType,
            createRoutesFromChildren,
            matchRoutes
        )
    })],
    tunnel: "/tunnel",

    // Set tracesSampleRate to 1.0 to capture 100%
    // of transactions for performance monitoring.
    // We recommend adjusting this value in production
    tracesSampleRate: 1.0
})

const SentryRoutes = Sentry.withSentryReactRouterV6Routing(Routes)

function ObsidianPosts() {
    const {data, isLoading} = usePostsQuery();

    return <div style={{width: "640px"}}>
        <h1>Learning Obsidian</h1>
        <p>Obsidian is an incredibly powerful and open-ended tool for personal knowledge management. Learning to use
            Obsidian for your goals isn't a straight-forward task.</p>
        <p>Our guides are meant to be <b>practical</b> and easy to follow for any audience with a focus on optimizing
            your Obsidian vault for your goals.</p>

        <h2>Guides</h2>
        {isLoading ? "Loading..." :
            data.map((post) =>
                <Link to={`/learn/obsidian/${post.slug}`}>{post.title}</Link>
            )
        }
    </div>;
}

function ObsidianPost() {
    const {slug} = useParams();
    const {data: post, isLoading} = usePostQuery(slug);

    return <>
        {isLoading ?
            "Loading..." :
            <div style={{width: "640px"}}>
                <h1>{post.title}</h1>
                <ReactMarkdown>
                    {post.content}
                </ReactMarkdown>
            </div>
        }
    </>;
}

function AppRoutes() {
    return (
        <React.Suspense>
            <SentryRoutes>
                <Route path="/" element={<MainLayout/>}>
                    <Route path="purchased" element={<PurchasedPage/>}/>
                    <Route path="dashboard" element={<Dashboard/>}/>
                    <Route path="inspect-sync" element={<InspectSync/>}/>

                    <Route path="profile" element={<UserProfile/>}></Route>
                    <Route path="auth" element={<AuthPage/>}>
                        <Route path="login" element={<LoginCard/>}/>
                        <Route path="register" element={<RegisterCard/>}/>
                        <Route path="reset-password" element={<ResetPasswordCard/>}/>
                    </Route>
                    <Route path="base" element={<AuthPage/>}>
                        <Route path="reset-password/:token" element={<ResetPasswordTokenCard/>}/>
                    </Route>
                    <Route path="learn">
                        <Route path="obsidian" element={<ObsidianPosts/>}/>
                        <Route path="obsidian/:slug" element={<ObsidianPost/>}/>
                    </Route>
                    <Route path="roadmap" element={<Roadmap/>}/>
                    <Route index element={<LandingPage/>}/>
                </Route>
            </SentryRoutes>
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
