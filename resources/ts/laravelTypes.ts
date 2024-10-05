export interface ErrorResponse {
  data: {
    message: string
    errors: {
      [k: string]: ReadonlyArray<string>
    }
  }
}
