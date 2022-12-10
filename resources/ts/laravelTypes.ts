export interface ErrorResponse {
    message: string,
    errors: {
        [k: string]: ReadonlyArray<string>
    }
}
