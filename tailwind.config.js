import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                ocean: {
                    50: '#f0f7ff',
                    100: '#e0efff',
                    200: '#b9dffe',
                    300: '#7cc5fd',
                    400: '#36a9fa',
                    500: '#0c8ce9',
                    600: '#006dc7',
                    700: '#0057a1',
                    800: '#004a85',
                    900: '#003d6e',
                    950: '#00274a',
                },
                sand: {
                    50: '#fdf8f0',
                    100: '#f9eedb',
                    200: '#f2dab5',
                    300: '#e9c086',
                    400: '#dfa055',
                    500: '#d78634',
                    600: '#c96d29',
                },
                seafoam: {
                    50: '#effcf6',
                    100: '#d9f7ea',
                    200: '#b6edd8',
                    300: '#84dfc0',
                    400: '#4ec9a2',
                    500: '#28ae87',
                    600: '#1a8d6e',
                    700: '#15705a',
                    800: '#145948',
                },
            },
            boxShadow: {
                'card': '0 1px 3px 0 rgba(0, 61, 110, 0.06), 0 1px 2px -1px rgba(0, 61, 110, 0.06)',
                'card-hover': '0 10px 25px -5px rgba(0, 61, 110, 0.1), 0 8px 10px -6px rgba(0, 61, 110, 0.06)',
                'nav': '0 4px 20px -2px rgba(0, 61, 110, 0.08)',
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-out',
                'fade-in-up': 'fadeInUp 0.6s ease-out',
                'slide-in-right': 'slideInRight 0.4s ease-out',
                'wave': 'wave 8s ease-in-out infinite',
                'float': 'float 6s ease-in-out infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideInRight: {
                    '0%': { opacity: '0', transform: 'translateX(-10px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                wave: {
                    '0%, 100%': { transform: 'translateX(0) translateY(0)' },
                    '25%': { transform: 'translateX(-5px) translateY(-3px)' },
                    '50%': { transform: 'translateX(0) translateY(-5px)' },
                    '75%': { transform: 'translateX(5px) translateY(-3px)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
            },
        },
    },

    plugins: [forms],
};
