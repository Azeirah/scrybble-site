import React from "react"
import {Link} from "react-router-dom"

export function LandingPage() {
    return <div id="landing-page" style={{height: "100%"}}
                className="container d-flex align-items-center justify-content-center text-center">
        <main>
            <h1>ReMarkable to Obsidian sync</h1>
            <p className="lead">This is the interface for the RM to Obsidian tool.</p>
            <p className="lead d-flex justify-content-center gap-2">
                <a href="https://streamsoft.gumroad.com/l/remarkable-to-obsidian"
                   className="btn btn-lg btn-secondary fw-bold border-white bg-white mr-4">Learn more
                </a>
                <Link to="/auth/register" className="btn btn-lg btn-outline-secondary fw-bold">I have a license</Link>
            </p>
        </main>
    </div>
}
