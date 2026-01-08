<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Page Not Found - Codexse</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('codexse_dark_mode') === 'true' ||
            (!localStorage.getItem('codexse_dark_mode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="h-full font-sans antialiased bg-surface-50 dark:bg-surface-900">
    <div class="min-h-full flex flex-col items-center justify-center px-4 py-16 sm:px-6 lg:px-8">
        <div class="text-center">
            <!-- Illustration -->
            <div class="relative mx-auto w-40 h-40 mb-8">
                <div class="absolute inset-0 bg-gradient-to-br from-primary-500/20 to-indigo-500/20 rounded-full blur-2xl"></div>
                <div class="relative flex items-center justify-center w-full h-full">
                    <svg class="w-32 h-32 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <p class="text-sm font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wide">404 Error</p>

            <!-- Title -->
            <h1 class="mt-2 text-4xl font-bold tracking-tight text-surface-900 dark:text-white sm:text-5xl">
                Page not found
            </h1>

            <!-- Description -->
            <p class="mt-4 text-lg text-surface-600 dark:text-surface-400 max-w-md mx-auto">
                Sorry, we couldn't find the page you're looking for. It may have been moved or deleted.
            </p>

            <!-- Actions -->
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors shadow-lg shadow-primary-500/25">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Go Home
                </a>
                <button onclick="history.back()" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-white dark:bg-surface-800 text-surface-700 dark:text-surface-300 font-semibold border-2 border-surface-200 dark:border-surface-700 hover:border-surface-300 dark:hover:border-surface-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Go Back
                </button>
            </div>

            <!-- Helpful Links -->
            <div class="mt-12 border-t border-surface-200 dark:border-surface-700 pt-8">
                <p class="text-sm text-surface-500 dark:text-surface-400 mb-4">Or check out these popular pages:</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ url('/products') }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:underline">Products</a>
                    <a href="{{ url('/services') }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:underline">Services</a>
                    <a href="{{ url('/sellers') }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:underline">Sellers</a>
                    <a href="{{ url('/contact') }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:underline">Contact</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
