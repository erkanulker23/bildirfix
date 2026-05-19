<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @php
        $seoParams = isset($seo) && is_array($seo) ? $seo : [];
        $structuredLd = isset($structuredData) ? array_values(array_filter((array) $structuredData)) : [];
        $seoDescription = isset($seoParams['description']) && $seoParams['description'] !== ''
            ? (string) $seoParams['description']
            : (string) config('seo.default_meta_description');
        if (trim($seoDescription) === '') {
            $seoDescription = 'Şehir şikâyetlerini listeleyin, destek olun ve süreci görünür kılın. Vatandaşların yerel sorun bildirimleri ve kurumsal süreçler için simdibildir.com platformu.';
        }
        $seoCanonical = isset($seoParams['canonical']) && $seoParams['canonical'] !== ''
            ? (string) $seoParams['canonical']
            : request()->fullUrl();
        $robotsDirective = isset($seoParams['robots']) && is_string($seoParams['robots']) && $seoParams['robots'] !== ''
            ? $seoParams['robots']
            : config('seo.default_robots');
        $ogTitleFallback = strip_tags(trim($__env->yieldContent('title'))) ?: config('app.name');
        $ogTitle = isset($seoParams['og_title']) ? (string) $seoParams['og_title'] : $ogTitleFallback;
        $ogType = isset($seoParams['og_type']) ? (string) $seoParams['og_type'] : 'website';
        $seoOgImage = isset($seoParams['og_image']) && is_string($seoParams['og_image']) && trim($seoParams['og_image']) !== ''
            ? trim($seoParams['og_image'])
            : trim((string) config('seo.og_image'));
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name').' • '.__('Şehir şikâyetleri'))</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit($seoDescription, 320) }}">
    <meta name="robots" content="{{ $robotsDirective }}">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <link rel="canonical" href="{{ $seoCanonical }}">
    <meta name="theme-color" content="#FF5A1F">
    @php
        $siteBranding = $siteBranding ?? \App\Support\SiteBranding::fromPlatform();
        $faviconUrl = $siteBranding->faviconUrl();
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}" type="{{ str_ends_with($faviconUrl, '.svg') ? 'image/svg+xml' : 'image/png' }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    @include('partials.site-integrations-head')

    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ \Illuminate\Support\Str::limit(strip_tags($ogTitle), 70) }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($seoDescription), 200) }}">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:locale" content="{{ config('seo.locale_og') }}">
    @if ($seoOgImage !== '')
        <meta property="og:image" content="{{ $seoOgImage }}">
    @endif

    <meta name="twitter:card" content="{{ config('seo.twitter_card') }}">
    <meta name="twitter:title" content="{{ \Illuminate\Support\Str::limit(strip_tags($ogTitle), 70) }}">
    <meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($seoDescription), 200) }}">
    @if ($seoOgImage !== '')
        <meta name="twitter:image" content="{{ $seoOgImage }}">
    @endif

    @foreach ((array) config('seo.preconnect_hints') as $hintHost)
        @continue(! is_string($hintHost) || $hintHost === '')
        <link rel="dns-prefetch" href="{{ $hintHost }}">
        <link rel="preconnect" href="{{ $hintHost }}" crossorigin>
    @endforeach

    <script type="application/ld+json">
        {!! \Illuminate\Support\Js::from(App\Support\Seo::organizationStructuredData()) !!}
    </script>
    @foreach ($structuredLd as $graph)
        <script type="application/ld+json">
            {!! \Illuminate\Support\Js::from($graph) !!}
        </script>
    @endforeach

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (config('adsense.enabled') && filled(config('adsense.client')))
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ config('adsense.client') }}"
            crossorigin="anonymous"></script>
    @endif
</head>

<body class="relative min-h-screen bg-[#F9FAFB] font-sans text-gray-700 antialiased">
    @php
        $complaintWriteHref = route('posts.create');
    @endphp
    @if (empty($minimalChrome ?? false))
        @include('partials.app-header')
    @endif

    @if (empty($minimalChrome ?? false) && ! empty($pageHero) && empty($hidePageHero ?? false))
        <div class="home-fluid relative z-0">
            <x-page-hero
                :overline="$pageHero['overline'] ?? null"
                :title="$pageHero['title'] ?? ''"
                :title-accent="$pageHero['titleAccent'] ?? null"
                :description="$pageHero['description'] ?? null"
            />
        </div>
    @endif

    <div class="min-h-screen overflow-x-hidden">
    <main id="icerik"
        @class([
            'animate-page-in',
            'mx-auto max-w-[1200px] px-4 py-4 sm:py-5' => empty($minimalChrome ?? false) && ! request()->routeIs('home'),
            'mx-auto w-full max-w-none px-0 py-0' => empty($minimalChrome ?? false) && request()->routeIs('home'),
            'mx-auto w-full max-w-none px-0 py-4 sm:py-5' => ! empty($minimalChrome ?? false),
        ])
    >
        @if (session('status'))
            <p role="status" class="mb-4 rounded-ds-md border border-success/20 bg-success-light px-4 py-3 text-sm font-medium text-gray-900 shadow-sm">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-950 shadow-sm" role="alert">
                <ul class="list-inside list-disc space-y-1 font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    @if (empty($minimalChrome ?? false) || ! empty($showSiteFooter ?? false))
        @include('partials.site-footer')
    @endif

    @if (empty($minimalChrome ?? false))
        @include('partials.mobile-report-fab')
    @endif
    </div>

    @php
        $siteBodyIntegrations = $siteIntegrations ?? \App\Support\SiteIntegrations::fromPlatform();
    @endphp
    @if (filled($siteBodyIntegrations->customBodyHtml))
        {!! $siteBodyIntegrations->customBodyHtml !!}
    @endif
    @stack('scripts')
</body>

</html>
