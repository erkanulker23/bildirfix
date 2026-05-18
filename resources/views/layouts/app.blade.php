<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @php
        $seoParams = isset($seo) && is_array($seo) ? $seo : [];
        $structuredLd = isset($structuredData) ? array_values(array_filter((array) $structuredData)) : [];
        $seoDescription = isset($seoParams['description']) && $seoParams['description'] !== ''
            ? (string) $seoParams['description']
            : config('seo.default_meta_description');
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
</head>

<body class="relative min-h-screen bg-[#F9FAFB] font-sans text-gray-700 antialiased">
    @php
        $complaintWriteHref = route('posts.create');
    @endphp
    @if (empty($minimalChrome ?? false))
    <header
        class="sticky top-0 z-50 safe-top border-b border-neutral-200 bg-white/95 shadow-sm backdrop-blur-md supports-[backdrop-filter]:bg-white/90">
        <div class="relative mx-auto max-w-[1200px] px-3 sm:px-5">
            <div class="flex min-h-14 items-center gap-2 py-1 sm:gap-3">
                <a href="{{ route('home') }}"
                    class="flex min-w-0 shrink-0 items-center gap-2 tracking-tight text-neutral-900">
                    <span
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary text-white shadow-sm ring-2 ring-white"
                        aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 13 9 17 19 7" />
                        </svg>
                    </span>
                    <span class="font-heading truncate text-[1.05rem] font-extrabold leading-none tracking-tight sm:text-[1.125rem]">{{ config('app.name') }}</span>
                </a>

                <nav
                    class="flex min-w-0 flex-1 items-center gap-4 overflow-x-auto whitespace-nowrap py-1 pr-1 text-[13px] font-semibold text-neutral-700 [scrollbar-width:none] md:justify-center md:gap-6 md:py-0 [&::-webkit-scrollbar]:hidden"
                    aria-label="{{ __('Ana menü') }}">
                    <a href="{{ route('cities.explore') }}"
                        class="shrink-0 rounded-md px-0.5 py-1 transition hover:text-primary">{{ __('Şehrini keşfet') }}</a>
                    <a href="{{ route('campaigns.index') }}"
                        class="shrink-0 rounded-md px-0.5 py-1 transition hover:text-primary">{{ __('Kampanyalar') }}</a>
                    <a href="{{ route('feed.index') }}"
                        class="shrink-0 rounded-md px-0.5 py-1 transition hover:text-primary">{{ __('Akış') }}</a>
                    @auth
                        <a href="{{ route('notifications.index') }}"
                            class="shrink-0 rounded-md px-0.5 py-1 transition hover:text-primary md:hidden">{{ __('Bildirimler') }}</a>
                        <a href="{{ route('profile') }}"
                            class="shrink-0 rounded-md px-0.5 py-1 transition hover:text-primary">{{ __('Profil') }}</a>
                    @endauth
                </nav>

                <div class="flex shrink-0 items-center gap-1 sm:gap-2">
                    @auth
                        <a href="{{ route('notifications.index') }}"
                            class="relative hidden h-10 w-10 items-center justify-center rounded-full text-neutral-500 transition hover:bg-neutral-100 md:inline-flex"
                            title="{{ __('Bildirimler') }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @php
                                $navUnread = auth()->user()->unreadNotifications()->count();
                            @endphp
                            @if ($navUnread > 0)
                                <span class="absolute right-2 top-2 block h-2 w-2 rounded-full bg-danger ring-2 ring-white"></span>
                                <span class="sr-only">{{ __('Okunmamış bildirimler') }}</span>
                            @endif
                        </a>
                        @php
                            $__u = auth()->user();
                            preg_match_all('/\p{L}/u', (string) $__u->name, $m);
                            $nmShort = (($m[0] ?? []) !== [])
                                ? mb_strtoupper(implode('', array_slice($m[0], 0, 2)))
                                : mb_substr((string) $__u->name, 0, 2);
                            $nmShort = mb_substr(mb_strtoupper($nmShort ?: '?'), 0, 2);
                        @endphp
                        <a href="{{ $__u->canAccessAdminPanel() ? route('admin.dashboard') : route('panel.dashboard') }}"
                            class="hidden items-center gap-2 rounded-full border border-gray-200 bg-gray-50 py-1 pl-1 pr-2.5 text-xs font-semibold text-gray-900 shadow-sm transition hover:border-gray-300 sm:inline-flex">
                            <span
                                class="font-heading flex h-8 w-8 items-center justify-center rounded-full bg-secondary-soft text-[11px] font-bold text-white shadow-inner">{{ $nmShort }}</span>
                            <span class="hidden max-w-[5.5rem] truncate lg:inline">{{ \Illuminate\Support\Str::limit($__u->name, 14) }}</span>
                            <svg class="hidden h-3.5 w-3.5 text-gray-400 lg:inline" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="hidden min-h-11 min-w-11 items-center justify-center rounded-full px-2.5 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 sm:inline-flex">{{ __('Giriş') }}</a>
                        <a href="{{ route('register') }}"
                            class="hidden min-h-11 min-w-11 items-center justify-center rounded-full px-2.5 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 md:inline-flex">{{ __('Üye ol') }}</a>
                    @endauth
                    <a href="{{ $complaintWriteHref }}"
                        class="btn-primary inline-flex shrink-0 !px-2.5 !py-2 text-[13px] ring-2 ring-white sm:!px-3.5">
                        <span class="text-base leading-none sm:mr-1" aria-hidden="true">+</span>
                        <span class="hidden sm:inline">{{ __('Kent sorunu bildir') }}</span>
                        <span class="sm:hidden">{{ __('Bildir') }}</span>
                    </a>
                    @auth
                        <div class="hidden items-center gap-1 border-l border-neutral-200 pl-2 sm:flex">
                            @if (auth()->user()->canAccessAdminPanel())
                                <a href="{{ route('admin.dashboard') }}"
                                    class="rounded-lg px-2 py-1.5 text-[11px] font-semibold text-secondary hover:bg-primary-light">{{ __('Yönetim') }}</a>
                            @endif
                            @if (auth()->user()->isInstitution())
                                <a href="{{ route('institution.dashboard') }}"
                                    class="rounded-lg px-2 py-1.5 text-[11px] font-semibold text-secondary-soft hover:bg-neutral-100">{{ __('Kurum') }}</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="rounded-lg px-2 py-1.5 text-[11px] font-semibold text-neutral-500 hover:bg-danger-light hover:text-danger">{{ __('Çıkış') }}</button>
                            </form>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>
    @endif

    <div class="min-h-screen overflow-x-hidden">
    <main id="icerik"
        @class([
            'animate-page-in',
            'mx-auto max-w-[1200px] px-4 py-4 sm:py-5' => empty($minimalChrome ?? false) && ! request()->routeIs('home'),
            'mx-auto max-w-[1200px] px-4 py-0 sm:py-0' => empty($minimalChrome ?? false) && request()->routeIs('home'),
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
    <footer class="safe-bottom mt-14 border-t border-gray-200 bg-white py-10 text-sm text-gray-500">
        <div class="mx-auto grid max-w-[1200px] gap-8 px-4 sm:grid-cols-2 sm:gap-10 lg:grid-cols-4">
            <div class="sm:col-span-2 lg:col-span-1">
                <p class="font-heading text-base font-extrabold text-gray-900">{{ config('app.name') }}</p>
                <p class="mt-2 max-w-xs leading-relaxed">{{ __('Kent ve mahalle düzeyinde sorun bildirimi: fotoğraf, konum ve moderasyon ile şeffaf süreç. Resmi kanallar yerine geçmez.') }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">{{ __('Üyelik') }}</p>
                <ul class="mt-3 space-y-2 font-semibold">
                    <li><a href="{{ route('login') }}" class="text-gray-900 hover:text-primary hover:underline">{{ __('Vatandaş girişi') }}</a></li>
                    <li><a href="{{ route('campaigns.index') }}" class="text-gray-900 hover:text-primary hover:underline">{{ __('Sosyal kampanyalar') }}</a></li>
                    <li><a href="{{ route('login.brand') }}" class="text-gray-900 hover:text-primary hover:underline">{{ __('Belediye / kurum (/brand)') }}</a></li>
                </ul>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">{{ __('İletişim ve yasal') }}</p>
                <ul class="mt-3 space-y-2">
                    <li><a href="{{ route('how-it-works') }}" class="font-medium underline-offset-4 hover:text-primary hover:underline">{{ __('Nasıl çalışır?') }}</a></li>
                    <li><a href="{{ route('contact') }}" class="font-medium underline-offset-4 hover:text-primary hover:underline">{{ __('İletişim') }}</a></li>
                    <li><a href="{{ route('legal.privacy') }}" class="font-medium underline-offset-4 hover:text-primary hover:underline">{{ __('Gizlilik') }}</a></li>
                    <li><a href="{{ route('legal.kvkk') }}" class="font-medium underline-offset-4 hover:text-primary hover:underline">{{ __('KVKK') }}</a></li>
                    <li><a href="{{ route('legal.terms') }}" class="font-medium underline-offset-4 hover:text-primary hover:underline">{{ __('Kullanım koşulları') }}</a></li>
                </ul>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">{{ __('Uyarı') }}</p>
                <p class="mt-3 leading-relaxed">{{ __('Resmi başvuru yollarının yerini almaz; yasal haklarınızı etkileyebilecek hususlarda mevzuat ve ilgili kurumları yanınıza alınız.') }}</p>
            </div>
        </div>
    </footer>
    @endif
    </div>

    @stack('scripts')
</body>

</html>
