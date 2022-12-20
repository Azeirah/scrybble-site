import * as React from "react"

export default function PurchasedPage() {
    return <>
        <div className="container">
            <h1>Thank you for your purchase, you can now start using Scrybble.</h1>
            <p>Get started by creating an account</p>
            <a className="btn btn-primary" href="auth/register">Sign up</a>
        </div>
    </>
}
