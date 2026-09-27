import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Instrument Sans', ...defaultTheme.fontFamily.sans],
                serif: ['Instrument Serif', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                public: {
                    ink: '#101114',
                    paper: '#ffffff',
                    bone: '#f1f4f7',
                    mist: '#e8edf2',
                    line: '#d7dde4',
                    muted: '#5f6875',
                },
            }
        },
    },

    plugins: [forms],
};
