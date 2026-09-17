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
                    ink: '#050505',
                    paper: '#ffffff',
                    bone: '#E5E5E5',
                    mist: '#ece9e3',
                    line: '#d8d4cc',
                    muted: '#6f6a61',
                },
            }
        },
    },

    plugins: [forms],
};
