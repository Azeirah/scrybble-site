import {useAppSelector} from "../../store/hooks"
import {useLogoutMutation} from "../../store/api/apiRoot"
import {Link, Outlet} from "react-router-dom"
import * as React from "react"
import "./MainLayout.scss"

export function MainLayout() {
    const user = useAppSelector((state) => state.auth.user)
    const [logout, {}] = useLogoutMutation()

    return <div id="mainLayout">
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
                            <Link to="/roadmap" className="nav-link">Roadmap</Link>
                        </li>
                        <li className="nav-item border-right border-dark border"></li>
                        {user ?
                            <>
                                <li className="nav-item">
                                    <Link to="/dashboard" className="nav-link">Dashboard</Link>
                                </li>
                                <li className="nav-item">
                                    <Link to="/inspect-sync" className="nav-link">Sync status</Link>
                                </li>
                            </> : null
                        }
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
                                      data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {user.name}
                                </Link>

                                <div className="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a className="dropdown-item"
                                       onClick={(e) => {
                                           e.preventDefault()
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
        <div className="alert alert-warning w-50 m-auto mt-4">
            ReMarkable 3.0 software is not yet supported.
        </div>
        <div style={{flexGrow: 1, flexShrink: 0, display: "flex", justifyContent: "center", alignItems: "center"}}>
            <Outlet/>
        </div>
        <footer className="border-top border-2 border-dark">
            <span>© {(new Date()).getFullYear()} Streamsoft. Streamsoft is a sole-proprietorship registered in the
                Netherlands.</span>
        </footer>
    </div>
}
