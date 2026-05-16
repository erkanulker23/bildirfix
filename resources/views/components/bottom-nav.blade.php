@php
    $isHome = request()->routeIs('home');
    $isExplore = request()->routeIs('explore') || request()->routeIs('campaigns.*');
    $isMap = request()->routeIs('map');
    $isProfile = request()->routeIs('profile') || request()->routeIs('panel.*');
@endphp

<nav class="fixed bottom-0 left-0 right-0 z-50 border-t border-gray-100 bg-white md:hidden safe-bottom"
    style="height: calc(4rem + env(safe-area-inset-bottom, 0px))" aria-label="{{ __('Alt menü') }}">
    <div class="mx-auto flex h-16 max-w-lg items-center justify-around px-2">
        <a href="{{ route('home') }}" class="nav-item {{ $isHome ? 'active' : '' }}">
            <svg class="h-[22px] w-[22px]" viewBox="0 0 24 24" fill="{{ $isHome ? 'currentColor' : 'none' }}"
                stroke="currentColor" stroke-width="{{ $isHome ? '0' : '2' }}" stroke-linecap="round"
                stroke-linejoin="round" aria-hidden="true">
                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                <polyline points="9 22 9 12 15 12 15 22" />
            </svg>
            <span>{{ __('Ana Sayfa') }}</span>
        </a>

        <a href="{{ route('explore') }}" class="nav-item {{ $isExplore ? 'active' : '' }}">
            <svg class="h-[22px] w-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="{{ request()->routeIs('campaigns.index') ? '2.5' : '2' }}" stroke-linecap="round"
                stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
            <span>{{ __('Keşfet') }}</span>
        </a>

        <div class="relative flex items-center justify-center">
            <a href="{{ route('posts.create') }}" title="{{ __('Paylaş') }}"
                class="fab-share fab-share--nav -mt-8 flex items-center justify-center rounded-full bg-primary text-white">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" aria-hidden="true">
                    <path d="M12 5v14M5 12h14" />
                </svg>
            </a>
        </div>

        <a href="{{ route('map') }}" class="nav-item {{ $isMap ? 'active' : '' }}">
            <svg class="h-[22px] w-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                <circle cx="12" cy="10" r="{{ $isMap ? '4' : '3' }}" fill="{{ $isMap ? 'currentColor' : 'none' }}" />
            </svg>
            <span>{{ __('Harita') }}</span>
        </a>

        <a href="{{ route('profile') }}" class="nav-item {{ $isProfile ? 'active' : '' }}">
            @auth
                @php
                    $pn = trim((string) (auth()->user()->name ?? '?'));
                    preg_match_all('/\p{L}/u', $pn, $pm);
                    $pi = (($pm[0] ?? []) !== []) ? mb_strtoupper(implode('', array_slice($pm[0], 0, 2))) : mb_strtoupper(mb_substr($pn, 0, 2));
                @endphp
                <span
                    class="font-heading flex h-6 w-6 items-center justify-center rounded-full bg-secondary-soft text-[10px] font-bold text-white {{ $isProfile ? 'ring-2 ring-primary ring-offset-1' : '' }}">{{ mb_substr($pi ?: '?', 0, 2) }}</span>
            @else
                <svg class="h-[22px] w-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    aria-hidden="true">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
            @endauth
            <span>{{ __('Profil') }}</span>
        </a>
    </div>
</nav>

<div class="h-16 shrink-0 md:hidden" aria-hidden="true"></div>
