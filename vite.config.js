import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { svelte } from "@sveltejs/vite-plugin-svelte";
import path from "path";
import { glob } from "glob";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/auth.js',
                'resources/js/mingle.svelte.js',
                ...glob.sync("resources/js/components/**/*.svelte"),
            ],
            refresh: true,
        }),
        svelte()
    ],
    resolve: {
        alias: {
          $lib: path.resolve("./resources/js/lib"),
        },
    },
});
