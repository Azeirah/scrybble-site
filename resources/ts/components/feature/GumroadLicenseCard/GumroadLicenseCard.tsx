import React, {useState} from "react"
import "./GumroadLicenseCard.scss"
import {useSendGumroadLicenseMutation} from "../../../store/api/apiRoot"
import FormError from "../../reusable/FormError/FormError"

export default function GumroadLicenseCard() {
    const [sendGumroadLicense, {isSuccess, error}] = useSendGumroadLicenseMutation()
    const [license, setLicense] = useState("")

    let errMsg
    if (error && "data" in error) {
        // @ts-ignore
        errMsg = error.data?.error
    }

    return <div id="login-card" className="card-dark">
        <div className="card-header">
            <span className="fs-4">Connect your gumroad license<span
                className="fs-5 text-muted"> (step 1/2)</span></span>
        </div>
        <form className="card-body" onSubmit={(e) => {
            e.preventDefault()
            sendGumroadLicense(license)
        }}>
            <div className="input-group">
                <input type="text" className={`form-control input-group-text${errMsg ? " is-invalid" : ""}`} required
                       name="license"
                       placeholder="Your license" value={license} onChange={(e) => setLicense(e.currentTarget.value)}/>
                <button className="btn btn-primary" type="submit">Submit</button>
                {errMsg ? <FormError errorMessage={errMsg}/> : null}
            </div>
        </form>
    </div>
}
