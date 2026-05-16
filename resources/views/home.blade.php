@extends('layouts.app')

@section('title', config('app.name').' • '.__('Kent sorun bildir'))

@isset($platformStats)
    @push('prepend_header')
        <div class="border-b border-[#252830]/80 bg-[#1f232d] px-4 py-2 text-[13px] sm:px-5 lg:py-2.5">
            <div class="mx-auto flex max-w-[1250px] flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="flex flex-wrap items-end gap-x-3 gap-y-0.5 leading-none text-neutral-100">
                    <span class="translate-y-[1px] text-[13px] font-medium text-neutral-300">{{ __('Toplam çözüm sayısı') }}</span>
                    <strong class="text-[clamp(1.35rem,3.5vw,1.95rem)] font-black tabular-nums tracking-tight text-[#5eead4]">
                        {{ number_format((int) $platformStats['resolved'], 0, ',', '.') }}</strong>
                </p>
                <div class="flex flex-wrap items-center gap-3">
                    @if (($platformStats['campaigns_live'] ?? 0) > 0)
                        <a href="{{ route('campaigns.index') }}"
                            class="inline-flex items-center gap-2 rounded-full bg-[#7c6cf5]/20 px-3 py-1.5 text-[11px] font-black uppercase tracking-wider text-[#c4beff] ring-2 ring-[#c4beff]/30 hover:bg-[#7c6cf5]/30">
                            <span aria-hidden="true">✶</span>
                            {{ __(':n kampanya • sosyal sorumluluk', ['n' => number_format((int) $platformStats['campaigns_live'])]) }}
                        </a>
                    @endif
                    <span class="text-[13px] font-medium leading-snug text-neutral-400">{{ __('Kent sorunları ve güncellenen çözümleri takip et') }}</span>
                    <a href="{{ route('home') }}#liste-sikayetleri"
                        class="inline-flex items-center gap-2 rounded-full bg-[#34d399] px-3.5 py-1.5 text-[11px] font-black uppercase tracking-wider text-neutral-950 shadow-sm ring-2 ring-white/10 hover:bg-emerald-300">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-white opacity-50"></span>
                            <span class="relative inline-block h-2 w-2 rounded-full bg-white"></span>
                        </span>
                        {{ __('Canlı izle') }}
                    </a>
                </div>
            </div>
        </div>
    @endpush
@endisset

