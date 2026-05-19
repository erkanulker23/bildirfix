<header
    class="sticky top-0 z-50 safe-top border-b border-neutral-200 bg-white/95 shadow-sm backdrop-blur-md supports-[backdrop-filter]:bg-white/90">
    <div class="relative mx-auto max-w-[1200px] px-3 sm:px-5">
        <div class="flex flex-col gap-2 py-2 md:min-h-14 md:flex-row md:items-center md:gap-3 md:py-1">
            {{-- Mobil: logo + aksiyonlar üst satır; masaüstü: md:contents ile tek satır --}}
            <div class="flex min-h-11 items-center justify-between gap-2 md:contents">
                <a href="{{ route('home') }}"
                    class="flex min-w-0 shrink-0 items-center gap-2 tracking-tight text-neutral-900 md:order-1">
                    @if ($siteBranding->hasCustomLogo())
                        <img src="{{ $siteBranding->logoUrl() }}" alt="{{ config('app.name') }}"
                            class="h-9 w-auto max-w-[140px] shrink-0 object-contain object-left" width="140" height="36">
                    @else
                        <span
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary text-white shadow-sm ring-2 ring-white"
                            aria-hidden="true">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 13 9 17 19 7" />
                            </svg>
                        </span>
                        <span class="font-heading truncate text-[1.05rem] font-extrabold leading-none tracking-tight sm:text-[1.125rem]">{{ config('app.name') }}</span>
                    @endif
                </a>

                <div class="flex shrink-0 items-center gap-1 sm:gap-2 md:order-3">
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
                        class="btn-primary hidden shrink-0 !px-2.5 !py-2 text-[13px] ring-2 ring-white md:inline-flex sm:!px-3.5">
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

            {{-- Mobil: logo altında; masaüstü: ortada --}}
            <nav
                class="flex w-full min-w-0 items-center gap-4 overflow-x-auto whitespace-nowrap text-[13px] font-semibold text-neutral-700 [scrollbar-width:none] md:order-2 md:flex-1 md:justify-center md:gap-6 [&::-webkit-scrollbar]:hidden"
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
                        class="shrink-0 rounded-md px-0.5 py-1 transition hover:text-primary md:hidden">{{ __('Profil') }}</a>
                @endauth
            </nav>
        </div>
    </div>
</header>
