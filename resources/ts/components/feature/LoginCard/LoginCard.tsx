import React, {useEffect} from "react";
import {LoginData, useLogin, useLoginMutation} from "../../../store/api/apiRoot";

export default function () {
    const [login, {isSuccess}] = useLoginMutation();
    const authenticateUser = useLogin();

    useEffect(() => {
        if (isSuccess) {
            authenticateUser();
        }
    }, [isSuccess]);

    return <div className="card-dark">
        <div className="card-header">Login</div>
        <form className="card-body" onSubmit={(e) => {
            e.preventDefault();
            const formData = new FormData(e.currentTarget) as unknown as LoginData;
            login(formData);
        }}>
            <div className="row mb-3">
                <label htmlFor="email"
                       className="col-md-4 col-form-label text-md-end">Email address</label>

                <div className="col-md-6">
                    <input id="email" type="email" className="form-control"
                           name="email" required autoComplete="email" autoFocus/>
                </div>
            </div>

            <div className="row mb-3">
                <label htmlFor="password"
                       className="col-md-4 col-form-label text-md-end">Password</label>

                <div className="col-md-6">
                    <input id="password" type="password"
                           className="form-control" name="password"
                           required autoComplete="current-password"/>
                </div>
            </div>

            <div className="row mb-3">
                <div className="col-md-6 offset-md-4">
                    <div className="form-check">
                        <input className="form-check-input" type="checkbox" name="remember"
                               id="remember"/>

                        <label className="form-check-label" htmlFor="remember">
                            Remember me
                        </label>
                    </div>
                </div>
            </div>

            <div className="row mb-0">
                <div className="col-md-8 offset-md-4">
                    <button type="submit" className="btn btn-primary">Login</button>
                    {/*<a className="btn btn-link" href="{{ route('password.request') }}">Forgot your password?</a>*/}
                </div>
            </div>
        </form>
    </div>
}
