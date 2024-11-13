import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: ["class"],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './app/Livewire/**/*.php',
        './resources/views/**/*.blade.php',
        './config/support-bubble.php',
        './vendor/spatie/laravel-support-bubble/resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Bricolage Grotesque', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, require('@tailwindcss/typography')],
};
