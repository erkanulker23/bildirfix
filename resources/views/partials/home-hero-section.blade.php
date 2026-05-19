{{-- Ana sayfa hero — kent şikâyet odaklı, yüksek kontrast --}}
@php
    $heroQuickTopics = [
        ['label' => __('Kaldırım'), 'q' => 'kaldırım'],
        ['label' => __('Çevre'), 'q' => 'çevre'],
        ['label' => __('Ulaşım'), 'q' => 'ulaşım'],
        ['label' => __('Aydınlatma'), 'q' => 'aydınlatma'],
    ];
@endphp
<section class="home-hero relative z-0 mb-10 w-full overflow-hidden bg-gradient-to-br from-[#0a0f1a] via-[#1a1040] to-[#0d3d32] text-white"
    aria-labelledby="hero-baslik">
    <div class="home-hero__mesh pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="home-hero__blob home-hero__blob--emerald"></div>
        <div class="home-hero__blob home-hero__blob--violet"></div>
        <div class="home-hero__blob home-hero__blob--amber"></div>
        <div class="home-hero__grid absolute inset-0 opacity-[0.35]"></div>
    </div>

    <div class="relative z-[1] mx-auto max-w-[1250px] px-5 py-10 sm:px-8 sm:py-14 lg:py-16">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,1.08fr)_minmax(240px,0.92fr)] lg:items-center lg:gap-12 xl:gap-16">
            <div class="min-w-0">
                <div class="home-hero__badge inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-[11px] font-black uppercase tracking-[0.14em] text-emerald-100">
                    <span class="relative flex h-2 w-2" aria-hidden="true">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-70"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-300"></span>
                    </span>
                    {{ __('Kent şikâyet ve çözüm ağı') }}
                </div>

                <h1 id="hero-baslik" class="home-hero__title mt-5 font-heading font-black tracking-tight text-white">
                    <span class="block text-[clamp(1.85rem,4.2vw,2.65rem)] leading-[1.05] text-white/95">{{ __('Çözüm için') }}</span>
                    <span class="home-hero__brand mt-1 block text-[clamp(2rem,5vw,3.25rem)] leading-[1.02]">{{ config('app.name') }}</span>
                </h1>

                <p class="mt-5 max-w-xl text-[16px] font-medium leading-relaxed text-white/75 sm:text-[17px]">
                    {{ __('Kaldırım, çevre, ulaşım ve benzeri kent yaşamı bildirimi; fotoğraf ve konum ile kuruma görünür kılın.') }}
                </p>

                @if (! empty($geoActive))
                    <p class="mt-4 inline-flex items-center gap-2 rounded-full border border-emerald-400/30 bg-emerald-500/15 px-4 py-2 text-[13px] font-bold text-emerald-100 backdrop-blur-sm">
                        <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.9)]" aria-hidden="true"></span>
                        {{ __('Konum sıralaması açık') }}
                    </p>
                @endif

                <form method="get" action="{{ route('feed.index') }}" role="search" class="home-hero__search mt-8">
                    @foreach (request()->only(['city_id', 'relax_city', 'lat', 'lng']) as $k => $v)
                        @if (! (is_string($v) && trim($v) === '') && $v !== null)
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endif
                    @endforeach
                    <label class="sr-only" for="hero-arama">{{ __('Kent sorununda ara…') }}</label>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-stretch">
                        <div class="flex min-h-[3.5rem] min-w-0 flex-1 items-center gap-3 rounded-2xl border border-white/20 bg-white/95 px-4 shadow-[0_20px_50px_-20px_rgba(0,0,0,0.45)] backdrop-blur-md sm:rounded-2xl sm:pl-5">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-900/30" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.2-5.2M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
                                </svg>
                            </span>
                            <input id="hero-arama" type="search" name="q" value="{{ old('q', $searchQuery ?? '') }}"
                                autocomplete="off" placeholder="{{ __('Kent sorununda ara…') }}"
                                class="w-full min-w-0 flex-1 border-0 bg-transparent py-3 text-[16px] font-semibold text-neutral-900 outline-none placeholder:font-medium placeholder:text-neutral-400">
                        </div>
                        <button type="submit"
                            class="home-hero__search-btn shrink-0 rounded-2xl px-8 py-3.5 text-[15px] font-black uppercase tracking-wide text-neutral-950 shadow-[0_12px_32px_-8px_rgba(52,211,153,0.55)] transition hover:brightness-105 sm:min-w-[7.5rem]">
                            {{ __('Ara') }}
                        </button>
                    </div>
                </form>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-white/70">{{ __('Hızlı ara:') }}</span>
                    @foreach ($heroQuickTopics as $topic)
                        <a href="{{ route('feed.index', array_filter(['q' => $topic['q'], 'city_id' => $activeCityId ?? null])) }}"
                            class="rounded-full border border-white/30 bg-white/15 px-3 py-1.5 text-[12px] font-bold text-white shadow-sm backdrop-blur-sm transition hover:border-emerald-300/60 hover:bg-emerald-500/25">
                            {{ $topic['label'] }}
                        </a>
                    @endforeach
                </div>

                <div class="mt-8 flex flex-wrap items-center gap-2.5">
                    @auth
                        <a href="{{ route('posts.create') }}" class="home-hero__cta-primary">
                            <span aria-hidden="true">+</span>{{ __('Kent sorunu bildir') }}
                        </a>
                        <a href="{{ route('feed.index') }}" class="home-hero__cta-secondary">{{ __('Canlı akış') }}</a>
                    @else
                        <a href="{{ route('posts.create') }}" class="home-hero__cta-primary">
                            <span aria-hidden="true">+</span>{{ __('Kent sorunu bildir') }}
                        </a>
                        <a href="{{ route('register') }}" class="home-hero__cta-secondary">{{ __('Üye ol') }}</a>
                        <a href="{{ route('login') }}" class="home-hero__cta-ghost">{{ __('Giriş') }}</a>
                    @endauth
                    <a href="{{ route('contact') }}" class="home-hero__cta-ghost">{{ __('İletişim') }}</a>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-[min(100%,22rem)] lg:max-w-none">
                <div class="home-hero__visual relative mx-auto aspect-square w-full max-w-[20rem] lg:max-w-[22rem] xl:max-w-[24rem]" aria-hidden="true">
                    <div class="home-hero__visual-glow pointer-events-none absolute -inset-6 rounded-full bg-emerald-500/20 blur-3xl"></div>
                    <div class="absolute -right-[6%] -top-[4%] h-[62%] w-[58%] overflow-hidden rounded-3xl bg-violet-600 shadow-2xl ring-2 ring-white/20 home-hero__float-slow">
                        <img src="{{ asset('images/hero/collage-woman-purple.jpg') }}" alt=""
                            class="h-full w-full object-cover object-[center_top] mix-blend-luminosity" loading="eager" decoding="async" fetchpriority="high">
                        <span class="absolute inset-0 bg-gradient-to-br from-violet-500/55 to-indigo-900/70 mix-blend-color"></span>
                    </div>
                    <div class="absolute bottom-[26%] left-0 h-[43%] w-[43%] overflow-hidden rounded-full border-[5px] border-white/90 bg-neutral-900 shadow-2xl ring-2 ring-emerald-400/80 home-hero__float-mid">
                        <img src="{{ asset('images/hero/collage-man-circle.jpg') }}" alt=""
                            class="h-full w-full object-cover object-top" loading="lazy" decoding="async">
                    </div>
                    <div class="absolute bottom-[-2%] right-[6%] h-[54%] w-[52%] overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-amber-400 to-orange-500 p-1.5 shadow-2xl ring-2 ring-white/25 home-hero__float-fast">
                        <div class="h-full w-full overflow-hidden rounded-[1.35rem]">
                            <img src="{{ asset('images/hero/collage-woman-yellow.jpg') }}" alt=""
                                class="h-full w-full object-cover object-center" loading="lazy" decoding="async">
                        </div>
                    </div>
                    @isset($platformStats)
                        <div class="absolute left-0 top-[12%] z-10 hidden rounded-2xl border border-white/20 bg-white/95 px-3 py-2.5 shadow-xl backdrop-blur-md home-hero__float-mid sm:block sm:-left-2 sm:top-[18%]">
                            <p class="text-[9px] font-black uppercase tracking-wider text-emerald-700">{{ __('Son 30 gün') }}</p>
                            <p class="font-heading text-xl font-black tabular-nums text-neutral-950">{{ number_format((int) ($platformStats['last30'] ?? 0)) }}</p>
                        </div>
                        <div class="absolute right-0 bottom-[6%] z-10 hidden rounded-2xl border border-white/20 bg-neutral-950/90 px-3 py-2.5 shadow-xl backdrop-blur-md home-hero__float-slow sm:block sm:-right-1 sm:bottom-[8%]">
                            <p class="text-[9px] font-black uppercase tracking-wider text-emerald-300">{{ __('Kanıtlı') }}</p>
                            <p class="font-heading text-xl font-black tabular-nums text-white">%{{ number_format((int) ($platformStats['evidence_pct'] ?? 0)) }}</p>
                        </div>
                    @endisset
                </div>
            </div>
        </div>

        @isset($platformStats)
            <dl class="home-hero__stats mt-10 grid gap-3 pb-2 sm:mt-12 {{ (int) ($platformStats['resolved'] ?? 0) > 0 ? 'sm:grid-cols-3' : 'sm:grid-cols-2' }}">
                @if ((int) ($platformStats['resolved'] ?? 0) > 0)
                    <div class="home-hero__stat">
                        <dt>{{ __('Çözülen şikâyet') }}</dt>
                        <dd>{{ number_format((int) $platformStats['resolved'], 0, ',', '.') }}</dd>
                    </div>
                @endif
                <div class="home-hero__stat">
                    <dt>{{ __('Son 30 günde kayıt') }}</dt>
                    <dd>{{ number_format((int) $platformStats['last30'], 0, ',', '.') }}</dd>
                </div>
                <div class="home-hero__stat home-hero__stat--accent">
                    <dt>{{ __('Kanıtlı içerik') }}</dt>
                    <dd>%{{ number_format((int) $platformStats['evidence_pct'], 0, ',', '.') }}</dd>
                </div>
            </dl>
        @endisset
    </div>
</section>
