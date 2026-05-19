<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', __('Dashboard')) • {{ config('app.name') }}</title>
    @fonts
    @vite(['resources/css/admin.css', 'resources/js/app.js'])
    @stack('head')
</head>

@php
    $viewerIsSuperAdmin = $viewerIsSuperAdmin ?? (auth()->user()?->isSuperAdmin() ?? false);
    $adminUser = auth()->user();
    $adminInitials = '—';
    if ($adminUser !== null) {
        $adminInitials = \Illuminate\Support\Str::of($adminUser->name ?? '')
            ->trim()->explode(' ')->filter()->take(2)
            ->map(fn (string $w): string => mb_strtoupper(mb_substr($w, 0, 1)))
            ->implode('');
        $adminInitials = $adminInitials !== '' ? $adminInitials : '•';
    }
    $pendingBell = 0;
    if ($viewerIsSuperAdmin && $adminUser !== null) {
        $pendingBell = (int) (\App\Models\Post::query()->where('moderation_status', \App\Enums\PostModerationStatus::Pending)->count()
            + \App\Models\Campaign::query()->where('moderation_status', \App\Enums\CampaignModerationStatus::Pending)->count()
            + \App\Models\BlogPost::query()->where('moderation_status', \App\Enums\PostModerationStatus::Pending)->count());
    }
    $navActive = fn (string $pattern): string => request()->routeIs($pattern) ? 'psc-nav-link--active' : '';
@endphp

