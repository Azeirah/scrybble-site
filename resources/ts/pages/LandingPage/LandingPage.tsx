import React from "react"
import {Link} from "react-router-dom"

import "./LandingPage.scss"

export function LandingPage() {
    return <div id="landing-page">
        <main>
            <div className="text-center hero">
                <h1>Scrybble</h1>
                <p className="lead">The perfect companion for ReMarkable owners who want to access their notes in
                    Obsidian.</p>
                <a href="https://streamsoft.gumroad.com/l/remarkable-to-obsidian"
                   className="btn btn-lg btn-secondary fw-bold border-white bg-white">Learn more
                </a>
                <Link to="/auth/register" className="btn btn-lg btn-outline-secondary fw-bold">I have a license</Link>
            </div>
            <div className="block remarkable-to-obsidian">
                <div className="images">
                    <h2>From
                        <img className="remarkable" src="/img/rm-sketch.jpg" alt="ReMarkable logo"/>
                        to
                        <img className="obsidian" src="/img/obsidian-sketch.jpg" alt="obsidian-logo"/>
                    </h2>
                </div>
                <div className="copy">
                    <h2>Your ReMarkable notes in your Obsidian vault</h2>
                    <p>Wouldn't it be nice if you had access to everything on your ReMarkable tablet in
                        your
                        Obsidian vault?</p>
                    <ul>
                        <li>The highlights in your textbooks and documents</li>
                        <li>Your annotations in the margins</li>
                        <li>Your notebooks and quicksheets containing your ideas and sketches</li>
                    </ul>
                    <p className="lead">
                        <a href="https://streamsoft.gumroad.com/l/remarkable-to-obsidian"
                           className="btn btn-lg btn-secondary fw-bold border-white bg-white mr-4">I want my ReMarkable
                            notes in Obsidian!</a>
                    </p>
                </div>
            </div>
            <hr/>
            <div className="block faq">
                <div className="copy">
                    <h2>FAQ</h2>
                    <ul>
                        <li>Does this work with ReMarkable version 3.0?</li>
                        <ul>
                            <li>Yes! Scrybble works with ReMarkable version 3.0.</li>
                        </ul>
                        <li>How does this work?</li>
                        <ul>
                            <li>You'll have to connect your ReMarkable account to Scrybble, and install the Obsidian
                                "Scrybble" plugin which will download the files you selected for synchronisation.
                            </li>
                        </ul>
                        <li>How long does it take to set-up?</li>
                        <ul>
                            <li>You'll be ready to go in less than five minutes</li>
                        </ul>
                        <li>Where can I learn more what scrybble does and doesn't do?</li>
                        <ul>
                            <li>Check out our <Link to="/roadmap">roadmap</Link></li>
                        </ul>
                    </ul>
                </div>
                <div className="images"></div>
            </div>
        </main>
    </div>
}
