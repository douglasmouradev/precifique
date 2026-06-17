import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    DEFAULT: '#00C896',
                    dark: '#00A67D',
                    light: '#E6FBF5',
                    glow: '#00E6B0',
                },
                ink: '#0D0D0D',
                midnight: {
                    DEFAULT: '#0A0A0A',
                    soft: '#141414',
                },
                paper: '#F8FAFC',
                surface: '#FFFFFF',
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                display: ['"Instrument Sans"', '"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                card: '0 1px 2px 0 rgb(15 23 42 / 0.04), 0 0 0 1px rgb(15 23 42 / 0.03)',
                'card-hover': '0 12px 40px -16px rgb(15 23 42 / 0.14), 0 0 0 1px rgb(15 23 42 / 0.04)',
                elevated: '0 24px 64px -28px rgb(15 23 42 / 0.16), 0 0 0 1px rgb(15 23 42 / 0.04)',
                'premium-glow': '0 0 0 1px rgb(0 200 150 / 0.14), 0 20px 50px -24px rgb(0 166 125 / 0.28)',
                'brand-glow': '0 0 0 1px rgb(0 200 150 / 0.2), 0 12px 32px -12px rgb(0 166 125 / 0.45)',
                sidebar: '4px 0 24px -8px rgb(0 0 0 / 0.35), 1px 0 0 0 rgb(255 255 255 / 0.06)',
                'inner-soft': 'inset 0 1px 0 0 rgb(255 255 255 / 0.06)',
            },
            borderRadius: {
                '2xl': '1rem',
                '3xl': '1.25rem',
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-out forwards',
                'slide-up': 'slideUp 0.4s ease-out forwards',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },

    plugins: [forms],
};
