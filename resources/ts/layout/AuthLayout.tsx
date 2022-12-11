import {Outlet} from "react-router-dom";
import * as React from "react";

export function AuthPage() {
    fetch("/sanctum/csrf-cookie")
    return <div className="page-centering-container">
        <Outlet/>
    </div>
}
