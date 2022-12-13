import React from "react";

export default function FormError({errorMessage}: {errorMessage: string}) {
    return <span className="invalid-feedback" role="alert">
        <strong>{errorMessage}</strong>
    </span>
}
