import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import ReactPlugin from "@vitejs/plugin-react";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        ReactPlugin()
    ],
    resolve: {
        alias: {
            "@": "/resources/ts"
        }
    }
});
