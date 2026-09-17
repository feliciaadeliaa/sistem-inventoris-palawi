import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

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
            backgroundImage: {
                'auth-gradient': 'linear-gradient(101.66deg, #12324A 9.55%, #204E61 39.35%, #49837A 100.82%)',
            },
            backgroundImage: {
                'auth-gradient': 'linear-gradient(101.66deg, #12324A 9.55%, #204E61 39.35%, #49837A 100.82%)',
                'btn-gradient': 'linear-gradient(276.46deg, #12324A 12.27%, #204E61 35.49%, #49837A 83.4%)',
            },
            colors: {
                brand: {
                    50:  '#EAF6F4',
                    100: '#CFEAE5',
                    200: '#A3D8CF',
                    300: '#6FC2B4',
                    400: '#3DA597',
                    500: '#00838C',
                    600: '#00707A',
                    700: '#00617A',
                    800: '#004E68',
                    900: '#003F5F',
                    950: '#002B3E',
                },
            },
        },
    },

    plugins: [forms],
};