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
                sans: ['Manrope', ...defaultTheme.fontFamily.sans],
                heading: ['"Inter Tight"', ...defaultTheme.fontFamily.sans],
                display: ['Newsreader', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                paper: 'var(--paper, #f7f7f5)',
                ink: 'var(--ink, #111111)',
                muted: 'var(--muted, #777773)',
                line: 'var(--line, rgba(17, 17, 17, 0.14))',
                'on-ink': 'var(--on-ink, #ffffff)',
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
