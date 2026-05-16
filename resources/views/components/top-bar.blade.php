@props([
    'title' => null,
    'back' => null,
    'cityName' => null,
])

@php
    $cityLabel = $cityName ?: __('Şehir seç');
    $unread = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
@endphp

<header
    class="safe-top sticky top-0 z-40 border-b border-gray-100 bg-white pt-[env(safe-area-inset-top)] shadow-[var(--shadow-sm)]">
    <div class="mx-auto flex h-14 max-w-screen-lg items-center gap-3 px-4">
        @if ($back)
            <a href="{{ $back }}" class="btn-icon !h-11 !w-11 shrink-0 text-gray-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                <span class="sr-only">{{ __('Geri') }}</span>
            </a>
            <h1 class="font-heading min-w-0 flex-1 truncate text-lg font-bold text-gray-900">{{ $title }}</h1>
        @else
            <div class="flex min-w-0 flex-1 items-center gap-0.5">
                <span class="font-heading text-xl font-extrabold tracking-tight text-gray-900">{{ strtoupper(config('app.name')) }}</span>
                <span class="font-heading text-xl font-extrabold text-primary">.</span>
            </div>

            <a href="{{ route('home', request()->except('page')) }}"
                class="flex min-h-11 max-w-[40%] items-center gap-1.5 rounded-full bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-700 transition-colors hover:bg-primary-light">
                <svg class="h-3.5 w-3.5 shrink-0 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" aria-hidden="true">
                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                    <circle cx="12" cy="10" r="3" />
                </svg>
                <span class="truncate">{{ $cityLabel }}</span>
                <svg class="h-3 w-3 shrink-0 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5" aria-hidden="true">
                    <path d="m6 9 6 6 6-6" />
                </svg>
            </a>
        @endif

        <div class="flex shrink-0 items-center gap-1">
            @isset($actions)
                {{ $actions }}
            @else
                @auth
                    <a href="{{ route('notifications.index') }}" class="btn-icon relative !h-11 !w-11 text-gray-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
                        </svg>
                        @if ($unread > 0)
                            <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-danger ring-2 ring-white"></span>
                            <span class="sr-only">{{ __('Okunmamış bildirim') }}</span>
                        @endif
                    </a>
                    <a href="{{ route('profile') }}" class="flex h-11 w-11 items-center justify-center">
                        @php
                            $pn = trim((string) (auth()->user()->name ?? '?'));
                            preg_match_all('/\p{L}/u', $pn, $pm);
                            $pi = (($pm[0] ?? []) !== []) ? mb_strtoupper(implode('', array_slice($pm[0], 0, 2))) : mb_strtoupper(mb_substr($pn, 0, 2));
                        @endphp
                        <span
                            class="font-heading flex h-9 w-9 items-center justify-center rounded-full bg-secondary-soft text-xs font-bold text-white ring-2 ring-gray-100">{{ mb_substr($pi ?: '?', 0, 2) }}</span>
                        <span class="sr-only">{{ __('Profil') }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary !px-4 !py-2 text-sm">{{ __('Giriş') }}</a>
                @endauth
            @endisset
        </div>
    </div>
</header>