<body class="psc-body">
    <input type="checkbox" id="psc-sidebar-toggle" autocomplete="off">

    <label for="psc-sidebar-toggle" class="psc-overlay" aria-hidden="true"></label>

    <aside class="psc-sidebar">
        <div class="psc-sidebar__head">
            <a href="{{ route('admin.dashboard') }}" class="block">
                <p class="psc-sidebar__site">{{ config('app.name') }}</p>
                <p class="psc-sidebar__title">{{ __('Yönetim paneli') }}</p>
            </a>
        </div>

        <nav class="psc-sidebar__nav" aria-label="{{ __('Yönetim menüsü') }}">
            <div class="psc-nav-group">
                <p class="psc-nav-group__label">{{ __('Genel') }}</p>
                <a href="{{ route('admin.dashboard') }}" class="psc-nav-link {{ $navActive('admin.dashboard') }}">
                    @include('partials.psc.icons', ['name' => 'dashboard'])
                    {{ __('Dashboard') }}
                </a>
            </div>

            @if ($viewerIsSuperAdmin)
                <div class="psc-nav-group">
                    <p class="psc-nav-group__label">{{ __('Onay kuyruğu') }}</p>
                    <a href="{{ route('admin.moderation.index') }}" class="psc-nav-link {{ $navActive('admin.moderation.*') }}">
                        @include('partials.psc.icons', ['name' => 'complaint'])
                        {{ __('Şikâyetler') }}
                    </a>
                    <a href="{{ route('admin.campaign-moderation.index') }}" class="psc-nav-link {{ $navActive('admin.campaign-moderation.*') }}">
                        @include('partials.psc.icons', ['name' => 'campaign'])
                        {{ __('Kampanyalar') }}
                    </a>
                </div>

                <div class="psc-nav-group">
                    <p class="psc-nav-group__label">{{ __('İzleme') }}</p>
                    <a href="{{ route('admin.users.index') }}" class="psc-nav-link {{ $navActive('admin.users.*') }}">
                        @include('partials.psc.icons', ['name' => 'users'])
                        {{ __('Kullanıcılar') }}
                    </a>
                    <a href="{{ route('admin.institutions.index') }}" class="psc-nav-link {{ $navActive('admin.institutions.*') }}">
                        @include('partials.psc.icons', ['name' => 'building'])
                        {{ __('Kurumlar') }}
                    </a>
                    <a href="{{ route('admin.campaigns.registry') }}" class="psc-nav-link {{ $navActive('admin.campaigns.*') }}">
                        @include('partials.psc.icons', ['name' => 'campaign'])
                        {{ __('Tüm kampanyalar') }}
                    </a>
                    <a href="{{ route('admin.ads.index') }}" class="psc-nav-link {{ $navActive('admin.ads.*') }}">
                        @include('partials.psc.icons', ['name' => 'ads'])
                        {{ __('Reklamlar') }}
                    </a>
                </div>

                <div class="psc-nav-group">
                    <p class="psc-nav-group__label">{{ __('Sistem') }}</p>
                    <a href="{{ route('admin.platform-settings.edit') }}" class="psc-nav-link {{ $navActive('admin.platform-settings.*') }}">
                        @include('partials.psc.icons', ['name' => 'settings'])
                        {{ __('Platform / Google OAuth') }}
                    </a>
                    <a href="{{ route('admin.homepage-settings.edit') }}" class="psc-nav-link {{ $navActive('admin.homepage-settings.*') }}">
                        @include('partials.psc.icons', ['name' => 'dashboard'])
                        {{ __('Anasayfa ve marka') }}
                    </a>
                    <a href="{{ route('admin.site-integrations.edit') }}" class="psc-nav-link {{ $navActive('admin.site-integrations.*') }}">
                        @include('partials.psc.icons', ['name' => 'ads'])
                        {{ __('SEO ve analitik') }}
                    </a>
                    <a href="{{ route('admin.mail-settings.edit') }}" class="psc-nav-link {{ $navActive('admin.mail-settings.*') }}">
                        @include('partials.psc.icons', ['name' => 'mail'])
                        {{ __('E-posta (SMTP)') }}
                    </a>
                    <a href="{{ route('admin.legal-pages.edit') }}" class="psc-nav-link {{ $navActive('admin.legal-pages.*') }}">
                        @include('partials.psc.icons', ['name' => 'complaint'])
                        {{ __('Yasal sayfalar') }}
                    </a>
                </div>
            @endif

            <div class="psc-nav-group">
                <p class="psc-nav-group__label">{{ __('İçerik') }}</p>
                <a href="{{ route('admin.blog.index') }}" class="psc-nav-link {{ $navActive('admin.blog.*') }}">
                    @include('partials.psc.icons', ['name' => 'blog'])
                    {{ __('Blog yönetimi') }}
                </a>
            </div>
        </nav>

        @if ($adminUser !== null)
            <div class="psc-sidebar__foot">
                <div class="psc-user-chip">
                    <div class="psc-avatar">{{ $adminInitials }}</div>
                    <div class="min-w-0">
                        <p class="psc-user-chip__name">{{ $adminUser->name }}</p>
                        <p class="psc-user-chip__role">{{ $adminUser->role->value }}</p>
                    </div>
                </div>
                <a href="{{ route('home') }}" class="psc-sidebar__home">{{ __('Siteye dön') }}</a>
            </div>
        @endif
    </aside>

    <div class="psc-main">
        <header class="psc-topbar">
            <label for="psc-sidebar-toggle" class="psc-topbar__menu" aria-label="{{ __('Menü') }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </label>

            <form action="{{ route('admin.users.index') }}" method="get" class="psc-topbar__search">
                <svg class="psc-topbar__search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                <input type="search" name="q" value="{{ request('q') }}"
                    placeholder="{{ __('Hızlı arama: kullanıcı, e-posta, telefon…') }}">
            </form>

            <div class="psc-topbar__actions">
                @if ($viewerIsSuperAdmin)
                    <a href="{{ route('admin.moderation.index') }}" class="psc-icon-btn" title="{{ __('Onay bekleyen') }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if ($pendingBell > 0)
                            <span class="psc-icon-btn__badge">{{ $pendingBell > 99 ? '99+' : $pendingBell }}</span>
                        @endif
                    </a>
                @endif

                @if ($adminUser !== null)
                    <div class="psc-topbar-user">
                        <div class="psc-avatar">{{ $adminInitials }}</div>
                        <div class="min-w-0 hidden md:block">
                            <p class="truncate text-xs font-semibold text-[#0f172a]">{{ $adminUser->name }}</p>
                            <p class="truncate text-[10px] text-[#64748b]">{{ $adminUser->role->value }}</p>
                        </div>
                    </div>
                @endif

                @if (auth()->check())
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="psc-btn psc-btn--ghost">{{ __('Çıkış') }}</button>
                    </form>
                @endif
            </div>
        </header>

        <main class="psc-content">
            <div class="psc-content__inner">
                @if (session('status'))
                    <div class="psc-alert psc-alert--success" role="status">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="psc-alert psc-alert--error">
                        <p class="font-semibold">{{ __('Doğrulama hatası') }}</p>
                        <ul class="mt-2 list-inside list-disc text-[13px]">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>

</html>
