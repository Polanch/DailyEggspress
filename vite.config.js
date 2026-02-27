import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js',
                    'resources/css/style.css', 'resources/js/script.js',
                    'resources/css/admin_style.css', 'resources/js/admin_script.js',
                    'resources/css/welcome_style.css', 'resources/js/welcome_script.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: 'dailyeggspress.local',
        port: 5173,
        strictPort: true,
        allowedHosts: ['dailyeggspress.local'],
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
