import React from "react"
import {OnetimecodeQuery, useSendOnetimecodeMutation} from "../../../store/api/apiRoot"
import FormError from "../../reusable/FormError/FormError"

export default function RMOneTimeCodeCard() {
    const [sendOnetimecode, {error}] = useSendOnetimecodeMutation()

    let errMsg
    if (error && "data" in error) {
        // @ts-ignore
        errMsg = error.data?.error
    }

    return <div className="card-dark">
        <div className="card-header">
            <span className="fs-4">Connect with ReMarkable <span className="fs-5 text-muted">(step 2/2)</span></span>
        </div>
        <div className="card-body">
            <p>Retrieve your <a target="_blank"
                                href="https://my.remarkable.com/device/desktop/connect">one-time-code</a> and fill it
                in below</p>
            <p><strong>Note:</strong> connecting for the first time may take <em>well over a minute!</em></p>
            <form onSubmit={(e) => {
                e.preventDefault()
                const oneTimeCodeBody = new FormData(e.currentTarget) as unknown as OnetimecodeQuery
                sendOnetimecode(oneTimeCodeBody)
            }}>
                <div className="input-group">
                    <input className={`input-group-text${errMsg ? " is-invalid" : ""}`} required minLength={8}
                           maxLength={8} pattern="[a-z]{8}"
                           placeholder="aabbccdd" name="code" type="text" autoFocus/>
                    <input className="btn btn-primary" type="submit" value="submit"/>
                    {errMsg ? <FormError errorMessage={errMsg}/> : null}
                </div>
            </form>
        </div>
    </div>
}
