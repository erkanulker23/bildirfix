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
    <meta name="theme-color" content="#059669">

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

<body class="relative min-h-screen overflow-x-hidden bg-[#f8f9fb] font-sans text-neutral-800 antialiased">
    @stack('prepend_header')
    @php
        $complaintWriteHref = route('complaints.quick.create');
    @endphp
    <header
        class="sticky top-0 z-50 border-b border-neutral-200 bg-white shadow-[0_1px_0_rgba(15,23,42,0.04)] backdrop-blur supports-[backdrop-filter]:bg-white/95">
        <div class="relative mx-auto max-w-[1250px] px-3 sm:px-4">
            <div class="flex items-center justify-between gap-3 py-2.5 sm:gap-4 sm:py-3">
                <a href="{{ route('home') }}"
                    class="flex min-w-0 shrink items-center gap-2 font-black tracking-tight text-neutral-900">
                    <span
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white shadow-[0_2px_8px_rgba(16,185,129,.35)]"
                        aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 13 9 17 19 7" />
                        </svg>
                    </span>
                    <span class="truncate text-[1.05rem] leading-none sm:text-[1.12rem]">{{ \Illuminate\Support\Str::lower(config('app.name')) }}</span>
                </a>

                <nav class="pointer-events-none absolute left-1/2 top-1/2 hidden -translate-x-1/2 -translate-y-1/2 items-center gap-8 text-[14px] font-semibold text-neutral-600 xl:flex xl:pointer-events-auto"
                    aria-label="{{ __('Ana menü') }}">
                    <a href="{{ route('home') }}" class="transition hover:text-[#6C5CE7]">{{ __('Kent sorunları') }}</a>
                    <a href="{{ route('campaigns.index') }}"
                        class="transition hover:text-[#6C5CE7]">{{ __('Kampanyalar') }}</a>
                    <a href="{{ route('blog.index') }}" class="transition hover:text-[#6C5CE7]">{{ __('Blog') }}</a>
                    <a href="{{ route('home') }}#liste-sikayetleri" class="transition hover:text-[#6C5CE7]">{{ __('Akış') }}</a>
                    <a href="{{ route('home') }}#hikayeler" class="transition hover:text-[#6C5CE7]">{{ __('Hikâyeler') }}</a>
                </nav>

                <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                    @auth
                        <span class="relative hidden h-10 w-10 items-center justify-center rounded-full text-neutral-400 hover:bg-neutral-100 sm:inline-flex"
                            aria-hidden="true" title="{{ __('Bildirimler') }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span
                                class="absolute right-2 top-2 inline-block h-2 w-2 rounded-full bg-emerald-400 ring-2 ring-white"></span>
                        </span>
                        @php
                            $__u = auth()->user();
                            preg_match_all('/\p{L}/u', (string) $__u->name, $m);
                            $nmShort = (($m[0] ?? []) !== [])
                                ? mb_strtoupper(implode('', array_slice($m[0], 0, 2)))
                                : mb_substr((string) $__u->name, 0, 2);
                            $nmShort = mb_substr(mb_strtoupper($nmShort ?: '?'), 0, 2);
                        @endphp
                        <a href="{{ route('panel.dashboard') }}"
                            class="hidden items-center gap-2 rounded-full border border-neutral-200/90 bg-neutral-50/80 py-1 pl-1 pr-2.5 text-xs font-semibold text-neutral-900 shadow-sm transition hover:border-neutral-300 sm:inline-flex">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-[#6C5CE7] to-violet-600 text-[11px] font-black text-white shadow-inner">{{ $nmShort }}</span>
                            <span class="hidden max-w-[5.5rem] truncate lg:inline">{{ \Illuminate\Support\Str::limit($__u->name, 14) }}</span>
                            <svg class="hidden h-3.5 w-3.5 text-neutral-400 lg:inline" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="hidden rounded-full px-2.5 py-2 text-[13px] font-semibold text-neutral-700 hover:bg-neutral-100 sm:inline">{{ __('Giriş') }}</a>
                        <a href="{{ route('register') }}"
                            class="hidden rounded-full px-2.5 py-2 text-[13px] font-semibold text-neutral-700 hover:bg-neutral-100 md:inline">{{ __('Üye ol') }}</a>
                    @endauth
                    <a href="{{ $complaintWriteHref }}"
                        class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full bg-[#6C5CE7] px-3.5 py-2 text-[13px] font-bold text-white shadow-[0_8px_20px_-8px_rgba(108,92,231,.55)] ring-2 ring-white transition hover:bg-[#5b4dcf]">
                        <span class="text-base leading-none" aria-hidden="true">+</span>{{ __('Kent sorunu bildir') }}
                    </a>
                    @auth
                        <div class="hidden items-center gap-0.5 border-l border-neutral-200 pl-2 sm:flex">
                            @if (auth()->user()->canAccessAdminPanel())
                                <a href="{{ route('admin.dashboard') }}"
                                    class="rounded-full px-2 py-1.5 text-[11px] font-semibold text-violet-800 hover:bg-violet-50">{{ __('Yönetim') }}</a>
                            @endif
                            @if (auth()->user()->isInstitution())
                                <a href="{{ route('institution.dashboard') }}"
                                    class="rounded-full px-2 py-1.5 text-[11px] font-semibold text-sky-800 hover:bg-sky-50">{{ __('Kurum') }}</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="rounded-full px-2 py-1.5 text-[11px] font-semibold text-neutral-500 hover:bg-rose-50 hover:text-rose-600">{{ __('Çıkış') }}</button>
                            </form>
                        </div>
                    @endauth
                </div>
            </div>

            <nav class="flex justify-center gap-5 overflow-x-auto border-t border-neutral-100 bg-neutral-50/90 py-2.5 text-[13px] font-semibold text-neutral-700 [scrollbar-width:thin] xl:hidden"
                aria-label="{{ __('Ana menü') }}">
                <a href="{{ route('home') }}" class="shrink-0 hover:text-[#6C5CE7]">{{ __('Kent sorunları') }}</a>
                <a href="{{ route('campaigns.index') }}" class="shrink-0 hover:text-[#6C5CE7]">{{ __('Kampanyalar') }}</a>
                <a href="{{ route('blog.index') }}" class="shrink-0 hover:text-[#6C5CE7]">{{ __('Blog') }}</a>
                <a href="{{ route('home') }}#liste-sikayetleri" class="shrink-0 hover:text-[#6C5CE7]">{{ __('Akış') }}</a>
                <a href="{{ route('home') }}#hikayeler" class="shrink-0 hover:text-[#6C5CE7]">{{ __('Hikâyeler') }}</a>
                @auth
                    @if (auth()->user()->canAccessAdminPanel())
                        <a href="{{ route('admin.dashboard') }}" class="shrink-0 hover:text-[#6C5CE7]">{{ __('Yönetim') }}</a>
                    @endif
                    @if (auth()->user()->isInstitution())
                        <a href="{{ route('institution.dashboard') }}" class="shrink-0 hover:text-[#6C5CE7]">{{ __('Kurum') }}</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline shrink-0">
                        @csrf
                        <button type="submit" class="hover:text-rose-600">{{ __('Çıkış') }}</button>
                    </form>
                @endauth
            </nav>

            @hasSection('toolbar')
                @yield('toolbar')
            @else
                @unless (request()->routeIs('home'))
                    <div class="border-t border-neutral-100 py-3">
                        <form method="get" action="{{ route('home') }}" role="search"
                            class="mx-auto flex max-w-2xl min-w-0 items-center rounded-full border border-neutral-200/70 bg-neutral-50/90 px-2 py-1.5 shadow-inner shadow-black/[0.03] ring-1 ring-black/[0.03]">
                            @foreach (request()->only(['city_id', 'category_id', 'relax_city', 'lat', 'lng']) as $k => $v)
                                @continue(is_string($v) && trim($v) === '')
                                @continue($v === null)
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endforeach
                            <label class="sr-only" for="global-search">{{ __('Şikâyet başlığında ara…') }}</label>
                            <span class="pl-3 text-neutral-400" aria-hidden="true">
                                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-4.35-4.35M18 10.5a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z" />
                                </svg>
                            </span>
                            <input id="global-search" type="search" name="q" value="{{ request('q') }}"
                                placeholder="{{ __('Kent sorunu, birim ya da adres ara…') }}"
                                class="min-w-0 flex-1 border-0 bg-transparent px-3 py-1 text-sm outline-none placeholder:text-neutral-400">
                            <button type="submit"
                                class="shrink-0 rounded-full bg-[#6C5CE7] px-5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#5b4dcf]">{{ __('Ara') }}</button>
                        </form>
                    </div>
                @endunless
            @endif
        </div>
    </header>

    <main id="icerik" class="mx-auto max-w-[1250px] px-3 py-4 sm:px-4 sm:py-5">
        @if (session('status'))
            <p role="status" class="mb-4 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-950 shadow-sm">{{ session('status') }}</p>
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

    <footer class="mt-14 border-t border-neutral-200 bg-white py-10 text-sm text-neutral-600">
        <div class="mx-auto grid max-w-[1250px] gap-8 px-3 sm:grid-cols-2 sm:gap-10 lg:grid-cols-4">
            <div class="sm:col-span-2 lg:col-span-1">
                <p class="text-base font-black text-emerald-800">{{ config('app.name') }}</p>
                <p class="mt-2 max-w-xs leading-relaxed">{{ __('Kent ve mahalle düzeyinde sorun bildirimi: fotoğraf, konum ve moderasyon ile şeffaf süreç. Resmi kanallar yerine geçmez.') }}</p>
            </div>
            <div>
                <p class="text-[11px] font-black uppercase tracking-wider text-neutral-400">{{ __('Üyelik') }}</p>
                <ul class="mt-3 space-y-2 font-semibold">
                    <li><a href="{{ route('login') }}" class="text-neutral-900 hover:text-emerald-700 hover:underline">{{ __('Vatandaş girişi') }}</a></li>
                    <li><a href="{{ route('blog.index') }}" class="text-neutral-900 hover:text-emerald-700 hover:underline">{{ __('Blog') }}</a></li>
                    <li><a href="{{ route('campaigns.index') }}" class="text-neutral-900 hover:text-emerald-700 hover:underline">{{ __('Sosyal kampanyalar') }}</a></li>
                    <li><a href="{{ route('login.brand') }}" class="text-neutral-900 hover:text-emerald-700 hover:underline">{{ __('Belediye / kurum (/brand)') }}</a></li>
                </ul>
            </div>
            <div>
                <p class="text-[11px] font-black uppercase tracking-wider text-neutral-400">{{ __('İletişim ve yasal') }}</p>
                <ul class="mt-3 space-y-2">
                    <li><a href="{{ route('contact') }}" class="font-medium underline-offset-4 hover:text-emerald-700 hover:underline">{{ __('İletişim') }}</a></li>
                    <li><a href="{{ route('legal.privacy') }}" class="font-medium underline-offset-4 hover:text-emerald-700 hover:underline">{{ __('Gizlilik') }}</a></li>
                    <li><a href="{{ route('legal.kvkk') }}" class="font-medium underline-offset-4 hover:text-emerald-700 hover:underline">{{ __('KVKK') }}</a></li>
                    <li><a href="{{ route('legal.terms') }}" class="font-medium underline-offset-4 hover:text-emerald-700 hover:underline">{{ __('Kullanım koşulları') }}</a></li>
                </ul>
            </div>
            <div>
                <p class="text-[11px] font-black uppercase tracking-wider text-neutral-400">{{ __('Uyarı') }}</p>
                <p class="mt-3 leading-relaxed text-neutral-500">{{ __('Resmi başvuru yollarının yerini almaz; yasal haklarınızı etkileyebilecek hususlarda mevzuat ve ilgili kurumları yanınıza alınız.') }}</p>
            </div>
        </div>
    </footer>

    @php
        $hideFab = request()->routeIs(
            'login',
            'register',
            'login.brand',
            'login.super',
            'verify.phone.form',
            'contact',
            'complaints.quick.create',
        );
    @endphp
    @unless ($hideFab)
        @php
            $fabHref = route('complaints.quick.create');
        @endphp
        <a href="{{ $fabHref }}"
            class="fixed bottom-[max(1.25rem,env(safe-area-inset-bottom))] right-[max(1.25rem,env(safe-area-inset-right))] z-[70] flex h-14 items-center gap-2 rounded-full bg-[#6C5CE7] px-5 text-[15px] font-bold text-white shadow-2xl shadow-[#6C5CE7]/40 ring-[3px] ring-white hover:bg-[#5b4dcf] md:hidden">
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-xl leading-none">+</span>
            <span>{{ __('Kent bildir') }}</span>
        </a>
    @endunless

    @stack('scripts')
</body>

</html>
