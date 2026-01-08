<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Session Expired - Codexse</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('codexse_dark_mode') === 'true' ||
            (!localStorage.getItem('codexse_dark_mode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes countdown {
            0% { stroke-dashoffset: 0; }
            100% { stroke-dashoffset: 283; }
        }
        .float-animation { animation: float 6s ease-in-out infinite; }
    </style>
</head>
<body class="h-full font-sans antialiased bg-gradient-to-br from-surface-50 via-surface-100 to-surface-50 dark:from-surface-900 dark:via-surface-800 dark:to-surface-900 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-violet-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-500/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-gradient-to-r from-violet-500/5 to-purple-500/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative min-h-full flex flex-col items-center justify-center px-4 py-16 sm:px-6 lg:px-8">
        <div class="text-center max-w-xl mx-auto">
            <!-- Animated Illustration -->
            <div class="relative mx-auto w-64 h-64 mb-8 float-animation">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="relative">
                        <div class="w-40 h-40 rounded-full bg-gradient-to-br from-violet-500 to-purple-600 shadow-2xl shadow-violet-500/30 flex items-center justify-center">
                            <svg class="w-20 h-20 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <!-- Badge -->
                        <div class="absolute -top-2 -right-2 w-14 h-14 bg-violet-400 rounded-full shadow-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">419</span>
                        </div>
                        <div class="absolute -bottom-3 -left-3 w-6 h-6 bg-purple-300 dark:bg-purple-700 rounded-full shadow-lg"></div>
                    </div>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-4xl sm:text-5xl font-extrabold text-surface-900 dark:text-white tracking-tight">
                Session Expired ⏰
            </h1>

            <!-- Description -->
            <p class="mt-4 text-lg text-surface-600 dark:text-surface-400 leading-relaxed">
                ⌛ Your session has expired due to inactivity. Please refresh the page and try again to continue where you left off.
            </p>

            <!-- Actions -->
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <button onclick="location.reload()" class="group inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl bg-gradient-to-r from-violet-500 to-purple-600 text-white font-semibold hover:from-violet-600 hover:to-purple-700 transition-all duration-300 shadow-xl shadow-violet-500/25 hover:shadow-2xl hover:shadow-violet-500/30 hover:-translate-y-0.5">
                    <svg class="w-5 h-5 transition-transform group-hover:rotate-180 duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh Page
                </button>
                <a href="{{ url('/login') }}" class="group inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl bg-white dark:bg-surface-800 text-surface-700 dark:text-surface-300 font-semibold border-2 border-surface-200 dark:border-surface-700 hover:border-violet-300 dark:hover:border-violet-700 hover:text-violet-600 dark:hover:text-violet-400 transition-all duration-300 hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Sign In Again
                </a>
            </div>

            <!-- Info -->
            <div class="mt-12 p-4 rounded-2xl bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800">
                <p class="text-sm text-violet-700 dark:text-violet-300">
                    For your security, sessions expire after a period of inactivity. This helps protect your account.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
