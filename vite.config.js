import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // Entry terpisah: Chart.js hanya diunduh di halaman statistik,
                // bukan ikut terbawa ke setiap halaman aplikasi.
                'resources/js/statistik.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        // Peta sumber tidak ikut diterbitkan agar kode asli tidak terbaca
        // dari browser di lingkungan production.
        sourcemap: false,
    },
});
