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
                // 👇 Thay đổi dòng này: Đặt 'Be Vietnam Pro' làm font mặc định (sans)
                sans: ['"Be Vietnam Pro"', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
