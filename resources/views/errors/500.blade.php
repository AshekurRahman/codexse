<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Server Error - Codexse</title>
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
                <div class="absolute inset-0 bg-gradient-to-br from-danger-500/20 to-rose-500/20 rounded-full blur-2xl"></div>
                <div class="relative flex items-center justify-center w-full h-full">
                    <svg class="w-32 h-32 text-danger-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <p class="text-sm font-semibold text-danger-600 dark:text-danger-400 uppercase tracking-wide">500 Error</p>

            <!-- Title -->
            <h1 class="mt-2 text-4xl font-bold tracking-tight text-surface-900 dark:text-white sm:text-5xl">
                Server Error
            </h1>

            <!-- Description -->
            <p class="mt-4 text-lg text-surface-600 dark:text-surface-400 max-w-md mx-auto">
                Something went wrong on our end. We're working to fix this. Please try again later.
            </p>

            <!-- Actions -->
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors shadow-lg shadow-primary-500/25">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Go Home
                </a>
                <button onclick="location.reload()" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-white dark:bg-surface-800 text-surface-700 dark:text-surface-300 font-semibold border-2 border-surface-200 dark:border-surface-700 hover:border-surface-300 dark:hover:border-surface-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Try Again
                </button>
            </div>

            <!-- Support -->
            <div class="mt-12 border-t border-surface-200 dark:border-surface-700 pt-8">
                <p class="text-sm text-surface-500 dark:text-surface-400">
                    If the problem persists, please <a href="{{ url('/contact') }}" class="text-primary-600 dark:text-primary-400 hover:underline">contact support</a>.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
