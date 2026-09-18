import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        // View kini tinggal di dalam modul masing-masing (arsitektur HMVC),
        // jadi pemindaian kelas Tailwind harus ikut ke sana. Tanpa baris ini
        // seluruh kelas di halaman modul akan hilang dari CSS hasil build.
        './app/Modules/**/Views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                display: ['Space Grotesk', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
