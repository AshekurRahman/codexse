<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Codexse') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-6 sm:py-0 bg-surface-50 dark:bg-surface-900">
            <!-- Logo -->
            <div class="mb-6 sm:mb-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="Codexse" class="h-10 w-10">
                    <span class="text-2xl font-bold text-surface-900 dark:text-white">Codexse</span>
                </a>
            </div>

            <!-- Card -->
            <div class="w-full sm:max-w-md px-5 sm:px-6 py-6 sm:py-8 bg-white dark:bg-surface-800 shadow-xl shadow-surface-200/50 dark:shadow-none overflow-hidden rounded-2xl border border-surface-200 dark:border-surface-700">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="mt-6 sm:mt-8 pb-4 text-center text-sm text-surface-500 dark:text-surface-400">
                <p>&copy; {{ date('Y') }} Codexse. All rights reserved.</p>
            </div>
        </div>
    </body>
</html>
