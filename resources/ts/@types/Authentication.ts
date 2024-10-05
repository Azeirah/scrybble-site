export type User = {
    name: string; email: string;
}
export type LoginData = {
    email: string; password: string; remember?: true;
}
export type RequestPasswordResetData = {
    email: string;
}
export type ResetPasswordData = {
    email: string; password: string; password_confirmation: string; token: string;
}
export interface RegisterForm {
    name: string,
    email: string,
    password: string
}
