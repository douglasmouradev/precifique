import js from '@eslint/js';
import globals from 'globals';

export default [
    {
        ignores: ['public/build/**', 'vendor/**', 'node_modules/**'],
    },
    js.configs.recommended,
    {
        files: ['resources/js/**/*.js'],
        languageOptions: {
            ecmaVersion: 2022,
            sourceType: 'module',
            globals: {
                ...globals.browser,
                window: 'readonly',
                document: 'readonly',
                localStorage: 'readonly',
                navigator: 'readonly',
                CustomEvent: 'readonly',
                EventSource: 'readonly',
                FormData: 'readonly',
                fetch: 'readonly',
                requestAnimationFrame: 'readonly',
                performance: 'readonly',
            },
        },
        rules: {
            'no-unused-vars': ['warn', { argsIgnorePattern: '^_' }],
            'no-empty': ['error', { allowEmptyCatch: true }],
        },
    },
];
