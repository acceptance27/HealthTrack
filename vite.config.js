import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        // Tailwind v4 is a Vite plugin. There is no tailwind.config.js any
        // more -- theme values are declared in resources/css/app.css.
        tailwindcss(),
    ],
});
