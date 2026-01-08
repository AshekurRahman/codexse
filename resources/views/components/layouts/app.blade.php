@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'image' => null,
    'type' => 'website',
    'canonical' => null,
    'robots' => 'index, follow',
    'product' => null,
    'breadcrumbs' => null
])
@php
    use App\Models\SeoSetting;

    $seoTitle = $title ?? SeoSetting::get('default_meta_title', config('app.name', 'Codexse') . ' - Premium Digital Marketplace');
    $seoDescription = $description ?? SeoSetting::get('default_meta_description', 'Discover premium digital products, professional services, and talented freelancers.');
    $seoKeywords = $keywords ?? SeoSetting::get('default_meta_keywords', '');
    $siteName = SeoSetting::get('site_name', config('app.name', 'Codexse'));

    $ogImage = $image;
    if (!$ogImage) {
        $defaultOgImage = SeoSetting::get('og_default_image');
        $ogImage = $defaultOgImage ? asset('storage/' . $defaultOgImage) : asset('images/og-default.jpg');
    }

    $twitterSite = SeoSetting::get('twitter_site');
    $twitterCreator = SeoSetting::get('twitter_creator');
    $facebookAppId = SeoSetting::get('facebook_app_id');
    $googleVerification = SeoSetting::get('google_site_verification');
    $bingVerification = SeoSetting::get('bing_site_verification');

    $orgName = SeoSetting::get('organization_name', $siteName);
    $orgLogo = SeoSetting::get('organization_logo');
    $orgUrl = SeoSetting::get('organization_url', url('/'));
    $orgEmail = SeoSetting::get('organization_email');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-init="$store.theme.init()">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Primary Meta Tags -->
        <title>{{ $seoTitle }}</title>
        <meta name="title" content="{{ $seoTitle }}">
        <meta name="description" content="{{ $seoDescription }}">
        @if($seoKeywords)
        <meta name="keywords" content="{{ $seoKeywords }}">
        @endif
        <meta name="robots" content="{{ $robots }}">
        <meta name="author" content="{{ $siteName }}">

        <!-- Canonical URL -->
        <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        <meta name="theme-color" content="#6366f1">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="{{ $type }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ $seoTitle }}">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="og:site_name" content="{{ $siteName }}">
        <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
        @if($facebookAppId)
        <meta property="fb:app_id" content="{{ $facebookAppId }}">
        @endif

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="{{ $seoTitle }}">
        <meta name="twitter:description" content="{{ $seoDescription }}">
        <meta name="twitter:image" content="{{ $ogImage }}">
        @if($twitterSite)
        <meta name="twitter:site" content="@{{ ltrim($twitterSite, '@') }}">
        @endif
        @if($twitterCreator)
        <meta name="twitter:creator" content="@{{ ltrim($twitterCreator, '@') }}">
        @endif

        <!-- Search Engine Verification -->
        @if($googleVerification)
        <meta name="google-site-verification" content="{{ $googleVerification }}">
        @endif
        @if($bingVerification)
        <meta name="msvalidate.01" content="{{ $bingVerification }}">
        @endif

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

        <!-- Structured Data - Organization -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "{{ $orgName }}",
            "url": "{{ $orgUrl }}",
            @if($orgLogo)
            "logo": "{{ str_starts_with($orgLogo, 'http') ? $orgLogo : asset('storage/' . $orgLogo) }}",
            @else
            "logo": "{{ asset('images/logo.png') }}",
            @endif
            @if($orgEmail)
            "email": "{{ $orgEmail }}",
            @endif
            "description": "{{ $seoDescription }}",
            "sameAs": []
        }
        </script>

        <!-- Structured Data - Website with Search -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "name": "{{ $siteName }}",
            "url": "{{ url('/') }}",
            "potentialAction": {
                "@type": "SearchAction",
                "target": "{{ url('/products') }}?search={search_term_string}",
                "query-input": "required name=search_term_string"
            }
        }
        </script>

        @if($product)
        <!-- Structured Data - Product -->
        <x-schema-markup type="product" :data="['product' => $product]" />
        @endif

        @if($breadcrumbs)
        <!-- Structured Data - Breadcrumb -->
        <x-schema-markup type="breadcrumb" :data="['items' => $breadcrumbs]" />
        @endif

        @stack('head')

        {{-- Custom Head Code (from admin settings) --}}
        {!! \App\Models\Setting::get('custom_head_code', '') !!}
    </head>
    <body class="font-sans antialiased bg-surface-50 text-surface-900 dark:bg-surface-900 dark:text-surface-50" data-authenticated="{{ auth()->check() ? 'true' : 'false' }}">
        <!-- Skip to Content Link (Accessibility) -->
        <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[100] focus:px-4 focus:py-2 focus:bg-primary-600 focus:text-white focus:rounded-lg focus:outline-none">
            Skip to main content
        </a>

        <div class="min-h-screen flex flex-col">
            <!-- Navigation -->
            <x-navbar />

            <!-- Page Content -->
            <main id="main-content" class="flex-1" role="main">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <x-footer />
        </div>

        <!-- Quick View Modal -->
        <x-quick-view-modal />

        <!-- AI Chatbot Widget -->
        @if(\App\Models\Setting::get('chatbot_enabled', false))
            <x-chatbot-widget />
        @endif

        <!-- Product Compare Bar -->
        <x-compare-bar />

        <!-- Live Chat Support Widget -->
        @if(\App\Models\Setting::get('live_chat_enabled', true))
            <x-live-chat-widget />
        @endif

        <!-- Push Notifications Script -->
        @auth
        <script>
            window.VAPID_PUBLIC_KEY = '{{ config('services.webpush.public_key') }}';
            window.APP_URL = '{{ url('/') }}';
        </script>
        <script src="{{ asset('js/push-notifications.js') }}" defer></script>
        @endauth

        @stack('scripts')

        {{-- Custom Body Code (from admin settings) --}}
        {!! \App\Models\Setting::get('custom_body_code', '') !!}
    </body>
</html>
