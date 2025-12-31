<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Access Denied - Codexse</title>
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
                <div class="absolute inset-0 bg-gradient-to-br from-warning-500/20 to-amber-500/20 rounded-full blur-2xl"></div>
                <div class="relative flex items-center justify-center w-full h-full">
                    <svg class="w-32 h-32 text-warning-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <p class="text-sm font-semibold text-warning-600 dark:text-warning-400 uppercase tracking-wide">403 Error</p>

            <!-- Title -->
            <h1 class="mt-2 text-4xl font-bold tracking-tight text-surface-900 dark:text-white sm:text-5xl">
                Access Denied
            </h1>

            <!-- Description -->
            <p class="mt-4 text-lg text-surface-600 dark:text-surface-400 max-w-md mx-auto">
                You don't have permission to access this page. Please sign in or contact support if you believe this is an error.
            </p>

            <!-- Actions -->
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors shadow-lg shadow-primary-500/25">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Go Home
                </a>
                <a href="{{ url('/login') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-white dark:bg-surface-800 text-surface-700 dark:text-surface-300 font-semibold border-2 border-surface-200 dark:border-surface-700 hover:border-surface-300 dark:hover:border-surface-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Sign In
                </a>
            </div>
        </div>
    </div>
</body>
</html>
