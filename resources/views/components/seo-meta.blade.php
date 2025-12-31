{{-- Basic Meta Tags --}}
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
@if($keywords)
<meta name="keywords" content="{{ $keywords }}">
@endif
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

{{-- Open Graph / Facebook --}}
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
@if($ogImage)
<meta property="og:image" content="{{ $ogImage }}">
@endif
<meta property="og:site_name" content="{{ \App\Models\SeoSetting::get('site_name', config('app.name')) }}">
@if($facebookAppId = $getFacebookAppId())
<meta property="fb:app_id" content="{{ $facebookAppId }}">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="{{ $twitterCard }}">
@if($twitterSite = $getTwitterSite())
<meta name="twitter:site" content="{{ $twitterSite }}">
@endif
@if($twitterCreator = $getTwitterCreator())
<meta name="twitter:creator" content="{{ $twitterCreator }}">
@endif
<meta name="twitter:title" content="{{ $twitterTitle }}">
<meta name="twitter:description" content="{{ $twitterDescription }}">
@if($twitterImage)
<meta name="twitter:image" content="{{ $twitterImage }}">
@endif

{{-- Search Engine Verification --}}
@if($googleVerification = $getGoogleVerification())
<meta name="google-site-verification" content="{{ $googleVerification }}">
@endif
@if($bingVerification = $getBingVerification())
<meta name="msvalidate.01" content="{{ $bingVerification }}">
@endif
