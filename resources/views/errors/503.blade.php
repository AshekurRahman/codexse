<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Under Maintenance - Codexse</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-icon.svg') }}">
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
        @keyframes wrench {
            0%, 100% { transform: rotate(-15deg); }
            50% { transform: rotate(15deg); }
        }
        @keyframes pulse-bg {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 0.8; }
        }
        .float-animation { animation: float 6s ease-in-out infinite; }
        .wrench-animation { animation: wrench 2s ease-in-out infinite; }
        .pulse-bg { animation: pulse-bg 3s ease-in-out infinite; }
    </style>
</head>
<body class="h-full font-sans antialiased bg-gradient-to-br from-surface-50 via-surface-100 to-surface-50 dark:from-surface-900 dark:via-surface-800 dark:to-surface-900 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-cyan-500/10 rounded-full blur-3xl pulse-bg"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl pulse-bg"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-gradient-to-r from-cyan-500/5 to-teal-500/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative min-h-full flex flex-col items-center justify-center px-4 py-16 sm:px-6 lg:px-8">
        <div class="text-center max-w-xl mx-auto">
            <!-- Animated Illustration -->
            <div class="relative mx-auto w-64 h-64 mb-8 float-animation">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="relative">
                        <div class="w-40 h-40 rounded-full bg-gradient-to-br from-cyan-500 to-teal-600 shadow-2xl shadow-cyan-500/30 flex items-center justify-center">
                            <svg class="w-20 h-20 text-white wrench-animation" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                            </svg>
                        </div>
                        <!-- Badge -->
                        <div class="absolute -top-2 -right-2 w-14 h-14 bg-cyan-400 rounded-full shadow-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">503</span>
                        </div>
                        <div class="absolute -bottom-3 -left-3 w-6 h-6 bg-teal-300 dark:bg-teal-700 rounded-full shadow-lg"></div>
                    </div>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-4xl sm:text-5xl font-extrabold text-surface-900 dark:text-white tracking-tight">
                Under Maintenance 🔧
            </h1>

            <!-- Description -->
            <p class="mt-4 text-lg text-surface-600 dark:text-surface-400 leading-relaxed">
                🛠️ We're currently performing scheduled maintenance to improve your experience. We'll be back online shortly!
            </p>

            <!-- Progress indicator -->
            <div class="mt-8 max-w-xs mx-auto">
                <div class="flex items-center justify-between text-sm text-surface-500 dark:text-surface-400 mb-2">
                    <span>Progress</span>
                    <span>Almost there...</span>
                </div>
                <div class="h-2 bg-surface-200 dark:bg-surface-700 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-cyan-500 to-teal-500 rounded-full" style="width: 75%; animation: pulse-bg 2s ease-in-out infinite;"></div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <button onclick="location.reload()" class="group inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl bg-gradient-to-r from-cyan-500 to-teal-600 text-white font-semibold hover:from-cyan-600 hover:to-teal-700 transition-all duration-300 shadow-xl shadow-cyan-500/25 hover:shadow-2xl hover:shadow-cyan-500/30 hover:-translate-y-0.5">
                    <svg class="w-5 h-5 transition-transform group-hover:rotate-180 duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Check Again
                </button>
            </div>

            <!-- Info Box -->
            <div class="mt-12 p-6 rounded-2xl bg-surface-100 dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-surface-900 dark:text-white">What's happening?</h3>
                        <p class="mt-1 text-sm text-surface-600 dark:text-surface-400">
                            We're upgrading our systems to serve you better. This usually takes just a few minutes.
                            Follow us on social media for updates or
                            <a href="{{ url('/contact') }}" class="font-semibold text-cyan-600 dark:text-cyan-400 hover:underline">contact support</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
