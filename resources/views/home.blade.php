@extends('layouts.app')

@php
    $homeBranding = \App\Support\SiteBranding::fromPlatform();
@endphp
@section('title', $homeBranding->homepageTitle())

@section('content')
    @isset($platformStats)
        <div class="relative left-1/2 z-0 w-screen max-w-[100vw] -translate-x-1/2 border-b border-neutral-800 bg-neutral-900 text-white">
            @php
                $showResolvedStat = (int) ($platformStats['resolved'] ?? 0) > 0;
                $showLiveCampaigns = (int) ($platformStats['campaigns_live'] ?? 0) > 0;
            @endphp
            <div @class([
                'mx-auto flex max-w-[1200px] flex-wrap items-center gap-x-4 gap-y-2 px-4 py-2.5 sm:px-5',
                'justify-between' => $showResolvedStat || $showLiveCampaigns,
                'justify-end' => ! $showResolvedStat && ! $showLiveCampaigns,
            ])>
                @if ($showResolvedStat || $showLiveCampaigns)
                <p class="text-[12px] font-medium text-neutral-300">
                    @if ($showResolvedStat)
                        {{ __('Çözülen kayıt') }}
                        <span class="font-black tabular-nums text-emerald-400">{{ number_format((int) $platformStats['resolved'], 0, ',', '.') }}</span>
                    @endif
                    @if ($showResolvedStat && $showLiveCampaigns)
                        <span class="mx-2 hidden text-neutral-600 sm:inline" aria-hidden="true">|</span>
                    @endif
                    @if ($showLiveCampaigns)
                        <a href="{{ route('campaigns.index') }}"
                            class="mt-1 inline-flex rounded-md bg-white/10 px-2 py-0.5 text-[11px] font-bold text-violet-200 ring-1 ring-white/15 hover:bg-white/15 sm:mt-0">
                            {{ __(':n kampanya', ['n' => number_format((int) $platformStats['campaigns_live'])]) }}</a>
                    @endif
                </p>
                @endif
                <div class="flex flex-wrap items-center gap-3">
                    <span class="hidden text-[12px] text-neutral-400 sm:inline">{{ __('Akış ve süreçleri tek ekranda izle.') }}</span>
                    <a href="{{ route('feed.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500 px-3 py-1 text-[11px] font-black uppercase tracking-wide text-neutral-950 shadow-sm ring-1 ring-emerald-400/50 hover:bg-emerald-400">
                        <span class="relative flex h-2 w-2" aria-hidden="true">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-200 opacity-60"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-white"></span>
                        </span>
                        {{ __('Canlı akış') }}</a>
                </div>
            </div>
        </div>
    @endisset
    @php
        $feedWithoutGeo = array_filter([
            'city_id' => $activeCityId,
            'category_id' => request()->integer('category_id') ?: null,
            'q' => $searchQuery !== '' ? $searchQuery : null,
        ], fn ($v) => $v !== null && $v !== '');
    @endphp

    {{-- Hero — açık yüzey, yeşil arama + mor tonlu ana çağrı (referans: modern tüketici platformu stili) --}}
    <section
        class="relative left-1/2 z-0 mb-10 w-screen max-w-[100vw] -translate-x-1/2 rounded-none border-y border-neutral-200/80 bg-gradient-to-b from-neutral-50 via-white to-[#eef1f8] shadow-[inset_0_1px_0_rgba(255,255,255,0.9)]"
        aria-labelledby="hero-baslik">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-indigo-100/35 to-transparent blur-2xl"
            aria-hidden="true"></div>
        <div
            class="relative z-[1] mx-auto grid max-w-[1250px] gap-10 px-5 py-9 sm:gap-12 sm:px-8 sm:py-12 lg:grid-cols-[minmax(0,1.06fr)_minmax(260px,.94fr)] lg:items-center lg:gap-14">
            <div>
                <p class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-emerald-700 shadow-sm ring-1 ring-emerald-200/80">
                    <svg class="h-3.5 w-3.5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                    </svg>
                    {{ __('Kent şikâyet ve çözüm ağı') }}</p>
                <h1 id="hero-baslik"
                    class="mt-4 font-heading text-[clamp(1.75rem,3.6vw,2.75rem)] font-black leading-[1.08] tracking-tight text-neutral-800">
                    {{ __('Çözüm için') }}
                    <span class="text-neutral-950">{{ config('app.name') }}</span>
                </h1>
                <p class="mt-3 max-w-xl text-[15px] leading-relaxed text-neutral-600">{{ __('Kaldırım, çevre, ulaşım ve benzeri kent yaşamı bildirimi; fotoğraf ve konum ile kuruma görünür kılın.') }}</p>
                @if (! empty($geoActive))
                    <p class="mt-4 inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-2 text-[13px] font-semibold text-emerald-950 ring-1 ring-emerald-200/80">
                        <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-500" aria-hidden="true"></span>
                        {{ __('Konum sıralaması açık.') }}</p>
                @endif

                <form method="get" action="{{ route('feed.index') }}" role="search"
                    class="mt-8 flex flex-col gap-2 rounded-full border border-neutral-200/80 bg-white p-2 shadow-[0_14px_44px_-24px_rgba(79,70,229,0.2)] sm:flex-row sm:items-center sm:rounded-full">
                    @foreach (request()->only(['city_id', 'category_id', 'relax_city', 'lat', 'lng']) as $k => $v)
                        @if (! (is_string($v) && trim($v) === '') && $v !== null)
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endif
                    @endforeach
                    <label class="sr-only" for="hero-arama">{{ __('Kent sorununda ara…') }}</label>
                    <div class="flex min-h-[3.125rem] min-w-0 flex-1 items-center gap-2 pl-5 text-neutral-500">
                        <svg class="h-6 w-6 shrink-0 text-neutral-400" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-5.2-5.2M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
                        </svg>
                        <input id="hero-arama" type="search" name="q" value="{{ old('q', $searchQuery) }}"
                            autocomplete="off"
                            placeholder="{{ __('Kent sorununun özünü ara…') }}"
                            class="w-full flex-1 border-0 bg-transparent py-3 text-[16px] font-medium text-neutral-900 outline-none placeholder:text-neutral-500 sm:text-[15px]">
                    </div>
                    <button type="submit"
                        class="shrink-0 rounded-full bg-emerald-400 px-7 py-3.5 text-[14px] font-black text-neutral-950 shadow-md shadow-teal-500/35 transition hover:bg-emerald-300 sm:self-stretch md:rounded-full md:py-3">
                        {{ __('Ara') }}
                    </button>
                </form>

                <div class="mt-6 flex flex-wrap items-center gap-2">
                    @auth
                        <a href="{{ route('posts.create') }}"
                            class="inline-flex items-center justify-center gap-1 rounded-full bg-violet-600 px-5 py-2.5 text-[13px] font-bold text-white shadow-[0_6px_20px_-4px_rgba(91,33,182,0.45)] transition hover:bg-violet-700"><span class="text-base font-black leading-none" aria-hidden="true">+</span>{{ __('Kent sorunu bildir') }}</a>
                        <a href="{{ route('feed.index') }}"
                            class="inline-flex items-center justify-center rounded-full border border-neutral-300/90 bg-white px-4 py-2.5 text-[13px] font-bold text-neutral-800 hover:border-violet-300 hover:bg-violet-50/50">{{ __('Canlı akış') }}</a>
                    @else
                        <a href="{{ route('posts.create') }}"
                            class="inline-flex items-center justify-center gap-1 rounded-full bg-violet-600 px-5 py-2.5 text-[13px] font-bold text-white shadow-[0_6px_20px_-4px_rgba(91,33,182,0.45)] transition hover:bg-violet-700"><span class="text-base font-black leading-none" aria-hidden="true">+</span>{{ __('Kent sorunu bildir') }}</a>
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center rounded-full border border-neutral-300/90 bg-white px-4 py-2.5 text-[13px] font-bold text-neutral-800 hover:border-violet-200 hover:bg-violet-50/40">{{ __('Üye ol') }}</a>
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center rounded-full border border-neutral-300/90 bg-white px-4 py-2.5 text-[13px] font-bold text-neutral-800 hover:bg-neutral-50">{{ __('Giriş') }}</a>
                    @endauth
                    <a href="{{ route('contact') }}"
                        class="inline-flex items-center justify-center px-4 py-2 text-[13px] font-semibold text-neutral-600 underline underline-offset-4 hover:text-violet-700">{{ __('İletişim') }}</a>
                </div>
            </div>

            {{-- Geometrik kolaj — masaüstünde sınırlı boyut (dev görüntü taşmasını önler) --}}
            <div class="relative mx-auto isolate aspect-square w-full max-w-[min(100%,20rem)] sm:max-w-xs md:max-w-sm lg:max-w-[17.5rem] xl:max-w-[19rem]"
                aria-hidden="true">
                <div class="absolute -right-[6%] -top-[4%] h-[62%] w-[58%] overflow-hidden rounded-3xl bg-primary shadow-lg ring-2 ring-white sm:ring-[5px]">
                    <img src="{{ asset('images/hero/collage-woman-purple.jpg') }}" alt=""
                        class="h-full w-full object-cover object-[center_top] mix-blend-luminosity" loading="lazy" decoding="async">
                    <span class="absolute inset-0 bg-gradient-to-br from-[#8b7cff]/50 to-[#422dc7]/65 mix-blend-color"></span>
                </div>
                <div
                    class="absolute bottom-[26%] left-0 h-[43%] w-[43%] overflow-hidden rounded-full border-4 border-white bg-neutral-900 shadow-lg ring-2 ring-emerald-400/85 sm:border-[6px] sm:shadow-xl sm:ring-[3px]">
                    <img src="{{ asset('images/hero/collage-man-circle.jpg') }}" alt=""
                        class="h-full w-full object-cover object-top" loading="lazy" decoding="async">
                </div>
                <div
                    class="absolute bottom-[-2%] right-[6%] h-[54%] w-[52%] overflow-hidden rounded-[1.5rem] bg-gradient-to-br from-amber-300 to-orange-400 p-1 shadow-lg ring-2 ring-white sm:rounded-[2rem] sm:p-[6px] sm:shadow-xl sm:ring-[5px]">
                    <div class="h-full w-full overflow-hidden rounded-[1.55rem]">
                        <img src="{{ asset('images/hero/collage-woman-yellow.jpg') }}" alt=""
                            class="h-full w-full object-cover object-center" loading="lazy" decoding="async">
                    </div>
                </div>
                <div
                    class="absolute left-[4%] top-[8%] flex h-[28%] w-[36%] items-center justify-center gap-1.5 rounded-3xl bg-primary shadow-lg ring-2 ring-white/40">
                    <span class="h-2.5 w-2.5 rounded-full bg-white/95"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-white/65"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-white/40"></span>
                </div>
                <div
                    class="absolute left-[52%] top-[14%] flex h-[15%] w-[15%] min-h-[52px] min-w-[52px] items-center justify-center rounded-full bg-amber-300 shadow-md ring-[3px] ring-white">
                    <svg class="h-[55%] w-[55%] text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path
                            d="M11.049 3.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.887a1 1 0 00-1.176 0l-3.976 2.887c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
                <div class="pointer-events-none absolute left-[-14%] top-[52%] h-[42%] w-[36%] rounded-full bg-teal-200/45 blur-2xl"></div>
                <div class="pointer-events-none absolute bottom-[12%] left-[38%] h-[26%] w-[22%] rounded-full bg-neutral-900/85"></div>
                <div
                    class="pointer-events-none absolute -bottom-[8%] -left-[8%] h-[42%] w-[48%] rounded-full border-[14px] border-teal-200/80 border-l-transparent border-t-transparent">
                </div>
            </div>
        </div>

        @isset($platformStats)
            <div class="relative z-[1] border-t border-neutral-200/60 bg-white/70 backdrop-blur-md">
                <dl class="mx-auto grid max-w-[1250px] gap-3 px-5 py-4 sm:gap-5 sm:px-8 {{ (int) ($platformStats['resolved'] ?? 0) > 0 ? 'grid-cols-3' : 'grid-cols-2' }}">
                    @if ((int) ($platformStats['resolved'] ?? 0) > 0)
                    <div class="rounded-2xl bg-white px-2 py-3 text-center shadow-sm ring-1 ring-neutral-200/50 sm:rounded-3xl sm:py-4">
                        <dt class="text-[10px] font-bold uppercase tracking-wide text-neutral-500">{{ __('Çözülen şikâyet') }}</dt>
                        <dd class="mt-1 text-lg font-black tabular-nums text-neutral-900 sm:text-xl">{{ number_format((int) $platformStats['resolved'], 0, ',', '.') }}</dd>
                    </div>
                    @endif
                    <div class="rounded-2xl bg-white px-2 py-3 text-center shadow-sm ring-1 ring-neutral-200/50 sm:rounded-3xl sm:py-4">
                        <dt class="text-[10px] font-bold uppercase tracking-wide text-neutral-500">{{ __('Son 30 günde kayıt') }}</dt>
                        <dd class="mt-1 text-lg font-black tabular-nums text-neutral-900 sm:text-xl">{{ number_format((int) $platformStats['last30'], 0, ',', '.') }}</dd>
                    </div>
                    <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-white px-2 py-3 text-center shadow-sm ring-1 ring-emerald-200/50 sm:rounded-3xl sm:py-4">
                        <dt class="text-[10px] font-bold uppercase tracking-wide text-emerald-800">{{ __('Kanıtlı içerik') }}</dt>
                        <dd class="mt-1 text-lg font-black tabular-nums text-neutral-900 sm:text-xl">%{{ number_format((int) $platformStats['evidence_pct'], 0, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>
        @endisset
    </section>

    @include('partials.home-how-it-works')

    {{-- Kampanyalar — öne çıkan ızgarası --}}
    <section class="mb-10 scroll-mt-8 sm:mb-12" aria-labelledby="kampanya-csr-baslik">
        <div class="home-container">
            <header
                class="mb-5 flex flex-col gap-3 rounded-3xl border border-violet-200/60 bg-gradient-to-br from-violet-50/90 via-white to-emerald-50/40 px-5 py-5 shadow-[0_18px_44px_-32px_rgba(91,33,182,0.35)] sm:mb-6 sm:flex-row sm:items-center sm:justify-between sm:gap-4 sm:px-7 sm:py-6">
                <div class="min-w-0">
                    <h2 id="kampanya-csr-baslik" class="font-heading text-[clamp(1.05rem,2vw,1.35rem)] font-black leading-tight tracking-tight text-neutral-950">
                        {{ __('Toplumu güçlendiren kampanyalar') }}</h2>
                    <p class="mt-2 max-w-[42rem] text-[13px] leading-relaxed text-neutral-700">
                        {{ __('Öne çıkan çağrılar: destekçi sayısına göre sıralanır. Tek ekranda keşfet, detayda imzanı bırak.') }}</p>
                </div>
                <div class="flex shrink-0 flex-wrap items-center gap-2">
                    <a href="{{ route('campaigns.index') }}"
                        class="inline-flex items-center justify-center rounded-full bg-neutral-950 px-4 py-2.5 text-[12px] font-black text-white shadow-md hover:bg-neutral-800">{{ __('Tüm kampanyalar') }}</a>
                    @auth
                        <a href="{{ route('campaigns.create') }}"
                            class="inline-flex items-center justify-center rounded-full border-2 border-violet-300/80 bg-white px-4 py-2.5 text-[12px] font-bold text-violet-900 hover:bg-violet-50">{{ __('Kampanya başlat') }}</a>
                    @else
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center rounded-full border-2 border-neutral-300 bg-white px-4 py-2.5 text-[12px] font-bold text-neutral-900 hover:border-violet-300">{{ __('Katıl') }}</a>
                    @endauth
                </div>
            </header>

            @if (($featuredCampaigns ?? collect())->isEmpty())
                <div
                    class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-violet-200 bg-white px-4 py-10 text-center shadow-sm">
                    <p class="max-w-md text-[14px] font-semibold text-neutral-700">{{ __('Henüz öne çıkan kampanya yok. Liste sayfasından katılımcı olmayı sürdürebilirsin.') }}</p>
                    <a href="{{ route('campaigns.index') }}"
                        class="mt-4 rounded-full bg-violet-600 px-5 py-2.5 text-[12px] font-black text-white shadow-md hover:bg-violet-700">{{ __('Kampanya listesi') }}</a>
                </div>
            @else
                @php
                    $__cards = ($featuredCampaigns ?? collect())->take(5)->values();
                @endphp
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 xl:gap-4">
                    @foreach ($__cards as $idx => $c)
                        <a href="{{ route('campaigns.show', $c) }}"
                            class="group flex flex-col items-center rounded-2xl border border-neutral-200/90 bg-white px-4 pb-4 pt-5 text-center shadow-sm ring-1 ring-black/[0.04] transition hover:-translate-y-0.5 hover:border-violet-300 hover:shadow-md">
                            <div class="mb-2 flex min-h-[1.375rem] w-full items-start justify-center">
                                @if ($idx === 0)
                                    <span
                                        class="inline-flex rounded-full bg-violet-100 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-violet-800 ring-1 ring-violet-200/80">{{ __('Öne çıkan') }}</span>
                                @endif
                            </div>
                            <div
                                class="relative h-20 w-20 shrink-0 overflow-hidden rounded-full bg-gradient-to-br from-violet-100 to-emerald-50 shadow-inner ring-2 ring-white ring-offset-2 ring-offset-neutral-100 sm:h-[5.25rem] sm:w-[5.25rem]">
                                @if (filled($c->hero_image_url ?? null))
                                    <img src="{{ $c->hero_image_url }}" alt=""
                                        class="h-full w-full object-cover transition duration-300 group-hover:scale-110" loading="lazy">
                                @else
                                    <span class="flex h-full w-full items-center justify-center text-[13px] font-black text-violet-700"
                                        aria-hidden="true">{{ __('K') }}</span>
                                @endif
                            </div>
                            <h3 class="mt-4 line-clamp-2 min-h-[2.5rem] w-full text-[13px] font-black leading-snug text-neutral-950 group-hover:text-violet-800 sm:text-[14px]">
                                {{ $c->title }}</h3>
                            @if ($c->relationLoaded('city') && $c->city)
                                <p class="mt-1 line-clamp-1 text-[11px] font-semibold text-neutral-500">{{ $c->city->name }}</p>
                            @endif
                            <p class="mt-3 text-[12px] font-bold tabular-nums text-emerald-700">
                                {{ number_format(max(0, (int) $c->supporter_count)) }}
                                <span class="font-semibold text-neutral-500">{{ __('destek') }}</span>
                            </p>
                            <span class="mt-3 text-[11px] font-black uppercase tracking-wide text-violet-600 group-hover:text-violet-800">{{ __('Detay') }}
                                →</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
    
    @if (($trendingComplaints ?? collect())->isNotEmpty())
        @php
            $__trend = ($trendingComplaints ?? collect())->values();
            $__odd = $__trend->filter(fn ($p, $i) => $i % 2 === 0)->values();
            $__even = $__trend->filter(fn ($p, $i) => $i % 2 === 1)->values();
            if ($__even->isEmpty() && $__odd->isNotEmpty()) {
                $__even = $__odd;
            }
            $__dupOdd = $__odd->concat($__odd);
            $__dupEven = $__even->concat($__even);
            $__durOdd = max(40, min(112, max(1, $__odd->count()) * 14));
            $__durEven = max(44, min(118, max(1, $__even->count()) * 15));
        @endphp
        <section class="mb-12 scroll-mt-8 sm:mb-14" aria-labelledby="gundem-sikayet-baslik">
            <div
                class="home-fluid border-y border-neutral-200/70 bg-[linear-gradient(102deg,#fafafa_0%,#f4f4f5_35%,#ede9fe_100%)]">
                <div class="px-4 pt-6 pb-3 sm:px-6 sm:pt-8 sm:pb-4">
                    <header class="flex flex-col gap-3 rounded-2xl bg-white/80 px-5 py-4 shadow-sm ring-1 ring-neutral-200/60 sm:flex-row sm:items-center sm:justify-between sm:gap-4 sm:px-6 sm:py-5">
                        <div class="min-w-0">
                            <h2 id="gundem-sikayet-baslik"
                                class="font-heading text-[clamp(1.15rem,2.4vw,1.45rem)] font-black leading-tight tracking-tight text-neutral-950">
                                {{ __('Son gündemdeki şikayetler') }}</h2>
                            <p class="mt-2 text-[13px] leading-relaxed text-neutral-700">
                                {{ __('Destek, yorum ve takibe göre öne çıkan şikâyetler.') }}</p>
                        </div>
                        <a href="{{ route('feed.index') }}"
                            class="inline-flex shrink-0 items-center justify-center rounded-full bg-violet-600 px-4 py-2.5 text-[12px] font-black text-white shadow-md transition hover:bg-violet-700">{{ __('Tam akış') }}</a>
                    </header>
                </div>

                <div class="ds-marquee-wrap space-y-6 px-0 pb-4 pt-1 sm:space-y-7 sm:pb-5 sm:pt-1.5">
                    <div class="relative overflow-hidden">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 z-[1] w-6 bg-gradient-to-r from-neutral-100 to-transparent sm:w-10"
                            aria-hidden="true"></div>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 z-[1] w-6 bg-gradient-to-l from-indigo-100/80 to-transparent sm:w-10"
                            aria-hidden="true"></div>
                        <div class="ds-marquee-hover-pause">
                            <div class="ds-marquee-row gap-3 pl-1 pr-1 sm:gap-4 sm:pl-2 sm:pr-2 ds-marquee-animate-ltr"
                                style="animation-duration: {{ $__durOdd }}s;">
                                @foreach ($__dupOdd as $post)
                                    <div class="w-[min(16.25rem,calc(100vw-2rem))] shrink-0 sm:w-[18.5rem]">
                                        <x-home-trending-card :post="$post" :show-media="false" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="relative overflow-hidden">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 z-[1] w-6 bg-gradient-to-r from-neutral-100 to-transparent sm:w-10"
                            aria-hidden="true"></div>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 z-[1] w-6 bg-gradient-to-l from-indigo-100/80 to-transparent sm:w-10"
                            aria-hidden="true"></div>
                        <div class="ds-marquee-hover-pause">
                            <div class="ds-marquee-row gap-3 pl-1 pr-1 sm:gap-4 sm:pl-2 sm:pr-2 ds-marquee-animate-rtl"
                                style="animation-duration: {{ $__durEven }}s;">
                                @foreach ($__dupEven as $post)
                                    <div class="w-[min(16.25rem,calc(100vw-2rem))] shrink-0 sm:w-[18.5rem]">
                                        <x-home-trending-card :post="$post" :show-media="false" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @isset($platformStats)
        <section class="relative left-1/2 mb-14 w-screen max-w-[100vw] -translate-x-1/2 scroll-mt-8 bg-gradient-to-b from-neutral-900 via-neutral-900 to-neutral-950 py-10 text-white sm:mb-16 sm:py-12"
            aria-labelledby="sayilar-baslik">
            <div class="pointer-events-none absolute inset-0 opacity-40 bg-[radial-gradient(ellipse_at_80%_0%,rgba(167,139,250,0.35),transparent_55%),radial-gradient(ellipse_at_10%_80%,rgba(52,211,153,0.2),transparent_50%)]"
                aria-hidden="true"></div>
            <div class="relative mx-auto max-w-[1200px] px-5 sm:px-8">
                <header class="mx-auto mb-10 max-w-3xl px-2 text-center sm:mb-12">
                    <h2 id="sayilar-baslik"
                        class="font-heading text-[clamp(1.45rem,2.8vw,2rem)] font-black tracking-tight text-white">
                        {{ __('Sayılarla :name', ['name' => config('app.name')]) }}</h2>
                    <p class="mt-3 text-[14px] font-medium leading-relaxed text-neutral-300">{{ __('Platform ölçeği, moderasyon sonrası onaylı kayıtlara göre.') }}</p>
                </header>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6 lg:gap-4">
                <article
                    class="flex flex-col items-center rounded-3xl border border-white/10 bg-white/[0.07] px-3 py-6 text-center shadow-lg shadow-black/20 backdrop-blur-sm transition hover:border-emerald-400/40 hover:bg-white/[0.1] sm:py-7">
                    <span class="mb-3 text-violet-300" aria-hidden="true">
                        <svg class="mx-auto h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                    </span>
                    <p class="text-[11px] font-medium leading-snug text-neutral-300">{{ __('Bireysel üye sayısı') }}</p>
                    <p class="mt-1 text-xl font-black tabular-nums tracking-tight text-white sm:text-2xl">
                        {{ number_format((int) $platformStats['members'], 0, ',', '.') }}</p>
                </article>
                <article
                    class="flex flex-col items-center rounded-3xl border border-white/10 bg-white/[0.07] px-3 py-6 text-center shadow-lg shadow-black/20 backdrop-blur-sm transition hover:border-emerald-400/40 hover:bg-white/[0.1] sm:py-7">
                    <span class="mb-3 text-emerald-300" aria-hidden="true">
                        <svg class="mx-auto h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </span>
                    <p class="text-[11px] font-medium leading-snug text-neutral-300">{{ __('Kayıtlı birim') }}</p>
                    <p class="mt-1 text-xl font-black tabular-nums tracking-tight text-white sm:text-2xl">
                        {{ number_format((int) $platformStats['brands'], 0, ',', '.') }}</p>
                </article>
                <article
                    class="flex flex-col items-center rounded-3xl border border-white/10 bg-white/[0.07] px-3 py-6 text-center shadow-lg shadow-black/20 backdrop-blur-sm transition hover:border-emerald-400/40 hover:bg-white/[0.1] sm:py-7">
                    <span class="mb-3 text-violet-200" aria-hidden="true">
                        <svg class="mx-auto h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    <p class="text-[11px] font-medium leading-snug text-neutral-300">{{ __('Kanıtlı kayıt oranı') }}</p>
                    <p class="mt-1 text-xl font-black tabular-nums tracking-tight text-white sm:text-2xl">
                        %{{ number_format((int) $platformStats['evidence_pct'], 0, ',', '.') }}</p>
                </article>
                @if ((int) ($platformStats['resolved'] ?? 0) > 0)
                <article
                    class="flex flex-col items-center rounded-3xl border border-white/10 bg-white/[0.07] px-3 py-6 text-center shadow-lg shadow-black/20 backdrop-blur-sm transition hover:border-emerald-400/40 hover:bg-white/[0.1] sm:py-7">
                    <span class="mb-3 text-teal-300" aria-hidden="true">
                        <svg class="mx-auto h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </span>
                    <p class="text-[11px] font-medium leading-snug text-neutral-300">{{ __('Çözülen şikâyet') }}</p>
                    <p class="mt-1 text-xl font-black tabular-nums tracking-tight text-white sm:text-2xl">
                        {{ number_format((int) $platformStats['resolved'], 0, ',', '.') }}</p>
                </article>
                @endif
                <article
                    class="flex flex-col items-center rounded-3xl border border-white/10 bg-white/[0.07] px-3 py-6 text-center shadow-lg shadow-black/20 backdrop-blur-sm transition hover:border-emerald-400/40 hover:bg-white/[0.1] sm:py-7">
                    <span class="mb-3 text-fuchsia-300" aria-hidden="true">
                        <svg class="mx-auto h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </span>
                    <p class="text-[11px] font-medium leading-snug text-neutral-300">{{ __('Açık kampanya') }}</p>
                    <p class="mt-1 text-xl font-black tabular-nums tracking-tight text-white sm:text-2xl">
                        {{ number_format((int) ($platformStats['campaigns_live'] ?? 0), 0, ',', '.') }}</p>
                </article>
                <article
                    class="flex flex-col items-center rounded-3xl border border-white/10 bg-white/[0.07] px-3 py-6 text-center shadow-lg shadow-black/20 backdrop-blur-sm transition hover:border-emerald-400/40 hover:bg-white/[0.1] sm:py-7">
                    <span class="mb-3 text-indigo-300" aria-hidden="true">
                        <svg class="mx-auto h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </span>
                    <p class="text-[11px] font-medium leading-snug text-neutral-300">{{ __('Son 30 günde şikâyet') }}</p>
                    <p class="mt-1 text-xl font-black tabular-nums tracking-tight text-white sm:text-2xl">
                        {{ number_format((int) $platformStats['last30'], 0, ',', '.') }}</p>
                </article>
            </div>
            </div>
        </section>
    @endisset

    <section
        class="relative left-1/2 mb-12 w-screen max-w-[100vw] -translate-x-1/2 scroll-mt-10 overflow-hidden border-y border-neutral-200/80 bg-gradient-to-b from-neutral-50 via-white to-[#f0f4fa] py-10 text-neutral-900 sm:mb-14 sm:py-12"
        aria-labelledby="lider-sikayet-baslik">
        <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(125deg,rgba(16,185,129,0.08)_0%,transparent_42%),linear-gradient(225deg,rgba(139,92,246,0.07)_0%,transparent_45%)]"
            aria-hidden="true"></div>
        <div class="relative mx-auto max-w-[1200px] px-4 sm:px-6">
            <header class="max-w-3xl px-1 pb-8 sm:pb-10">
                <p class="text-[11px] font-black uppercase tracking-[0.22em] text-emerald-700">{{ __('Harita / yoğunluk') }}</p>
                <h2 id="lider-sikayet-baslik" class="mt-2 font-heading text-[clamp(1.25rem,2.5vw,1.75rem)] font-black tracking-tight text-neutral-950">
                    {{ __('Şikâyet yoğunluğu') }}</h2>
                <p class="mt-3 text-[14px] font-medium leading-relaxed text-neutral-700">{{ __('Onaylı kayıtlar üzerinden iller ve hedef kurumlar — çubuğun uzunluğu ilk sıradaki ile ölçeklenir.') }}</p>
            </header>
            @php
                $__cities = $topCitiesByComplaints ?? collect();
                $__maxCity = max(1, (int) $__cities->max('complaint_count'));
                $__insts = $topInstitutionsByComplaints ?? collect();
                $__maxInst = max(1, (int) $__insts->max('complaint_count'));
            @endphp
            <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                <div class="rounded-[1.75rem] border border-neutral-200/90 bg-white p-5 shadow-[0_20px_50px_-38px_rgba(15,23,42,0.18)] sm:p-6">
                    <h3 class="text-[15px] font-black text-neutral-950">{{ __('En çok sorun bildirilen iller') }}
                        <span class="font-semibold text-neutral-600">({{ __('ilk 10') }})</span></h3>
                    <ol class="mt-5 space-y-3">
                        @forelse ($__cities as $city)
                            @php
                                $w = max(8, round(100 * (int) $city->complaint_count / $__maxCity));
                            @endphp
                            <li>
                                <a href="{{ filled($city->slug ?? null) ? route('cities.show', $city) : route('feed.index', array_merge($feedWithoutGeo, ['city_id' => $city->id])) }}"
                                    class="group block rounded-2xl border border-neutral-100 bg-neutral-50/90 px-4 py-3 transition hover:border-emerald-300 hover:bg-emerald-50/50">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-[12px] font-black text-white">{{ $loop->iteration }}</span>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-[15px] font-black text-neutral-950 group-hover:text-emerald-900">{{ $city->name }}</p>
                                            @if (! empty($city->plate))
                                                <span class="mt-0.5 inline-block rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-bold tabular-nums text-emerald-900">{{ $city->plate }}</span>
                                            @endif
                                        </div>
                                        <span class="shrink-0 tabular-nums text-[15px] font-black text-emerald-800">{{ number_format((int) $city->complaint_count) }}</span>
                                    </div>
                                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-neutral-200/90" role="presentation">
                                        <span class="block h-full rounded-full bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-400 shadow-[0_0_12px_rgba(52,211,153,0.35)]"
                                            style="width: {{ $w }}%"></span>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="py-10 text-center text-[14px] font-medium text-neutral-600">{{ __('Henüz veri yok.') }}</li>
                        @endforelse
                    </ol>
                </div>
                <div class="rounded-[1.75rem] border border-violet-200/80 bg-white p-5 shadow-[0_20px_50px_-38px_rgba(91,33,182,0.12)] sm:p-6">
                    <h3 class="text-[15px] font-black text-neutral-950">{{ __('En çok şikâyeti olan kurumlar') }}
                        <span class="font-semibold text-neutral-600">({{ __('ilk 10') }})</span></h3>
                    <ul class="mt-5 divide-y divide-neutral-100">
                        @forelse ($__insts as $inst)
                            @php
                                $iw = max(6, round(100 * (int) $inst->complaint_count / $__maxInst));
                            @endphp
                            <li class="py-3.5 first:pt-0">
                                <a href="{{ route('institutions.show', $inst) }}" class="flex gap-3 rounded-xl p-2 transition hover:bg-violet-50/60">
                                    <img src="{{ $inst->displayLogoUrl() }}" alt="" width="48" height="48" loading="lazy"
                                        class="h-12 w-12 shrink-0 rounded-2xl object-cover ring-2 ring-neutral-100">
                                    <div class="min-w-0 flex-1">
                                        <p class="line-clamp-2 text-[14px] font-black leading-snug text-neutral-950">{{ $inst->name }}</p>
                                        <div class="mt-1 flex flex-wrap items-center gap-2">
                                            @if ($inst->verified)
                                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-black uppercase text-emerald-900">{{ __('onaylı') }}</span>
                                            @endif
                                            @if ($inst->relationLoaded('city') && $inst->city)
                                                <span class="text-[11px] font-semibold text-neutral-700">{{ $inst->city->name }}</span>
                                            @endif
                                        </div>
                                        <div class="mt-2 flex items-center gap-3">
                                            <div class="h-1.5 min-w-0 flex-1 overflow-hidden rounded-full bg-neutral-200">
                                                <span class="block h-full rounded-full bg-gradient-to-r from-violet-500 to-fuchsia-500"
                                                    style="width: {{ $iw }}%"></span>
                                            </div>
                                            <span class="shrink-0 text-[13px] font-black tabular-nums text-violet-900">{{ number_format((int) $inst->complaint_count) }}</span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="py-10 text-center text-[14px] font-medium text-neutral-600">{{ __('Henüz veri yok.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="home-panel scroll-mt-8 !rounded-3xl !border-neutral-200/70 !shadow-[0_12px_40px_-24px_rgba(15,23,42,0.1)]" aria-labelledby="vitrin-baslik">
        @php
            $__vv = isset($spotlightVideoPosts) ? $spotlightVideoPosts : collect();
            $__vi = isset($spotlightImagePosts) ? $spotlightImagePosts : collect();
            $__vs = isset($spotlightStories) ? $spotlightStories : collect();
        @endphp
        <header class="home-panel-head flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 id="vitrin-baslik" class="text-[clamp(1.15rem,2.2vw,1.5rem)] font-black tracking-tight text-neutral-950">
                    {{ __('Canlı vitrin') }}</h2>
                <p class="mt-2 max-w-2xl text-[13px] font-medium text-neutral-600">
                    {{ __('Video, fotoğraf ve hikâyelerden örnekler — içerik geldikçe burası otomatik güncellenir.') }}</p>
            </div>
            <a href="{{ route('feed.index') }}"
                class="shrink-0 rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-2 text-[12px] font-bold text-neutral-800 transition hover:border-emerald-300 hover:bg-emerald-50/40">{{ __('Akışa git') }}</a>
        </header>
        <div class="grid gap-4 lg:grid-cols-12 lg:items-stretch">
            <div class="space-y-3 lg:col-span-5">
                @if ($__vv->isEmpty())
                    @foreach (
                        [
                            'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?auto=format&fit=crop&w=960&q=80',
                            'https://images.unsplash.com/photo-1536240478700-b869070f9279?auto=format&fit=crop&w=960&q=80',
                        ] as $pvUrl)
                        <div class="relative overflow-hidden rounded-3xl bg-neutral-100 ring-1 ring-neutral-200">
                            <img src="{{ $pvUrl }}" alt=""
                                class="aspect-video w-full object-cover" loading="lazy" decoding="async">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/10 to-transparent"></div>
                            <span
                                class="absolute left-4 top-3 rounded-full bg-white/95 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-neutral-950">{{ __('video') }}</span>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span
                                    class="flex h-14 w-14 items-center justify-center rounded-full bg-white/95 text-xl shadow-xl ring-2 ring-black/5"
                                    aria-hidden="true">▶</span>
                            </div>
                            <p class="absolute bottom-3 left-4 right-4 text-[13px] font-bold text-white drop-shadow">
                                {{ __('İlk video şikâyetinle vitrin burada canlanır.') }}</p>
                        </div>
                    @endforeach
                @else
                    @foreach ($__vv as $post)
                        @php
                            $pmV = \App\Support\PostMediaPresenter::primary($post);
                        @endphp
                        @continue(! $pmV || ($pmV['type'] ?? '') !== 'video')
                        <a href="{{ route('posts.show', $post) }}"
                            class="group relative block overflow-hidden rounded-3xl bg-neutral-950 ring-1 ring-black/10 shadow-lg shadow-black/15">
                            @if (! empty($pmV['poster']))
                                <img src="{{ $pmV['poster'] }}" alt="" loading="lazy" decoding="async"
                                    class="aspect-video w-full object-cover opacity-95 transition duration-500 group-hover:scale-[1.03]">
                            @else
                                <div class="aspect-video w-full bg-gradient-to-br from-neutral-800 to-neutral-950"></div>
                            @endif
                            <span class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/25 to-transparent"></span>
                            <span
                                class="absolute left-4 top-3 rounded-full bg-white/95 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-neutral-950 shadow-sm">{{ __('video') }}</span>
                            <span class="absolute inset-0 flex items-center justify-center">
                                <span
                                    class="flex h-16 w-16 items-center justify-center rounded-full bg-white text-2xl shadow-2xl ring-2 ring-black/10 transition group-hover:scale-105"
                                    aria-hidden="true">▶</span>
                            </span>
                            <div class="absolute bottom-0 left-0 right-0 p-4">
                                <p class="line-clamp-2 text-[15px] font-black text-white drop-shadow-sm">{{ $post->title }}</p>
                                <p class="mt-1 text-[12px] font-semibold text-white/90">{{ $post->city?->name }}</p>
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>
            <div class="grid grid-cols-2 gap-2 sm:gap-3 lg:col-span-4">
                @if ($__vi->isEmpty())
                    @foreach (
                        [
                            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=640&q=80',
                            'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=640&q=80',
                            'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=640&q=80',
                            'https://images.unsplash.com/photo-1600573472592-401b489a3cdc?auto=format&fit=crop&w=640&q=80',
                        ] as $tiUrl)
                        <div class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-neutral-100 ring-1 ring-black/5">
                            <img src="{{ $tiUrl }}" alt="" loading="lazy" decoding="async"
                                class="h-full w-full object-cover">
                            <span
                                class="absolute left-2 top-2 rounded-full bg-white/90 px-2 py-0.5 text-[9px] font-black uppercase text-neutral-900 shadow">{{ __('foto') }}</span>
                        </div>
                    @endforeach
                @else
                    @php $vitrinTilesShown = 0; @endphp
                    @foreach ($__vi->take(16) as $post)
                        @if ($vitrinTilesShown >= 4)
                            @break
                        @endif
                        @php
                            $pmI = \App\Support\PostMediaPresenter::primary($post);
                            if (! $pmI) {
                                continue;
                            }
                            $isVid = ($pmI['type'] ?? '') === 'video';
                            $thumb = $isVid ? trim((string) ($pmI['poster'] ?? '')) : trim((string) ($pmI['url'] ?? ''));
                            if ($thumb === '') {
                                continue;
                            }
                            $vitrinTilesShown++;
                        @endphp
                        <a href="{{ route('posts.show', $post) }}"
                            class="relative block aspect-[4/3] overflow-hidden rounded-2xl bg-neutral-100 ring-1 ring-black/5 transition hover:ring-emerald-200">
                            <img src="{{ $thumb }}" alt="" loading="lazy" decoding="async"
                                class="h-full w-full object-cover transition hover:scale-105">
                            <span
                                class="absolute left-2 top-2 rounded-full bg-white/90 px-2 py-0.5 text-[9px] font-black uppercase text-neutral-900 shadow">{{ $isVid ? __('video') : __('foto') }}</span>
                        </a>
                    @endforeach
                    @if ($vitrinTilesShown === 0)
                        @foreach (
                            [
                                'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=640&q=80',
                                'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=640&q=80',
                                'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=640&q=80',
                                'https://images.unsplash.com/photo-1600573472592-401b489a3cdc?auto=format&fit=crop&w=640&q=80',
                            ] as $tiUrl)
                            <div class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-neutral-100 ring-1 ring-black/5">
                                <img src="{{ $tiUrl }}" alt="" loading="lazy" decoding="async"
                                    class="h-full w-full object-cover">
                                <span
                                    class="absolute left-2 top-2 rounded-full bg-white/90 px-2 py-0.5 text-[9px] font-black uppercase text-neutral-900 shadow">{{ __('foto') }}</span>
                            </div>
                        @endforeach
                    @endif
                @endif
            </div>
            <div
                class="flex flex-col rounded-3xl border border-neutral-200/90 bg-white p-4 shadow-sm lg:col-span-3">
                <p class="text-[11px] font-black uppercase tracking-wider text-emerald-800">{{ __('Kent hikâyeleri') }}</p>
                <p class="mt-1 text-[12px] font-medium text-neutral-600">{{ __('Kısa görsel günlükler — platform genelinden.') }}</p>
                @if ($__vs->isEmpty())
                    <p class="mt-6 flex-1 text-[13px] font-medium text-neutral-500">{{ __('Henüz hikâye yok; ilk paylaşım senin olsun.') }}</p>
                @else
                    <div class="mt-4 flex flex-wrap gap-3">
                        @foreach ($__vs->take(10) as $story)
                            @php
                                $storyUserName = trim((string) ($story->user?->name ?? '?'));
                                $su = mb_strlen($storyUserName) >= 2
                                    ? mb_strtoupper(mb_substr($storyUserName, 0, 2))
                                    : mb_strtoupper(mb_substr($storyUserName, 0, 1).'?');
                            @endphp
                            <div class="text-center">
                                <div
                                    class="relative mx-auto h-[4.25rem] w-[4.25rem] rounded-full bg-gradient-to-tr from-amber-300 via-teal-500 to-indigo-600 p-[2.5px] shadow-md shadow-black/15">
                                    <div class="rounded-full bg-white p-[3px]">
                                        @if ($story->media_url)
                                            <img src="{{ $story->media_url }}" alt="" loading="lazy" decoding="async"
                                                class="h-[3.82rem] w-[3.82rem] rounded-full object-cover shadow-inner shadow-black/15">
                                        @else
                                            <span
                                                class="flex h-[3.82rem] w-[3.82rem] items-center justify-center rounded-full bg-gradient-to-br from-emerald-400 to-teal-700 text-sm font-black text-white">{{ mb_substr($su, 0, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <p class="mx-auto mt-2 max-w-[5.5rem] line-clamp-2 text-[10px] font-bold leading-snug text-neutral-900">
                                    {{ \Illuminate\Support\Str::limit((string) $story->description, 56) }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if ($stories->isNotEmpty())
        <section id="hikayeler"
            class="home-panel mb-10 scroll-mt-[6.5rem] rounded-3xl border border-emerald-200/60 bg-gradient-to-br from-white to-emerald-50/30 px-5 py-6 shadow-[0_14px_44px_-32px_rgba(16,185,129,0.35)] sm:px-7 sm:py-7"
            aria-label="{{ __('Hikâye şeridi') }}">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-emerald-100/80 pb-4">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-wider text-emerald-800">{{ __('Senin bölgen') }}</p>
                    <h2 class="mt-1 text-[clamp(1.1rem,2vw,1.35rem)] font-black text-neutral-950">{{ __('Hikâyeler') }}</h2>
                </div>
                <p class="max-w-md text-[13px] font-medium leading-relaxed text-neutral-700">
                    {{ ! empty($geoActive) ? ((! empty($relaxNearby)) ? __('Konum kutusu açık — yakın ve aynı ili genişleterek gez.') : __('GPS sıralaması açık — il seçiminle birlikte çalışır.')) : __('Konumu açarsan hikâyeler yakınlığa göre sıralanır.') }}</p>
            </div>
            <div class="-mx-1 mt-5 flex gap-5 overflow-x-auto pb-2 pt-1 [scrollbar-width:thin]" style="-webkit-overflow-scrolling:touch">
                @foreach ($stories as $story)
                    @php
                        $storyUserName = trim((string) ($story->user?->name ?? '?'));
                        $su = mb_strlen($storyUserName) >= 2
                            ? mb_strtoupper(mb_substr($storyUserName, 0, 2))
                            : mb_strtoupper(mb_substr($storyUserName, 0, 1).'?');
                    @endphp
                    <article class="shrink-0 text-center">
                        <div class="relative mx-auto h-[4.95rem] w-[4.95rem] rounded-full bg-gradient-to-tr from-amber-300 via-teal-500 to-indigo-600 p-[2.5px] shadow-md shadow-black/20">
                            <div class="rounded-full bg-white p-[3px]">
                                @if ($story->media_url)
                                    <img src="{{ $story->media_url }}" alt="" loading="lazy"
                                        class="h-[4.45rem] w-[4.45rem] rounded-full object-cover shadow-inner shadow-black/20">
                                @else
                                    <span
                                        class="flex h-[4.45rem] w-[4.45rem] items-center justify-center rounded-full bg-gradient-to-br from-emerald-400 to-teal-700 text-sm font-black text-white">{{ mb_substr($su, 0, 2) }}</span>
                                @endif
                            </div>
                        </div>
                        <p class="mx-auto mt-2 max-w-[5.85rem] line-clamp-2 text-[10px] font-bold leading-snug text-neutral-900">
                            {{ \Illuminate\Support\Str::limit($story->description, 70) }}</p>
                        @if ($story->relationLoaded('city') && $story->city)
                            <span class="text-[10px] font-semibold text-emerald-800">{{ $story->city->name }}</span>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="relative left-1/2 mb-6 w-screen max-w-[100vw] -translate-x-1/2 overflow-hidden rounded-none border-y border-violet-200/50 bg-gradient-to-r from-violet-600 via-indigo-600 to-emerald-500 px-5 py-10 text-white sm:px-8 sm:py-12"
        aria-labelledby="akis-cta-baslik">
        <div class="pointer-events-none absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23fff\' fill-opacity=\'0.06\'%3E%3Cpath d=\'M20 0L40 20 20 40 0 20z\'/%3E%3C/g%3E%3C/svg%3E')] opacity-90"
            aria-hidden="true"></div>
        <div class="relative mx-auto flex max-w-[1100px] flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-xl">
                <h2 id="akis-cta-baslik" class="font-heading text-[clamp(1.35rem,2.5vw,1.85rem)] font-black leading-tight">
                    {{ __('Tüm kent kayıtları Akış sayfasında') }}</h2>
                <p class="mt-3 text-[14px] font-medium leading-relaxed text-white/90">
                    {{ __('İl, kategori ve sıralama ile listele; sayfa sonunda “daha fazla yükle” ile ilerle — sayfalama özeti burada gösterilmez.') }}</p>
            </div>
            <div class="flex flex-shrink-0 flex-wrap items-center gap-3">
                <a href="{{ route('feed.index') }}"
                    class="inline-flex min-w-[11rem] items-center justify-center rounded-full bg-white px-6 py-3.5 text-[13px] font-black text-violet-800 shadow-lg transition hover:bg-neutral-100">{{ __('Akışa git') }}</a>
                <a href="{{ route('posts.create') }}"
                    class="inline-flex items-center justify-center rounded-full border-2 border-white/80 bg-white/10 px-5 py-3 text-[13px] font-bold text-white backdrop-blur hover:bg-white/20">{{ __('Bildir') }}</a>
            </div>
        </div>
    </section>
@endsection
