<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', __('Panel')) • {{ config('app.name') }}</title>
    @fonts
    @vite(['resources/css/admin.css', 'resources/js/app.js'])
    @stack('head')
</head>

@php
    $panelUser = auth()->user();
    $panelInitials = '—';
    if ($panelUser !== null) {
        $panelInitials = \Illuminate\Support\Str::of($panelUser->name ?? '')
            ->trim()->explode(' ')->filter()->take(2)
            ->map(fn (string $w): string => mb_strtoupper(mb_substr($w, 0, 1)))
            ->implode('');
        $panelInitials = $panelInitials !== '' ? $panelInitials : '•';
    }
    $panelKind = $panelKind ?? 'user';
    $panelTitle = $panelKind === 'institution' ? __('Kurum paneli') : __('Kullanıcı paneli');
    $navActive = fn (string $pattern): string => request()->routeIs($pattern) ? 'psc-nav-link--active' : '';
@endphp

<body class="psc-body">
    <input type="checkbox" id="psc-sidebar-toggle" autocomplete="off">

    <label for="psc-sidebar-toggle" class="psc-overlay" aria-hidden="true"></label>

    <aside class="psc-sidebar">
        <div class="psc-sidebar__head">
            <a href="{{ $panelKind === 'institution' ? route('institution.dashboard') : route('panel.dashboard') }}" class="block">
                <p class="psc-sidebar__site">{{ config('app.name') }}</p>
                <p class="psc-sidebar__title">{{ $panelTitle }}</p>
            </a>
        </div>

        <nav class="psc-sidebar__nav" aria-label="{{ __('Panel menüsü') }}">
            <div class="psc-nav-group">
                <p class="psc-nav-group__label">{{ __('Genel') }}</p>
                @if ($panelKind === 'institution')
                    <a href="{{ route('institution.dashboard') }}" class="psc-nav-link {{ $navActive('institution.dashboard') }}">
                        @include('partials.psc.icons', ['name' => 'dashboard'])
                        {{ __('Dashboard') }}
                    </a>
                @else
                    <a href="{{ route('panel.dashboard') }}" class="psc-nav-link {{ $navActive('panel.dashboard') }}">
                        @include('partials.psc.icons', ['name' => 'dashboard'])
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('campaigns.index') }}" class="psc-nav-link {{ $navActive('campaigns.index') }}">
                        @include('partials.psc.icons', ['name' => 'campaign'])
                        {{ __('Kampanyalarım') }}
                    </a>
                    <a href="{{ route('campaigns.create') }}" class="psc-nav-link {{ $navActive('campaigns.create') }}">
                        @include('partials.psc.icons', ['name' => 'plus'])
                        {{ __('Kampanya başlat') }}
                    </a>
                @endif
                @yield('panel_nav_extra')
            </div>
        </nav>

        @if ($panelUser !== null)
            <div class="psc-sidebar__foot">
                <div class="psc-user-chip">
                    <div class="psc-avatar">{{ $panelInitials }}</div>
                    <div class="min-w-0">
                        <p class="psc-user-chip__name">{{ $panelUser->name }}</p>
                        <p class="psc-user-chip__role">{{ $panelUser->role->value }}</p>
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

            <div class="psc-topbar__search min-w-0">
                <p class="truncate text-sm font-semibold text-[#0f172a]">@yield('title')</p>
            </div>

            <div class="psc-topbar__actions">
                @if ($panelUser !== null)
                    <div class="psc-topbar-user">
                        <div class="psc-avatar">{{ $panelInitials }}</div>
                        <div class="min-w-0 hidden sm:block">
                            <p class="truncate text-xs font-semibold text-[#0f172a]">{{ $panelUser->name }}</p>
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
