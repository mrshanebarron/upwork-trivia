import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    safelist: [
        'from-forest',
        'from-grass',
        'from-sky',
        'from-bark',
        'to-forest',
        'to-grass',
        'to-sky',
        'to-bark',
        'bg-gradient-to-br',
        'text-white',
        'bg-blue-500',
        'bg-green-500',
        'rounded',
        'p-4',
        'font-bold',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'forest': {
                    DEFAULT: '#1E6F4D', // Deep Green (Primary)
                    50: '#E8F5F0',
                    100: '#D1EBE1',
                    200: '#A3D7C3',
                    300: '#75C3A5',
                    400: '#47AF87',
                    500: '#1E6F4D',
                    600: '#18593E',
                    700: '#12432E',
                    800: '#0C2D1F',
                    900: '#06170F',
                },
                'grass': {
                    DEFAULT: '#2FBF71', // Grass (CTA)
                    50: '#EAFBF2',
                    100: '#D5F7E5',
                    200: '#ABF0CB',
                    300: '#81E8B1',
                    400: '#57E097',
                    500: '#2FBF71',
                    600: '#25995A',
                    700: '#1C7344',
                    800: '#134D2D',
                    900: '#092617',
                },
                'sky': {
                    DEFAULT: '#89C2FF', // Sky (Support)
                    50: '#F0F7FF',
                    100: '#E1EFFF',
                    200: '#C3DFFF',
                    300: '#A5CFFF',
                    400: '#89C2FF',
                    500: '#5BA8FF',
                    600: '#2D8EFF',
                    700: '#0070F3',
                    800: '#0054B8',
                    900: '#00387D',
                },
                'bark': {
                    DEFAULT: '#6B4F3B', // Bark (Secondary)
                    50: '#F3F0ED',
                    100: '#E7E1DB',
                    200: '#CFC3B7',
                    300: '#B7A593',
                    400: '#9F876F',
                    500: '#6B4F3B',
                    600: '#563F2F',
                    700: '#402F23',
                    800: '#2B2018',
                    900: '#15100C',
                },
                'bone': '#F6F2E8', // Background
            },
        },
    },

    plugins: [forms],
};
