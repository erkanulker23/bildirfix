<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', __('Dashboard')) • {{ config('app.name') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

@php
    /** @var bool $viewerIsSuperAdmin */
    $viewerIsSuperAdmin = $viewerIsSuperAdmin ?? (auth()->user()?->isSuperAdmin() ?? false);
    $adminUser = auth()->user();
    $adminInitials = '—';
    if ($adminUser !== null) {
        $adminInitials = \Illuminate\Support\Str::of($adminUser->name ?? '')
            ->trim()
            ->explode(' ')
            ->filter()
            ->take(2)
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
@endphp

<body class="min-h-screen bg-[#F9FAFB] font-sans text-gray-800 antialiased">
    {{-- peer, ardından ardışık kardeşler: overlay + aside (Tailwind peer-checked yalnızca kardeşlerde çalışır) --}}
    <div class="flex min-h-screen">
        <input type="checkbox" id="admin-sidebar-toggle" class="peer sr-only" autocomplete="off">

        <label for="admin-sidebar-toggle"
            class="fixed inset-0 z-30 bg-slate-900/40 opacity-0 pointer-events-none backdrop-blur-[1px] transition-opacity peer-checked:opacity-100 peer-checked:pointer-events-auto lg:hidden"
            aria-hidden="true"></label>

        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-[260px] max-w-[85vw] -translate-x-full flex-col border-r border-white/10 bg-secondary transition-transform duration-200 ease-out peer-checked:translate-x-0 lg:relative lg:z-10 lg:max-w-none lg:shrink-0 lg:translate-x-0">
            <div class="border-b border-white/10 px-4 py-5">
                <a href="{{ route('admin.dashboard') }}" class="block">
                    <p class="font-heading text-xs font-bold uppercase tracking-wide text-white/50">{{ config('app.name') }}</p>
                    <p class="mt-1 text-lg font-extrabold leading-tight text-white">{{ __('Yönetim paneli') }}</p>
                    <p class="mt-0.5 text-xs text-white/55">{{ __('Operasyon ve onay özeti') }}</p>
                </a>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-2 py-4 text-[13px]">
                <p class="px-3 pb-1 pt-1 text-[10px] font-bold uppercase tracking-wider text-white/45">{{ __('Genel') }}</p>
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.dashboard') ? 'border-l-4 border-primary bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary/25 text-primary-light" aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </span>
                    {{ __('Dashboard') }}
                </a>

                @if ($viewerIsSuperAdmin)
                    <p class="px-3 pb-1 pt-5 text-[10px] font-bold uppercase tracking-wider text-white/45">{{ __('Onay kuyruğu') }}</p>
                    <a href="{{ route('admin.moderation.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.moderation.*') ? 'border-l-4 border-primary bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">{{ __('Şikâyetler') }}</a>
                    <a href="{{ route('admin.campaign-moderation.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.campaign-moderation.*') ? 'border-l-4 border-primary bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">{{ __('Kampanyalar') }}</a>
                    <a href="{{ route('admin.blog-moderation.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.blog-moderation.*') ? 'border-l-4 border-primary bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">{{ __('Blog onayı') }}</a>

                    <p class="px-3 pb-1 pt-5 text-[10px] font-bold uppercase tracking-wider text-white/45">{{ __('İzleme') }}</p>
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.users.*') ? 'border-l-4 border-primary bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">{{ __('Kullanıcılar') }}</a>
                    <a href="{{ route('admin.institutions.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.institutions.*') ? 'border-l-4 border-primary bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">{{ __('Kurumlar') }}</a>
                    <a href="{{ route('admin.campaigns.registry') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.campaigns.registry') ? 'border-l-4 border-primary bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">{{ __('Tüm kampanyalar') }}</a>

                    <p class="px-3 pb-1 pt-5 text-[10px] font-bold uppercase tracking-wider text-white/45">{{ __('Sistem') }}</p>
                    <a href="{{ route('admin.platform-settings.edit') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.platform-settings.*') ? 'border-l-4 border-primary bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">{{ __('Platform / Google') }}</a>
                    <a href="{{ route('admin.mail-settings.edit') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.mail-settings.*') ? 'border-l-4 border-primary bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">{{ __('E-posta (SMTP)') }}</a>
                @endif

                <p class="px-3 pb-1 pt-5 text-[10px] font-bold uppercase tracking-wider text-white/45">{{ __('İçerik') }}</p>
                <a href="{{ route('admin.blog.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.blog.*') ? 'border-l-4 border-primary bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">{{ __('Blog yönetimi') }}</a>
            </nav>

            @if ($adminUser !== null)
                <div class="border-t border-white/10 bg-secondary-soft p-3">
                    <div class="flex items-center gap-3 rounded-ds-md bg-white/95 p-3 shadow-md ring-1 ring-black/5">
                        <div
                            class="font-heading flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-white">
                            {{ $adminInitials }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-bold text-gray-900">{{ $adminUser->name }}</p>
                            <p class="truncate text-[11px] text-gray-500">{{ $adminUser->role->value }}</p>
                        </div>
                    </div>
                    <a href="{{ route('home') }}" class="mt-2 block px-3 py-2 text-xs font-semibold text-white/60 hover:text-primary-light">{{ __('Siteye dön') }}</a>
                </div>
            @endif
        </aside>

        <div class="flex min-w-0 flex-1 flex-col lg:min-h-screen">
            <header class="sticky top-0 z-20 border-b border-gray-200 bg-white shadow-sm">
                <div class="flex items-center gap-3 px-4 py-3 sm:px-6">
                    <label for="admin-sidebar-toggle"
                        class="flex h-11 w-11 shrink-0 cursor-pointer items-center justify-center rounded-ds-md border border-gray-200 bg-white text-gray-600 shadow-sm hover:bg-gray-50 lg:hidden"
                        aria-label="{{ __('Menü') }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </label>

                    <form action="{{ route('admin.users.index') }}" method="get" class="hidden min-w-0 flex-1 md:block">
                        <div class="relative">
                            <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('Hızlı arama: kullanıcı, e-posta, telefon…') }}"
                                class="input-ds w-full rounded-ds-md border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" aria-hidden="true">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                            </span>
                        </div>
                    </form>

                    <div class="ml-auto flex items-center gap-2 sm:gap-3">
                        @if ($viewerIsSuperAdmin)
                            <a href="{{ route('admin.moderation.index') }}"
                                class="relative flex h-11 w-11 items-center justify-center rounded-ds-md border border-gray-200 bg-white text-gray-600 shadow-sm hover:bg-gray-50"
                                title="{{ __('Onay bekleyen') }}">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                @if ($pendingBell > 0)
                                    <span class="absolute -right-1 -top-1 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">{{ $pendingBell > 99 ? '99+' : $pendingBell }}</span>
                                @endif
                            </a>
                        @endif

                        @if ($adminUser !== null)
                            <div class="hidden items-center gap-2 rounded-ds-md border border-gray-200 bg-white px-3 py-1.5 shadow-sm sm:flex">
                                <div
                                    class="font-heading flex h-8 w-8 items-center justify-center rounded-full bg-secondary-soft text-[11px] font-bold text-white">
                                    {{ $adminInitials }}</div>
                                <div class="min-w-0">
                                    <p class="truncate text-xs font-bold text-gray-900">{{ $adminUser->name }}</p>
                                    <p class="truncate text-[10px] text-gray-500">{{ $adminUser->role->value }}</p>
                                </div>
                            </div>
                        @endif

                        @if (auth()->check())
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="rounded-ds-md border border-gray-200 bg-white px-3 py-2 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50">{{ __('Çıkış') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div role="status"
                        class="mb-6 rounded-ds-md border border-success/25 bg-success-light px-4 py-3 text-sm font-semibold text-gray-900">
                        {{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
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
</body>

</html>
