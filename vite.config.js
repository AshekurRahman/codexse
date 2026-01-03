import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // Enable minification with esbuild (faster, built-in)
        minify: 'esbuild',
        // Chunk splitting for better caching
        rollupOptions: {
            output: {
                manualChunks: {
                    // Vendor chunk for Alpine.js and plugins
                    'alpine': ['alpinejs', '@alpinejs/persist', '@alpinejs/collapse'],
                },
            },
        },
        // Set chunk size warning limit
        chunkSizeWarningLimit: 500,
        // Enable source maps for production debugging (optional)
        sourcemap: false,
    },
    // Optimize dependencies
    optimizeDeps: {
        include: ['alpinejs', '@alpinejs/persist', '@alpinejs/collapse'],
    },
});
