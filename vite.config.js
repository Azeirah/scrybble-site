import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import ReactPlugin from "@vitejs/plugin-react";
import sentryVitePlugin from "@sentry/vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        sentryVitePlugin({
            org: "streamsoft",
            project: "scrybble-frontend",
            release: "scrybble" + process.env.npm_package_version,

            // Specify the directory containing build artifacts
            include: '.',
            ignore: ['node_modules', 'vite.config.ts'],
            silent: true,
            telemetry: true,
            sourceMapReference: false,
            sourceMaps: {
                include: ['./public/build/assets'],
                ignore: ['node_modules'],
                urlPrefix: '~/assets',
            },
            // Auth tokens can be obtained from https://sentry.io/settings/account/api/auth-tokens/
            // and needs the `project:releases` and `org:read` scopes
            authToken: "b2ebe70ab9894a49b99394b9e15b920b4ef2e14e9d9842919889ee7b1921703b",

            // Optionally uncomment the line below to override automatic release name detection
            // release: process.env.RELEASE,
        }),
        ReactPlugin()
    ],
    resolve: {
        alias: {
            "@": "/resources/ts"
        }
    }
});
