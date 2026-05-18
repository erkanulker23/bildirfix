<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', __('Panel')) • {{ config('app.name') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
@endphp

<body class="shell-body">
    <div class="flex min-h-screen">
        <input type="checkbox" id="panel-sidebar-toggle" class="peer sr-only" autocomplete="off">
        <label for="panel-sidebar-toggle"
            class="fixed inset-0 z-30 hidden bg-gray-900/40 opacity-0 backdrop-blur-[1px] transition peer-checked:pointer-events-auto peer-checked:opacity-100 lg:hidden"
            aria-hidden="true"></label>

        <aside
            class="shell-sidebar fixed inset-y-0 left-0 z-40 -translate-x-full transition-transform duration-200 peer-checked:translate-x-0 lg:relative lg:translate-x-0">
            <div class="shell-sidebar__brand">
                <a href="{{ $panelKind === 'institution' ? route('institution.dashboard') : route('panel.dashboard') }}">
                    <p class="shell-sidebar__brand-sub">{{ config('app.name') }}</p>
                    <p class="shell-sidebar__brand-title">
                        {{ $panelKind === 'institution' ? __('Kurum paneli') : __('Kullanıcı paneli') }}
                    </p>
                </a>
            </div>

            <nav class="shell-nav">
                <p class="shell-nav__label">{{ __('Genel') }}</p>
                @if ($panelKind === 'institution')
                    <a href="{{ route('institution.dashboard') }}"
                        class="shell-nav__link {{ request()->routeIs('institution.dashboard') ? 'shell-nav__link--active' : '' }}">
                        {{ __('Dashboard') }}
                    </a>
                @else
                    <a href="{{ route('panel.dashboard') }}"
                        class="shell-nav__link {{ request()->routeIs('panel.dashboard') ? 'shell-nav__link--active' : '' }}">
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('campaigns.index') }}"
                        class="shell-nav__link {{ request()->routeIs('campaigns.*') && ! request()->routeIs('campaigns.create') ? 'shell-nav__link--active' : '' }}">
                        {{ __('Kampanyalarım') }}
                    </a>
                    <a href="{{ route('campaigns.create') }}"
                        class="shell-nav__link {{ request()->routeIs('campaigns.create') ? 'shell-nav__link--active' : '' }}">
                        {{ __('Kampanya başlat') }}
                    </a>
                @endif
                @yield('panel_nav_extra')
            </nav>

            @if ($panelUser !== null)
                <div class="shell-sidebar__user">
                    <div class="shell-sidebar__user-card">
                        <div class="shell-avatar">{{ $panelInitials }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-bold text-gray-900">{{ $panelUser->name }}</p>
                            <p class="truncate text-[11px] text-gray-500">{{ $panelUser->role->value }}</p>
                        </div>
                    </div>
                    <a href="{{ route('home') }}" class="mt-2 block px-2 text-xs font-semibold text-gray-500 hover:text-blue-600">{{ __('Siteye dön') }}</a>
                </div>
            @endif
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="shell-header">
                <div class="shell-header__inner">
                    <label for="panel-sidebar-toggle"
                        class="flex h-10 w-10 shrink-0 cursor-pointer items-center justify-center rounded-md border border-gray-200 bg-white text-gray-600 lg:hidden"
                        aria-label="{{ __('Menü') }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </label>

                    <div class="min-w-0 flex-1">
                        @hasSection('panel_heading')
                            @yield('panel_heading')
                        @else
                            <p class="text-sm font-bold text-gray-900">@yield('title')</p>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        @if (auth()->check())
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="shell-btn shell-btn--secondary py-2">{{ __('Çıkış') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            </header>

            <main class="shell-main">
                @if (session('status'))
                    <div class="shell-alert shell-alert--success" role="status">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="shell-alert shell-alert--error">
                        <p class="font-bold">{{ __('Doğrulama hatası') }}</p>
                        <ul class="mt-2 list-inside list-disc text-[13px]">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>

</html>
