const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views//*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Satoshi', 'sans-serif'],
            },
            colors: {
                dark_gray: '#0D0D0D',
                navy_blue: '#131951',
                blue: '#2D5AF7',
                orange: '#F65C02',
                white: '#FFFFFF',
                gray: "#7B7B7B",
                light_gray: "#D3D3D3",
                dark_orange: "#C74D07",
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};