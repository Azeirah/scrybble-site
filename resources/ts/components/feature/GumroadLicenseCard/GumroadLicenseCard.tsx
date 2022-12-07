import React from "react";
import "./GumroadLicenseCard.scss"

export default function GumroadLicenseCard() {
    return <div id="login-card" className="card-dark">
        <div className="card-header">
            <span className="fs-4">Connect your gumroad license<span
                className="fs-5 text-muted"> (step 1/2)</span></span>
        </div>
        <form className="card-body">
            <div className="input-group">
                <input type="text" className="form-control input-group-text" required name="license"
                       placeholder="Your license"/>
                <button className="btn btn-primary" type="submit">Submit</button>
            </div>
        </form>
    </div>
}
