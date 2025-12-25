@props(['title' => config('app.name', 'Codexse')])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-init="$store.theme.init()">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Prevent flash of wrong theme -->
        <script>
            if (localStorage.getItem('codexse_dark_mode') === 'true' ||
                (!localStorage.getItem('codexse_dark_mode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        </script>
    </head>
    <body class="font-sans antialiased bg-surface-50 text-surface-900 dark:bg-surface-900 dark:text-surface-50">
        <div class="min-h-screen flex flex-col">
            <!-- Navigation -->
            <x-navbar />

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <x-footer />
        </div>

        @stack('scripts')
    </body>
</html>
