import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/login.css',
                'resources/js/login.js',
                'resources/js/petugas.js',
                'resources/css/admin.css',
                'resources/js/admin.js',
                'resources/js/facilities.js',
                'resources/js/inspections.js',
                'resources/js/history.js',
                'resources/js/laporan.js',
                'resources/js/admin-list.js',
                'resources/js/petugas-tugas.js',
                'resources/css/utilities.css'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});

