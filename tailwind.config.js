import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import preset from './vendor/filament/support/tailwind.config.preset';
import fs from 'fs';
import path from 'path';
import colors from 'tailwindcss/colors';

const themeFilePath = path.resolve(__dirname, 'theme.json');
const activeTheme = fs.existsSync(themeFilePath) ? JSON.parse(fs.readFileSync(themeFilePath, 'utf8')).name : 'anchor';

/** @type {import('tailwindcss').Config} */
export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/components/**/*.blade.php',
        './resources/views/components/blade.php',
        './wave/resources/views/**/*.blade.php',
        './resources/themes/' + activeTheme + '/**/*.blade.php',
        './resources/plugins/**/*.php',
        './config/*.php',
        // Include Vue/JS files so Tailwind picks up classes in components
        './resources/js/**/*.{vue,js,jsx,ts,tsx}',
        './resources/themes/' + activeTheme + '/assets/js/**/*.js'
    ],

    safelist: [
        // Ensure brand utility classes exist even if generated dynamically
        { pattern: /^(bg|text|border|ring|fill|stroke|from|to|via|placeholder)-primary-(50|100|200|300|400|500|600|700|800|900)$/ },
        { pattern: /^(bg|text|border|ring|fill|stroke|placeholder)-(neutral|success|warning|danger|info)-(50|100|200|300|400|500|600|700|800|900)$/ },
        'ring-offset-2',
        'ring-offset-4',
    ],

    theme: {
        extend: {
            colors: {
                // Brand palettes
                primary: colors.indigo,
                neutral: colors.zinc,
                success: colors.emerald,
                warning: colors.amber,
                danger: colors.red,
                info: colors.sky,
            },
            animation: {
                'marquee': 'marquee 25s linear infinite',
            },
            keyframes: {
                'marquee': {
                    from: { transform: 'translateX(0)' },
                    to: { transform: 'translateX(-100%)' },
                }
            } 
        },
    },

    plugins: [forms, typography],
};