@section('content')
    @php
        $feedPreserve = array_filter([
            'city_id' => $activeCityId,
            'category_id' => request()->integer('category_id') ?: null,
            'q' => $searchQuery !== '' ? $searchQuery : null,
            'lat' => $nearLat,
            'lng' => $nearLng,
            'relax_city' => $nearLat !== null && $nearLng !== null ? ((! empty($relaxNearby)) ? '1' : '0') : null,
        ], fn ($v) => $v !== null && $v !== '');
    @endphp

    {{-- Kent odaklı hero — tam görünüm genişliği --}}
    <section
        class="relative left-1/2 z-0 mb-10 w-screen max-w-[100vw] -translate-x-1/2 rounded-none border-y border-neutral-200/85 bg-[#e8ecf2] shadow-[inset_0_1px_0_rgba(255,255,255,0.72)] outline outline-1 outline-black/[0.03]"
        aria-labelledby="hero-baslik">
        <div
            class="pointer-events-none absolute inset-x-8 top-[15%] h-52 rounded-full bg-sky-200/35 blur-[48px]"
            aria-hidden="true"></div>
        <div class="relative z-[1] mx-auto grid max-w-[1250px] gap-10 px-5 py-10 sm:gap-12 sm:px-8 sm:py-14 lg:grid-cols-[minmax(0,1.06fr)_minmax(260px,.94fr)] lg:items-center lg:gap-14">
            <div>
                <h1 id="hero-baslik" class="text-[clamp(1.75rem,3.8vw,2.85rem)] font-black leading-[1.07] tracking-tight text-neutral-800">
                    {{ __('Kent yaşamına çözüm için') }}
                    {{ config('app.name') }}</h1>
                <p class="mt-4 max-w-lg text-[15px] leading-relaxed font-medium text-neutral-600">{{ __('Kaldırım, çevre, ulaşım ve benzeri kent yaşamı bildirimi; fotoğraf ve konum ile kuruma görünür kılın.') }}</p>
                @if (! empty($geoActive))
                    <p class="mt-3 max-w-lg rounded-xl border border-neutral-300/70 bg-black/[0.04] px-3 py-2 text-[13px] font-semibold text-neutral-900">
                        {{ __('Konum sıralaması açık.') }}</p>
                @endif

                <form method="get" action="{{ route('home') }}" role="search"
                    class="mt-9 flex flex-col gap-2 rounded-full border border-neutral-200/70 bg-white p-2 shadow-[0_12px_40px_-22px_rgba(15,23,42,0.35)] sm:flex-row sm:items-center sm:rounded-full">
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
                        class="shrink-0 rounded-full bg-[#34d399] px-7 py-3.5 text-[14px] font-black text-neutral-950 shadow-md shadow-teal-500/35 transition hover:bg-emerald-300 sm:self-stretch md:rounded-full md:py-3">
                        {{ __('Ara') }}
                    </button>
                </form>

                <div class="mt-7 flex flex-wrap items-center gap-2">
                    @auth
                        <a href="{{ route('complaints.quick.create') }}"
                            class="inline-flex items-center justify-center rounded-full bg-[#6C5CE7] px-5 py-2.5 text-[13px] font-bold text-white shadow-lg shadow-[#6C5CE7]/35 transition hover:bg-[#5b4dcf]">{{ __('Kent sorunu bildir') }}</a>
                        <a href="#liste-sikayetleri"
                            class="inline-flex items-center justify-center rounded-full border border-neutral-300/90 bg-transparent px-4 py-2.5 text-[13px] font-bold text-neutral-800 hover:bg-white/80">{{ __('Canlı akış') }}</a>
                    @else
                        <a href="{{ route('complaints.quick.create') }}"
                            class="inline-flex items-center justify-center rounded-full bg-[#6C5CE7] px-5 py-2.5 text-[13px] font-bold text-white shadow-lg shadow-[#6C5CE7]/35 transition hover:bg-[#5b4dcf]">{{ __('Kent sorunu bildir') }}</a>
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center rounded-full border border-neutral-300/90 bg-transparent px-4 py-2.5 text-[13px] font-bold text-neutral-800 hover:bg-white/80">{{ __('Üye ol') }}</a>
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center rounded-full border border-neutral-300/90 bg-transparent px-4 py-2.5 text-[13px] font-bold text-neutral-800 hover:bg-white/80">{{ __('Giriş') }}</a>
                    @endauth
                    <a href="{{ route('contact') }}"
                        class="inline-flex items-center justify-center px-4 py-2 text-[13px] font-semibold text-neutral-600 underline underline-offset-4 hover:text-[#6C5CE7]">{{ __('İletişim') }}</a>
                </div>
            </div>

            {{-- Geometrik kolaj — public/images/hero (Unsplash) --}}
            <div class="relative mx-auto isolate aspect-square w-full max-w-[min(100%,460px)] sm:max-w-md lg:max-w-none" aria-hidden="true">
                <div class="absolute -right-[6%] -top-[4%] h-[62%] w-[58%] overflow-hidden rounded-3xl bg-[#6C5CE7] shadow-xl ring-[5px] ring-white">
                    <img src="{{ asset('images/hero/collage-woman-purple.jpg') }}" alt=""
                        class="h-full w-full object-cover object-[center_top] mix-blend-luminosity" loading="lazy" decoding="async">
                    <span class="absolute inset-0 bg-gradient-to-br from-[#8b7cff]/50 to-[#422dc7]/65 mix-blend-color"></span>
                </div>
                <div
                    class="absolute left-0 bottom-[26%] h-[43%] w-[43%] overflow-hidden rounded-full border-[6px] border-white bg-neutral-900 shadow-xl ring-[3px] ring-[#34d399]/85">
                    <img src="{{ asset('images/hero/collage-man-circle.jpg') }}" alt=""
                        class="h-full w-full object-cover object-top" loading="lazy" decoding="async">
                </div>
                <div
                    class="absolute bottom-[-2%] right-[6%] h-[54%] w-[52%] overflow-hidden rounded-[2rem] bg-gradient-to-br from-amber-300 to-orange-400 p-[6px] shadow-xl ring-[5px] ring-white">
                    <div class="h-full w-full overflow-hidden rounded-[1.55rem]">
                        <img src="{{ asset('images/hero/collage-woman-yellow.jpg') }}" alt=""
                            class="h-full w-full object-cover object-center" loading="lazy" decoding="async">
                    </div>
                </div>
                <div
                    class="absolute left-[4%] top-[8%] flex h-[28%] w-[36%] items-center justify-center gap-1.5 rounded-3xl bg-[#6C5CE7] shadow-lg ring-2 ring-white/40">
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
                <div class="pointer-events-none absolute left-[-14%] top-[52%] h-[42%] w-[36%] rounded-full bg-teal-200/45 blur-2xl">
                </div>
                <div class="pointer-events-none absolute bottom-[12%] left-[38%] h-[26%] w-[22%] rounded-full bg-neutral-900/85"></div>
                <div
                    class="pointer-events-none absolute -bottom-[8%] -left-[8%] h-[42%] w-[48%] rounded-full border-[14px] border-teal-200/80 border-l-transparent border-t-transparent">
                </div>
            </div>
        </div>
    </section>

    {{-- Toplumsal sorumluluk: inline gradient + theme sınıfları — CSS derlenmese bile okunur --}}
    <section
        class="relative left-1/2 z-0 mb-10 w-screen max-w-[100vw] -translate-x-1/2 border-y border-indigo-950 bg-indigo-950 px-5 py-7 text-white sm:px-8 sm:py-10"
        style="background: linear-gradient(145deg, #1e1b4b 0%, #4338ca 48%, #312e81 100%); color: #ffffff;"
        aria-labelledby="kampanya-csr-baslik">
        <div class="relative z-10 mx-auto grid max-w-[1250px] gap-8 lg:grid-cols-[minmax(0,1.08fr)_minmax(260px,.92fr)] lg:items-center">
            <div>
                <p
                    class="inline-flex rounded-full px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-white shadow-sm"
                    style="background: rgba(0,0,0,0.35); box-shadow: inset 0 0 0 2px rgba(255,255,255,0.22);">
                    {{ __('Sosyal sorumluluk') }}</p>
                <h2 id="kampanya-csr-baslik"
                    class="mt-4 text-2xl font-black leading-tight tracking-tight text-white sm:text-[clamp(1.25rem,2.6vw,1.95rem)]"
                    style="color: #ffffff; text-shadow: 0 2px 20px rgba(0,0,0,0.45);">
                    {{ __('Toplumu güçlendiren kampanyalar — süper yönetici onayı ile yayında.') }}</h2>
                <p class="mt-4 max-w-2xl text-[15px] font-semibold leading-relaxed" style="color: rgba(255,255,255,0.96);">
                    {{ __('Kent sorununun yanında, dayanışma ve kolektif aksiyon içeren kampanyaları burada listeleyebilir, destekçi kitle oluşturabilirsiniz. Her kampanya süper yönetici incelemesinden sonra herkese açılır.') }}</p>
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <a href="{{ route('campaigns.index') }}"
                        class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-[13px] font-black text-indigo-900 shadow-lg transition hover:bg-indigo-50">
                        {{ __('Kampanyaları keşfet') }}</a>
                    @auth
                        <a href="{{ route('campaigns.create') }}"
                            class="inline-flex items-center justify-center rounded-full border-2 border-white bg-white/15 px-5 py-3 text-[13px] font-bold text-white shadow-sm transition hover:bg-white/25">{{ __('Yeni kampanya başlat') }}</a>
                    @else
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center rounded-full border-2 border-white bg-white/15 px-5 py-3 text-[13px] font-bold text-white shadow-sm transition hover:bg-white/25">{{ __('Katıl ve kampanya aç') }}</a>
                    @endauth
                </div>
            </div>
            <div class="rounded-3xl border-2 border-white/30 bg-indigo-950/80 p-5 shadow-2xl backdrop-blur-md sm:p-6"
                style="background: rgba(15, 23, 42, 0.55); border-color: rgba(255,255,255,0.28);">
                <p class="text-[11px] font-black uppercase tracking-wider text-emerald-300">{{ __('Canlı kampanyalar') }}</p>
                @if (($featuredCampaigns ?? collect())->isEmpty())
                    <p class="mt-4 text-[15px] font-semibold leading-snug text-white">{{ __('Onaylı kampanya oluştukça burada listelenecek. İlk destekçilerden olun!') }}</p>
                    <a href="{{ route('campaigns.index') }}"
                        class="mt-4 inline-flex text-[13px] font-black text-emerald-300 underline decoration-4 underline-offset-4 hover:text-white">{{ __('Tüm kampanyalar') }}</a>
                @else
                    <ul class="mt-4 space-y-3">
                        @foreach ($featuredCampaigns->take(4) as $c)
                            <li class="rounded-2xl border border-white/20 bg-white/10 px-4 py-3 backdrop-blur-sm">
                                <a href="{{ route('campaigns.show', $c) }}"
                                    class="block text-[15px] font-black tracking-tight text-white hover:text-emerald-200">{{ $c->title }}</a>
                                <p class="mt-1 line-clamp-2 text-[13px] font-medium" style="color: rgba(255,255,255,0.9);">
                                    {{ trim((string) ($c->excerpt ?? '')) !== '' ? \Illuminate\Support\Str::limit(trim((string) $c->excerpt), 120) : \Illuminate\Support\Str::limit(strip_tags((string) $c->description), 120) }}</p>
                                <p class="mt-2 text-[11px] font-bold uppercase tracking-wide text-emerald-300">
                                    {{ number_format(max(0, (int) $c->supporter_count)) }}
                                    {{ __('destekçi') }}
                                    @if ($c->goal_supporters)
                                        · {{ __('hedef :n', ['n' => number_format((int) $c->goal_supporters)]) }}
                                    @endif
                                </p>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('campaigns.index') }}"
                        class="mt-5 inline-flex w-full justify-center rounded-2xl bg-emerald-400 px-4 py-3 text-[12px] font-black uppercase tracking-wider text-neutral-950 shadow-lg ring-2 ring-black/25 hover:bg-emerald-300">{{ __('Liste ve filtre →') }}</a>
                @endif
            </div>
        </div>
    </section>

    @isset($platformStats)
        <section class="-mx-3 mb-6 sm:-mx-4" aria-labelledby="sayilar-baslik">
            <h2 id="sayilar-baslik" class="mb-4 text-center text-[15px] font-semibold text-neutral-500">
                {{ __('Sayılarla :name', ['name' => config('app.name')]) }}</h2>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6 lg:gap-4">
                <article
                    class="flex flex-col items-center rounded-2xl bg-white px-3 py-5 text-center shadow-[0_4px_14px_-4px_rgba(15,23,42,0.12)] ring-1 ring-neutral-100">
                    <span class="mb-3 text-[#6C5CE7]" aria-hidden="true">
                        <svg class="mx-auto h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                    </span>
                    <p class="text-[11px] font-medium leading-snug text-neutral-500">{{ __('Bireysel üye sayısı') }}</p>
                    <p class="mt-1 text-xl font-black tabular-nums tracking-tight text-neutral-900 sm:text-2xl">
                        {{ number_format((int) $platformStats['members'], 0, ',', '.') }}</p>
                </article>
                <article
                    class="flex flex-col items-center rounded-2xl bg-white px-3 py-5 text-center shadow-[0_4px_14px_-4px_rgba(15,23,42,0.12)] ring-1 ring-neutral-100">
                    <span class="mb-3 text-emerald-500" aria-hidden="true">
                        <svg class="mx-auto h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </span>
                    <p class="text-[11px] font-medium leading-snug text-neutral-500">{{ __('Kayıtlı birim') }}</p>
                    <p class="mt-1 text-xl font-black tabular-nums tracking-tight text-neutral-900 sm:text-2xl">
                        {{ number_format((int) $platformStats['brands'], 0, ',', '.') }}</p>
                </article>
                <article
                    class="flex flex-col items-center rounded-2xl bg-white px-3 py-5 text-center shadow-[0_4px_14px_-4px_rgba(15,23,42,0.12)] ring-1 ring-neutral-100">
                    <span class="mb-3 text-[#6C5CE7]" aria-hidden="true">
                        <svg class="mx-auto h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    <p class="text-[11px] font-medium leading-snug text-neutral-500">{{ __('Kanıtlı kayıt oranı') }}</p>
                    <p class="mt-1 text-xl font-black tabular-nums tracking-tight text-neutral-900 sm:text-2xl">
                        %{{ number_format((int) $platformStats['evidence_pct'], 0, ',', '.') }}</p>
                </article>
                <article
                    class="flex flex-col items-center rounded-2xl bg-white px-3 py-5 text-center shadow-[0_4px_14px_-4px_rgba(15,23,42,0.12)] ring-1 ring-neutral-100">
                    <span class="mb-3 text-emerald-500" aria-hidden="true">
                        <svg class="mx-auto h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </span>
                    <p class="text-[11px] font-medium leading-snug text-neutral-500">{{ __('Çözülen şikâyet') }}</p>
                    <p class="mt-1 text-xl font-black tabular-nums tracking-tight text-neutral-900 sm:text-2xl">
                        {{ number_format((int) $platformStats['resolved'], 0, ',', '.') }}</p>
                </article>
                <article
                    class="flex flex-col items-center rounded-2xl bg-white px-3 py-5 text-center shadow-[0_4px_14px_-4px_rgba(15,23,42,0.12)] ring-1 ring-neutral-100">
                    <span class="mb-3 text-indigo-500" aria-hidden="true">
                        <svg class="mx-auto h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </span>
                    <p class="text-[11px] font-medium leading-snug text-neutral-500">{{ __('Açık kampanya') }}</p>
                    <p class="mt-1 text-xl font-black tabular-nums tracking-tight text-neutral-900 sm:text-2xl">
                        {{ number_format((int) ($platformStats['campaigns_live'] ?? 0), 0, ',', '.') }}</p>
                </article>
                <article
                    class="flex flex-col items-center rounded-2xl bg-white px-3 py-5 text-center shadow-[0_4px_14px_-4px_rgba(15,23,42,0.12)] ring-1 ring-neutral-100">
                    <span class="mb-3 text-[#6C5CE7]" aria-hidden="true">
                        <svg class="mx-auto h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </span>
                    <p class="text-[11px] font-medium leading-snug text-neutral-500">{{ __('Son 30 günde şikâyet') }}</p>
                    <p class="mt-1 text-xl font-black tabular-nums tracking-tight text-neutral-900 sm:text-2xl">
                        {{ number_format((int) $platformStats['last30'], 0, ',', '.') }}</p>
                </article>
            </div>
        </section>
    @endisset

    {{-- üç sütun: sol kısayol • akış • sağ araç kutuları --}}
    <div class="grid gap-5 xl:grid-cols-[minmax(210px,14rem)_minmax(0,40rem)_minmax(248px,16rem)] xl:justify-center">
        <aside class="hidden xl:block xl:sticky xl:top-24 xl:h-fit">
            <nav class="space-y-4 rounded-2xl bg-white p-4 shadow-[0_2px_8px_rgba(0,0,0,.07)] ring-1 ring-neutral-100" aria-label="{{ __('Kenar çubuğu') }}">
                <p class="px-1 text-[11px] font-black uppercase tracking-widest text-neutral-400">{{ __('Kısayollar') }}</p>
                <div class="space-y-1">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl bg-[#eef0f3] px-3 py-2.5 text-sm font-bold text-neutral-900">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-neutral-900 text-[11px] text-white">⇄</span>
                        {{ __('Ana akış') }}
                    </a>
                    <a href="{{ route('campaigns.index') }}"
                        class="flex items-center gap-3 rounded-xl bg-indigo-50 px-3 py-2.5 text-sm font-bold text-indigo-950 ring-2 ring-indigo-100 hover:bg-indigo-100">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-[11px] text-white">✶</span>
                        {{ __('Sosyal kampanyalar') }}</a>
                    @auth
                        <a href="{{ route('complaints.quick.create') }}"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold text-neutral-800 hover:bg-[#eef0f3]">{{ __('Kent sorunu bildir') }}</a>
                        <a href="{{ route('panel.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold text-neutral-800 hover:bg-[#eef0f3]">{{ __('Profilim') }}</a>
                    @endauth
                    <a href="{{ route('contact') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-[#eef0f3]">{{ __('Destek iletişim') }}</a>
                </div>
                <div class="border-t border-neutral-100 pt-4">
                    <p class="px-1 text-[11px] font-black uppercase tracking-widest text-neutral-400">{{ __('Kategori') }}</p>
                    <ul class="mt-2 space-y-0.5">
                        @foreach ($categories->take(8) as $cat)
                            <li>
                                <a href="{{ route('home', array_merge($feedPreserve, ['category_id' => $cat->id])) }}"
                                    class="block truncate rounded-xl px-2 py-2 text-[13px] font-semibold text-neutral-800 hover:bg-[#eef0f3]">{{ $cat->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </nav>
        </aside>

        <div id="liste-sikayetleri-anchor" class="mx-auto min-w-0 space-y-4 xl:mx-0">
            @if ($stories->isNotEmpty())
                <section id="hikayeler"
                    class="scroll-mt-[6.5rem] rounded-2xl bg-white px-5 py-4 shadow-[0_2px_8px_rgba(0,0,0,.07)] ring-1 ring-neutral-100"
                    aria-label="{{ __('Hikâye şeridi') }}">
                    <div class="flex flex-wrap items-center justify-between gap-2 pb-3">
                        <h2 class="text-[14px] font-black text-neutral-900">{{ __('Hikâyeler') }}</h2>
                        <p class="max-w-xl text-[12px] font-medium text-neutral-600">
                            {{ ! empty($geoActive) ? ((! empty($relaxNearby)) ? __('Konum kutusu • geniş yakın sırayı keşfedin') : __('GPS sıralaması bağlı • il seçimiyle tutuyor')) : __('Tarayıcı konumu verilirse sıralama açılır') }}
                        </p>
                    </div>
                    <div class="-mx-1 flex gap-5 overflow-x-auto pb-4 pt-1 [scrollbar-width:thin]" style="-webkit-overflow-scrolling:touch">
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

            {{-- Compact filtre (Facebook bildir kutusu sığlığında) --}}
            <section class="rounded-2xl bg-white p-4 shadow-[0_2px_8px_rgba(0,0,0,.07)] ring-1 ring-neutral-100">
                <div id="liste-sikayetleri-top" class="flex flex-wrap items-center justify-between gap-2 pb-4">
                    <h2 class="text-[17px] font-black text-neutral-900">{{ __('Akış sıralaması') }}</h2>
                    <span class="rounded-full bg-[#eef0f3] px-4 py-1.5 text-[12px] font-bold text-neutral-800">{{ $posts->total() }}
                        {{ __('kayıt') }}</span>
                </div>
                <p class="text-[13px] font-medium leading-relaxed text-neutral-600">{{ __('Bugün oluşturulanlar öne • GPS varsa yakındakiler yakınlığa göre • etkileşim sıraları destekliyor.') }}</p>
                <form method="get" action="{{ route('home') }}" id="akisfiltre-form" class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-12">
                    @if ($searchQuery !== '')
                        <input type="hidden" name="q" value="{{ $searchQuery }}">
                    @endif
                    @if ($nearLat !== null && $nearLng !== null)
                        <input type="hidden" name="lat" value="{{ $nearLat }}">
                        <input type="hidden" name="lng" value="{{ $nearLng }}">
                        <input type="hidden" name="relax_city" id="relax_city_sent" value="{{ ! empty($relaxNearby) ? '1' : '0' }}">
                    @endif
                    <div class="sm:col-span-1 lg:col-span-4">
                        <label class="text-[11px] font-bold uppercase tracking-wide text-neutral-500">{{ __('İl') }}</label>
                        <select name="city_id" title="{{ __('Şehir') }}"
                            class="mt-1.5 w-full rounded-xl border-0 bg-[#eef0f3] px-4 py-3 text-sm font-bold text-neutral-900 shadow-inner outline-none ring-2 ring-transparent focus:ring-emerald-500"
                            onchange="document.cookie='bildir_city_id='+encodeURIComponent(this.value)+';path=/;max-age='+60*60*24*365+';SameSite=Lax'; this.form.submit();">
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" @selected((int) $activeCityId === (int) $city->id)>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-1 lg:col-span-4">
                        <label class="text-[11px] font-bold uppercase tracking-wide text-neutral-500">{{ __('Kategori') }}</label>
                        <select name="category_id"
                            class="mt-1.5 w-full rounded-xl border-0 bg-[#eef0f3] px-4 py-3 text-sm font-semibold outline-none ring-2 ring-transparent focus:ring-emerald-500"
                            onchange="this.form.submit()">
                            <option value="">{{ __('Tüm kategoriler') }}</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(request()->integer('category_id') === $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if (! empty($geoActive))
                        <div class="sm:col-span-2 lg:col-span-4 flex items-end">
                            <label class="flex w-full cursor-pointer items-start gap-3 rounded-xl bg-emerald-50 px-4 py-3 font-bold text-emerald-950 ring-1 ring-emerald-100">
                                <input type="checkbox" class="mt-0.5 h-5 w-5 rounded border-emerald-300 text-emerald-600"
                                    id="relax_toggle" @checked(! empty($relaxNearby))
                                    onchange="document.getElementById('relax_city_sent').value=this.checked?'1':'0';this.form.submit();">
                                <span class="text-[13px] leading-snug">{{ __('Geniş GPS kutusu ile yakındaki ve aynı ilde koordinatsız kayıtları da dahil et') }}</span>
                            </label>
                        </div>
                    @endif
                    <noscript>
                        <div class="lg:col-span-12"><button type="submit"
                                class="rounded-xl bg-emerald-600 px-6 py-3 text-xs font-black text-white">{{ __('Uygula') }}</button>
                        </div>
                    </noscript>
                </form>
            </section>

            @if ($searchQuery !== '')
                <p class="rounded-xl bg-emerald-50 px-4 py-2 text-[13px] font-bold text-emerald-900 ring-1 ring-emerald-100">{{ __('“:q” aramasına göre', ['q' => $searchQuery]) }}</p>
            @endif

            <section id="liste-sikayetleri" class="scroll-mt-[6.75rem]" aria-labelledby="liste-baslik">
                <div class="mb-5 flex flex-wrap items-end justify-between gap-4 border-b border-neutral-100 pb-3">
                    <h2 id="liste-baslik" class="text-lg font-black tracking-tight text-neutral-950">{{ __('Gönderi akışı') }}</h2>
                </div>

                <div class="space-y-4">
                    @forelse ($posts as $post)
                        @php
                            $status = $post->status;
                            $badge = match ($status) {
                                \App\Enums\PostStatus::Open => 'bg-amber-100 text-amber-950 ring-1 ring-amber-200',
                                \App\Enums\PostStatus::InProgress => 'bg-sky-50 text-sky-950 ring-1 ring-sky-200',
                                \App\Enums\PostStatus::Resolved => 'bg-emerald-100 text-emerald-950 ring-1 ring-emerald-100',
                                \App\Enums\PostStatus::Rejected => 'bg-neutral-100 text-neutral-900 ring-1 ring-neutral-200',
                            };
                            $pm = \App\Support\PostMediaPresenter::primary($post);
                            $uname = trim((string) ($post->user?->name ?? '?'));
                            $ini = '?';
                            if (preg_match_all('/\p{L}/u', $uname, $__ch) && ($__ch[0] ?? []) !== []) {
                                $slice = array_slice($__ch[0], 0, 2);
                                $ini = mb_strtoupper(implode('', $slice));
                            }
                        @endphp
                        <article
                            class="overflow-hidden rounded-2xl bg-white shadow-[0_2px_8px_rgba(0,0,0,.08)] ring-1 ring-black/[0.04] transition hover:shadow-[0_8px_28px_-6px_rgba(0,0,0,.12)]">

                            {{-- Üst sosyal satırı --}}
                            <div class="flex flex-wrap gap-4 p-5 pb-2">
                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-400 to-teal-600 text-[14px] font-black text-white shadow-inner">
                                    {{ mb_substr($ini, 0, 2) }}
                                </div>
                                <div class="flex min-w-0 flex-1 flex-col gap-1">
                                    <div class="flex flex-wrap items-baseline gap-2">
                                        <span class="text-[17px] font-black text-neutral-900">{{ $uname }}</span>
                                        <span class="text-[13px] text-neutral-700">{{ __('şikâyet bildirdi •') }}</span>
                                        @if ($post->category)
                                            <span class="truncate text-[13px] font-bold text-emerald-900">{{ $post->category->name }}</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[12px] font-semibold text-neutral-700">
                                        <span>{{ $post->city?->name }}@if ($post->district)
                                                · {{ $post->district->name }}
                                            @endif</span>
                                        @php
                                            $pbHome = \App\Support\PublishTimeBadge::for($post->created_at);
                                        @endphp
                                        <time datetime="{{ $post->created_at->toIso8601String() }}" title="{{ $pbHome['title'] }}"
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-black uppercase tracking-wide {{ $pbHome['class'] }}">{{ $pbHome['text'] }}</time>
                                    </div>
                                </div>
                                <span
                                    class="h-fit shrink-0 rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest ring-2 ring-black/[0.04] {{ $badge }}">{{ $status->label() }}</span>
                            </div>

                            @if ($pm)
                                <div class="-mt-px">
                                    @if ($pm['type'] === 'video')
                                        <div class="relative mx-4 mb-5 overflow-hidden rounded-2xl bg-neutral-950">
                                            @if (! empty($pm['poster']))
                                                <img src="{{ $pm['poster'] }}" alt="" class="aspect-video max-h-[20rem] w-full object-cover opacity-70" loading="lazy">
                                            @endif
                                            <a href="{{ route('posts.show', $post) }}"
                                                class="absolute inset-0 flex items-center justify-center">
                                                <span
                                                    class="flex h-[4.75rem] w-[4.75rem] items-center justify-center rounded-full bg-white text-2xl shadow-2xl">▶</span>
                                            </a>
                                            <span
                                                class="absolute left-6 top-4 rounded-full bg-black/70 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-white">{{ __('video') }}</span>
                                        </div>
                                    @else
                                        <a href="{{ route('posts.show', $post) }}" class="-mt-px block">
                                            <img src="{{ $pm['url'] }}" alt=""
                                                class="max-h-[20rem] w-full object-cover hover:brightness-[1.03]" loading="lazy">
                                        </a>
                                        <span
                                            class="mx-auto -mt-[2.95rem] ml-8 inline-flex rounded-full bg-white/92 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-emerald-900 shadow">{{ __('medya') }}</span>
                                    @endif
                                </div>
                            @elseif ($post->latitude !== null && $post->longitude !== null)
                                @php
                                    $__lat = round((float) $post->latitude, 6);
                                    $__lng = round((float) $post->longitude, 6);
                                    $__qm = http_build_query([
                                        'center' => $__lat.','.$__lng,
                                        'zoom' => 15,
                                        'size' => '1280x360',
                                        'markers' => $__lat.','.$__lng.',red-pushpin',
                                    ]);
                                    $__mapSrc = 'https://staticmap.openstreetmap.de/staticmap.php?'.$__qm;
                                @endphp
                                <div class="px-4 pb-5">
                                    <a href="{{ route('posts.show', $post) }}" class="relative block overflow-hidden rounded-2xl ring-2 ring-neutral-900/[0.04]">
                                        <img src="{{ $__mapSrc }}" alt="{{ __('Konum özeti • detay için tıklayın') }}" width="680" height="220" decoding="async" loading="lazy" class="aspect-[2.4/1] w-full object-cover">
                                        <span
                                            class="absolute left-4 top-3 rounded-full bg-emerald-600 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-white shadow">{{ __('harita konumu') }}</span>
                                    </a>
                                </div>
                            @endif

                            <div class="{{ $pm ? 'px-8 pb-8 pt-2' : 'px-8 pb-8 pt-1' }}">
                                <div class="-mt-1 flex flex-wrap items-start gap-4">
                                    <a href="{{ route('posts.show', $post) }}"
                                        class="min-w-0 flex-1 text-[1.375rem] font-black leading-snug tracking-tight text-neutral-950 hover:text-emerald-800">{{ $post->title }}</a>
                                </div>

                                @if ($post->institution)
                                    <a href="{{ route('institutions.show', $post->institution) }}"
                                        class="mt-3 inline-flex flex-wrap items-center gap-3 rounded-xl bg-neutral-900/[0.04] px-4 py-2.5 text-[13px] font-semibold text-neutral-900 ring-2 ring-transparent transition hover:bg-neutral-900/[0.06] hover:ring-emerald-200">
                                        <span
                                            class="rounded-lg bg-neutral-950 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-white">{{ __('Birim') }}</span>
                                        <span class="font-bold">{{ $post->institution->name }}</span>
                                        @if ($post->institution->verified)
                                            <span class="text-[11px] font-black text-emerald-800">{{ __('Onaylı') }}</span>
                                        @endif
                                    </a>
                                @endif

                                <p class="mt-4 text-[17px] leading-relaxed text-neutral-800">{{ \Illuminate\Support\Str::limit(strip_tags((string) $post->description), 360) }}</p>

                                @php
                                    $sup = number_format(max(0, (int) $post->support_count));
                                    $flw = number_format(max(0, (int) $post->follow_count));
                                @endphp
                                <p class="mt-3 text-[12px] font-semibold uppercase tracking-wide text-neutral-900/70">
                                    ❤️ {{ __('Topluluktan :s · takipçi :t', ['s' => $sup, 't' => $flw]) }}</p>

                                <div class="mt-5 grid gap-4 border-y border-neutral-100 py-6 sm:flex sm:flex-wrap">
                                    @auth
                                        <form method="POST" action="{{ route('posts.support.web', $post) }}" class="inline shrink-0">
                                            @csrf
                                            <button type="submit"
                                                class="rounded-full px-6 py-2.5 text-[12px] font-black uppercase tracking-widest shadow-sm ring-4 ring-black/[0.04] transition {{ ! empty($post->viewer_supported) ? 'bg-neutral-950 text-white' : 'bg-[#eef0f3] text-neutral-950 hover:bg-[#dfe3ea]' }}">{{ ! empty($post->viewer_supported) ? __('Destek gönderdin') : __('Destek') }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('posts.follow.web', $post) }}" class="inline shrink-0">
                                            @csrf
                                            <button type="submit"
                                                class="rounded-full px-6 py-2.5 text-[12px] font-black uppercase tracking-wide shadow-sm ring-4 ring-black/[0.04] transition {{ ! empty($post->viewer_following) ? 'border-4 border-transparent bg-neutral-950 text-white' : 'border-4 border-indigo-200 bg-white text-neutral-950 hover:bg-indigo-50' }}">{{ ! empty($post->viewer_following) ? __('Süreci izliyorum') : __('Süreci izle') }}</button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}"
                                            class="text-[13px] font-black text-emerald-800 underline decoration-4 underline-offset-4">{{ __('Giriş yap — destekle') }}</a>
                                    @endauth
                                    <a href="{{ route('posts.show', $post) }}"
                                        class="ml-auto inline-flex min-w-[180px] items-center justify-center rounded-full bg-neutral-950 px-6 py-3 text-[13px] font-black text-white hover:bg-neutral-900 sm:justify-center">{{ __('Gönderiye git →') }}</a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border-2 border-dashed border-neutral-200 bg-neutral-50 py-28 text-center text-[17px] font-bold text-neutral-700">
                            {{ __('Bu il ve filtrede kayıt yok. Yakınındaki ilçeden bir örnek almak için geniş yakın seçeneğini dene.') }}
                        </div>
                    @endforelse
                </div>
                <div class="flex justify-center pt-6">{{ $posts->links() }}</div>
            </section>
        </div>

        {{-- Sağ sütun: widget --}}
        <aside class="hidden space-y-4 lg:block">
            <section class="overflow-hidden rounded-2xl bg-white shadow-[0_2px_8px_rgba(0,0,0,.07)] ring-1 ring-neutral-100">
                <div class="bg-gradient-to-r from-neutral-950 to-neutral-900 px-5 py-4 text-[13px] font-black uppercase tracking-wider text-neutral-100">
                    {{ __('Neden güvenilir?') }}</div>
                <ul class="space-y-3 px-5 py-6 text-[14px] font-semibold text-neutral-800">
                    <li class="leading-snug">{{ __('moderasyon sırasından sonra yayın') }}</li>
                    <li class="leading-snug">{{ __('Konum yakınları ve etkileşim ile sıralama kuralları görünür') }}</li>
                    <li class="leading-snug">{{ __('Kurumu bir kez seç; /brand adresinden doğrulanmış şirket desteği aktif.') }}</li>
                </ul>
                @guest
                    <div class="px-5 pb-6">
                        <a href="{{ route('register') }}"
                            class="block w-full rounded-2xl bg-emerald-600 py-4 text-center text-[13px] font-black text-white shadow-lg shadow-emerald-600/30 hover:bg-emerald-700">{{ __('Topluluğa katıl') }}</a>
                    </div>
                @else
                    <div class="px-5 pb-6">
                        <a href="{{ route('complaints.quick.create') }}"
                            class="block w-full rounded-2xl bg-neutral-900 py-4 text-center text-[13px] font-black uppercase tracking-[0.2em] text-white hover:bg-neutral-800">{{ __('Bildiri tamamla') }}</a>
                    </div>
                @endguest
            </section>

            <section class="rounded-2xl bg-emerald-50 p-6 text-[13px] font-semibold shadow-[0_2px_10px_-3px_rgba(5,120,94,0.3)] ring-1 ring-emerald-100">
                <p class="text-[17px] font-black text-neutral-950">{{ __('Belediye ve kurumsal kullanıcı') }}</p>
                <p class="mt-4 leading-snug">{{ __('Doğrulanmış kurum hesapları bildirilmiş kent sorunlarına görünür tepki süreci oluşturur.') }}</p>
                <div class="mt-5">
                    <a href="{{ route('login.brand') }}"
                        class="inline-flex w-full justify-center rounded-2xl bg-emerald-600 py-4 text-[12px] font-black uppercase tracking-wider text-white shadow-md hover:bg-emerald-700">{{ __('Belediye / kurum oturumu') }}</a>
                </div>
            </section>

            <section class="rounded-2xl bg-gradient-to-br from-indigo-700 to-violet-800 p-6 text-[13px] font-semibold shadow-lg shadow-indigo-900/25 ring-2 ring-white/10">
                <p class="text-[11px] font-black uppercase tracking-[0.15em] text-emerald-200">{{ __('Sosyal sorumluluk') }}</p>
                <p class="mt-3 text-[17px] font-black leading-snug text-white">{{ __('Dayanışma kampanyalarına destek ver') }}</p>
                <p class="mt-3 leading-snug text-indigo-100">{{ __('Başlatılan kampanyalar süper yönetici onayından sonra yayına girer ve destekçi hedefleri görünür olur.') }}</p>
                <div class="mt-5 grid gap-2">
                    <a href="{{ route('campaigns.index') }}"
                        class="flex w-full justify-center rounded-xl bg-white py-3.5 text-center text-[12px] font-black text-indigo-950 hover:bg-indigo-50">{{ __('Kampanyaları gör') }}</a>
                    @auth
                        <a href="{{ route('campaigns.create') }}"
                            class="flex w-full justify-center rounded-xl border-2 border-white/40 py-3 text-center text-[12px] font-bold text-white hover:bg-white/10">{{ __('Yeni kampanya') }}</a>
                    @endauth
                </div>
            </section>

            <section class="rounded-2xl bg-white px-6 py-6 shadow-md ring-1 ring-neutral-100">
                <h3 class="text-[17px] font-black text-neutral-950">{{ __('Kategoriler • Tam liste') }}</h3>
                <ul class="mt-4 divide-y divide-neutral-100 border-t border-neutral-100">
                    @foreach ($categories as $cat)
                        <li>
                            <a href="{{ route('home', array_merge($feedPreserve, ['category_id' => $cat->id])) }}"
                                class="flex justify-between px-1 py-3 text-[15px] font-bold text-neutral-800 hover:bg-[#eef0f3]/80 {{ request()->integer('category_id') === $cat->id ? '!bg-emerald-50 font-black text-emerald-950 ring-4 ring-transparent' : '' }}">
                                <span>{{ $cat->name }}</span>
                                <span class="opacity-65">⇢</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        </aside>
    </div>
@endsection
