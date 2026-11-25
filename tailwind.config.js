import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

// Tonos 50...950 calculados a partir del color de marca (variables --brand-50 ... --brand-950)
const brandScale = Object.fromEntries(
    [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950].map((shade) => [
        shade,
        `rgb(var(--brand-${shade}) / <alpha-value>)`,
    ]),
);

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
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                // Tipografía elegante para el nombre del casino y los títulos grandes
                display: ['"Playfair Display"', 'Georgia', 'serif'],
            },
            colors: {
                // Color de marca: lo define la Identidad corporativa (variables CSS)
                brand: {
                    DEFAULT: 'rgb(var(--brand) / <alpha-value>)',
                    dark: 'rgb(var(--brand-dark) / <alpha-value>)',
                    light: 'rgb(var(--brand-light) / <alpha-value>)',
                    ...brandScale,
                },
                // Colores fijos del estilo casino
                ink: { DEFAULT: '#0b0b0f', soft: '#15151c' },
                gold: { DEFAULT: '#d4af37', light: '#f3e3a0', dark: '#a8841c' },
                felt: { DEFAULT: '#0b5d3b', dark: '#06402a' },
            },
        },
    },

    plugins: [forms],
};