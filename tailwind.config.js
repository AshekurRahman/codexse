import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    // Safelist for dynamic classes
    safelist: [
        // Dynamic status colors - all shades
        { pattern: /^(bg|text|border|ring)-(primary|success|warning|danger|info|accent|surface)-(50|100|200|300|400|500|600|700|800|900|950)$/ },
        { pattern: /^(bg|text|border|ring)-(primary|success|warning|danger|info|accent|surface)-(50|100|200|300|400|500|600|700|800|900|950)$/, variants: ['dark', 'hover', 'focus', 'group-hover'] },
        // Opacity variants
        { pattern: /^(bg|text|border)-(primary|success|warning|danger|info|accent|surface)-(50|100|200|300|400|500|600|700|800|900)\/(10|20|30|40|50|60|70|80|90)$/ },
        { pattern: /^(bg|text|border)-(primary|success|warning|danger|info|accent|surface)-(50|100|200|300|400|500|600|700|800|900)\/(10|20|30|40|50|60|70|80|90)$/, variants: ['dark'] },
        // Gradient colors
        { pattern: /^(from|to|via)-(primary|success|warning|danger|info|accent|surface)-(50|100|200|300|400|500|600|700|800|900)$/ },
        { pattern: /^(from|to|via)-(primary|success|warning|danger|info|accent|surface)-(50|100|200|300|400|500|600|700|800|900)$/, variants: ['dark'] },
        // Shadow variations
        { pattern: /^shadow-(primary|success|warning|danger|info|accent|surface)-(400|500|600)\/[0-9]+$/ },
        // Ring offset
        { pattern: /^ring-offset-(primary|success|warning|danger|info|accent|surface)-(50|100|200|300|400|500|600|700|800|900)$/ },
        // Placeholder colors
        { pattern: /^placeholder-(primary|success|warning|danger|info|accent|surface)-(400|500|600)$/ },
        // Divide colors
        { pattern: /^divide-(primary|success|warning|danger|info|accent|surface)-(100|200|300|700|800)$/ },
        { pattern: /^divide-(primary|success|warning|danger|info|accent|surface)-(100|200|300|700|800)$/, variants: ['dark'] },
        // Standard Tailwind colors for compatibility
        { pattern: /^(bg|text|border|ring)-(red|green|blue|yellow|orange|purple|pink|gray|slate|zinc|neutral|stone)-(50|100|200|300|400|500|600|700|800|900|950)$/ },
        { pattern: /^(bg|text|border|ring)-(red|green|blue|yellow|orange|purple|pink|gray|slate|zinc|neutral|stone)-(50|100|200|300|400|500|600|700|800|900|950)$/, variants: ['dark', 'hover'] },
        // Common utility classes used dynamically
        'translate-y-0',
        '-translate-y-0.5',
        '-translate-y-1',
        'scale-95',
        'scale-100',
        'opacity-0',
        'opacity-100',
        'invisible',
        'visible',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Modern Primary - Indigo/Blue gradient feel
                primary: {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#6366f1',
                    600: '#4f46e5',
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81',
                    950: '#1e1b4b',
                },
                // Accent - Cyan/Teal for highlights
                accent: {
                    50: '#ecfeff',
                    100: '#cffafe',
                    200: '#a5f3fc',
                    300: '#67e8f9',
                    400: '#22d3ee',
                    500: '#06b6d4',
                    600: '#0891b2',
                    700: '#0e7490',
                    800: '#155e75',
                    900: '#164e63',
                    950: '#083344',
                },
                // Surface - Slate for modern neutral feel
                surface: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                    950: '#020617',
                },
                // Success - Emerald
                success: {
                    50: '#ecfdf5',
                    100: '#d1fae5',
                    200: '#a7f3d0',
                    300: '#6ee7b7',
                    400: '#34d399',
                    500: '#10b981',
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                    900: '#064e3b',
                    950: '#022c22',
                },
                // Warning - Amber
                warning: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                    950: '#451a03',
                },
                // Danger - Rose
                danger: {
                    50: '#fff1f2',
                    100: '#ffe4e6',
                    200: '#fecdd3',
                    300: '#fda4af',
                    400: '#fb7185',
                    500: '#f43f5e',
                    600: '#e11d48',
                    700: '#be123c',
                    800: '#9f1239',
                    900: '#881337',
                    950: '#4c0519',
                },
                // Info - Sky Blue
                info: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                    950: '#082f49',
                },
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
                'mesh-gradient': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'aurora': 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 25%, #06b6d4 50%, #10b981 75%, #6366f1 100%)',
            },
            boxShadow: {
                'glow': '0 0 20px rgba(99, 102, 241, 0.3)',
                'glow-lg': '0 0 40px rgba(99, 102, 241, 0.4)',
                'glow-accent': '0 0 20px rgba(6, 182, 212, 0.3)',
            },
            animation: {
                'gradient': 'gradient 8s linear infinite',
                'float': 'float 6s ease-in-out infinite',
                'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
                gradient: {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
            },
            typography: ({ theme }) => ({
                surface: {
                    css: {
                        '--tw-prose-body': theme('colors.surface.700'),
                        '--tw-prose-headings': theme('colors.surface.900'),
                        '--tw-prose-lead': theme('colors.surface.600'),
                        '--tw-prose-links': theme('colors.primary.600'),
                        '--tw-prose-bold': theme('colors.surface.900'),
                        '--tw-prose-counters': theme('colors.surface.500'),
                        '--tw-prose-bullets': theme('colors.surface.300'),
                        '--tw-prose-hr': theme('colors.surface.200'),
                        '--tw-prose-quotes': theme('colors.surface.900'),
                        '--tw-prose-quote-borders': theme('colors.surface.200'),
                        '--tw-prose-captions': theme('colors.surface.500'),
                        '--tw-prose-code': theme('colors.surface.900'),
                        '--tw-prose-pre-code': theme('colors.surface.200'),
                        '--tw-prose-pre-bg': theme('colors.surface.800'),
                        '--tw-prose-th-borders': theme('colors.surface.300'),
                        '--tw-prose-td-borders': theme('colors.surface.200'),
                        '--tw-prose-invert-body': theme('colors.surface.300'),
                        '--tw-prose-invert-headings': theme('colors.white'),
                        '--tw-prose-invert-lead': theme('colors.surface.400'),
                        '--tw-prose-invert-links': theme('colors.primary.400'),
                        '--tw-prose-invert-bold': theme('colors.white'),
                        '--tw-prose-invert-counters': theme('colors.surface.400'),
                        '--tw-prose-invert-bullets': theme('colors.surface.600'),
                        '--tw-prose-invert-hr': theme('colors.surface.700'),
                        '--tw-prose-invert-quotes': theme('colors.surface.100'),
                        '--tw-prose-invert-quote-borders': theme('colors.surface.700'),
                        '--tw-prose-invert-captions': theme('colors.surface.400'),
                        '--tw-prose-invert-code': theme('colors.white'),
                        '--tw-prose-invert-pre-code': theme('colors.surface.300'),
                        '--tw-prose-invert-pre-bg': 'rgb(0 0 0 / 50%)',
                        '--tw-prose-invert-th-borders': theme('colors.surface.600'),
                        '--tw-prose-invert-td-borders': theme('colors.surface.700'),
                    },
                },
            }),
        },
    },

    plugins: [forms, typography],
};
