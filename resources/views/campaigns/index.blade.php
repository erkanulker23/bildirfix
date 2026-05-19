@extends('layouts.app')

@section('title', __('Sosyal sorumluluk kampanyaları').' • '.config('app.name'))

@section('content')
    @php
        $campaignHeroUrl = static function ($campaign): ?string {
            $u = $campaign->hero_image_url;
            if ($u === null || trim((string) $u) === '') {
                return null;
            }

            return \Illuminate\Support\Str::startsWith($u, ['http://', 'https://']) ? $u : url(ltrim($u, '/'));
        };
        $activeTopic = $topics->firstWhere('id', (int) ($activeTopicFilter ?? 0));
    @endphp

    <div class="blog-magazine mx-auto max-w-[1200px] px-4 pb-16 pt-2 sm:px-5">
        {{-- Masthead --}}
        <header class="relative mb-8 border-b-2 border-neutral-900 pb-6 pt-2 sm:mb-10 sm:pb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="font-heading text-[clamp(2.25rem,5.5vw,4rem)] font-black leading-[0.95] tracking-tight text-neutral-950">
                        {{ __('Kampanyalar') }}
                    </p>
                    <p class="mt-2 max-w-xl font-medium text-[15px] leading-relaxed text-neutral-600">
                        {{ __('Toplumsal dayanışma ve sosyal sorumluluk — onaylanan kampanyaları keşfedin, destek olun veya kendi kampanyanızı başlatın.') }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                    @auth
                        <a href="{{ route('campaigns.create') }}"
                            class="rounded-full bg-primary px-5 py-2.5 text-xs font-black uppercase tracking-wide text-white shadow-md shadow-primary/25 transition hover:bg-primary-hover">
                            {{ __('Kampanya başlat') }}
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                            class="rounded-full bg-primary px-5 py-2.5 text-xs font-black uppercase tracking-wide text-white shadow-md shadow-primary/25 transition hover:bg-primary-hover">
                            {{ __('Katıl') }}
                        </a>
                        <a href="{{ route('login') }}"
                            class="rounded-full border-2 border-neutral-900 bg-neutral-950 px-5 py-2.5 text-xs font-black uppercase tracking-wide text-white transition hover:bg-neutral-800">
                            {{ __('Giriş yap — kampanya aç') }}
                        </a>
                    @endauth
                    <a href="{{ route('home') }}"
                        class="rounded-full border border-neutral-200 bg-white px-4 py-2 text-xs font-bold text-neutral-800 transition hover:border-primary/40 hover:text-primary">
                        {{ __('Ana sayfa') }}
                    </a>
                </div>
            </div>

            @if ($topics->isNotEmpty())
                <nav class="blog-mag-cats mt-6 flex gap-2 overflow-x-auto pb-1 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                    aria-label="{{ __('Konular') }}">
                    <a href="{{ route('campaigns.index', request()->only('city_id')) }}"
                        class="shrink-0 rounded-full px-4 py-2 text-xs font-bold transition {{ empty($activeTopicFilter) ? 'bg-primary text-white shadow-md shadow-primary/25' : 'bg-neutral-100 text-neutral-800 hover:bg-neutral-200' }}">
                        {{ __('Tümü') }}
                    </a>
                    @foreach ($topics as $topic)
                        <a href="{{ route('campaigns.index', array_filter(['konu' => $topic->id, 'city_id' => $activeCityFilter ?: null])) }}"
                            class="shrink-0 rounded-full px-4 py-2 text-xs font-bold transition {{ (int) $activeTopicFilter === (int) $topic->id ? 'bg-primary text-white shadow-md shadow-primary/25' : 'bg-neutral-100 text-neutral-800 hover:bg-neutral-200' }}">
                            {{ $topic->name }}
                        </a>
                    @endforeach
                </nav>
            @endif
        </header>

        @if ($activeTopic !== null || $activeCityFilter)
            <p class="mb-6 text-sm font-semibold text-neutral-600">
                @if ($activeTopic !== null)
                    <span class="text-neutral-400">{{ __('Konu:') }}</span>
                    <span class="text-neutral-900">{{ $activeTopic->name }}</span>
                @endif
                @if ($activeCityFilter)
                    @php $activeCity = $cities->firstWhere('id', (int) $activeCityFilter); @endphp
                    @if ($activeCity)
                        <span class="text-neutral-300 mx-1">·</span>
                        <span class="text-neutral-400">{{ __('İl:') }}</span>
                        <span class="text-neutral-900">{{ $activeCity->name }}</span>
                    @endif
                @endif
                · <a href="{{ route('campaigns.index') }}"
                    class="font-bold text-primary underline-offset-2 hover:underline">{{ __('Filtreyi temizle') }}</a>
            </p>
        @endif

        <div class="grid gap-8 lg:grid-cols-[1fr_300px] lg:gap-10 xl:grid-cols-[1fr_320px]">
            <div class="min-w-0 space-y-8">
                <form method="get" action="{{ route('campaigns.index') }}"
                    class="flex flex-wrap items-end gap-3 rounded-2xl border border-neutral-200/80 bg-white p-4 shadow-sm ring-1 ring-black/[0.03] sm:p-5">
                    @if ($activeTopicFilter)
                        <input type="hidden" name="konu" value="{{ $activeTopicFilter }}">
                    @endif
                    <div class="min-w-[10rem] flex-1">
                        <label class="text-[10px] font-black uppercase tracking-wider text-neutral-500"
                            for="cmp-city">{{ __('İl (isteğe bağlı)') }}</label>
                        <select id="cmp-city" name="city_id"
                            class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm font-bold text-neutral-900 outline-none ring-2 ring-transparent focus:border-primary/40 focus:ring-primary/20">
                            <option value="">{{ __('Tüm iller + genel') }}</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" @selected((int) ($activeCityFilter ?? 0) === (int) $city->id)>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="rounded-full bg-neutral-900 px-6 py-3 text-xs font-black uppercase tracking-wide text-white transition hover:bg-neutral-800">
                        {{ __('Uygula') }}
                    </button>
                </form>

                @if ($campaigns->isEmpty())
                    <div class="rounded-3xl border-2 border-dashed border-neutral-200 bg-neutral-50 py-24 text-center">
                        <p class="text-lg font-bold text-neutral-700">{{ __('Bu filtre ile kampanya görünmüyor.') }}</p>
                        <p class="mt-2 text-sm font-medium text-neutral-500">
                            {{ __('Yeni kampanyalar onay sürecinden geçtikçe burada görünecek.') }}</p>
                        @guest
                            <a href="{{ route('register') }}"
                                class="btn-primary mt-6 inline-flex rounded-full px-6 py-3 text-sm font-bold">{{ __('Aramıza katıl') }}</a>
                        @endguest
                    </div>
                @else
                    @php
                        $showHero = $campaigns->onFirstPage() && $campaigns->count() > 0;
                        $heroCampaign = $showHero ? $campaigns->first() : null;
                    @endphp

                    @if ($heroCampaign !== null)
                        @php $heroUrl = $campaignHeroUrl($heroCampaign); @endphp
                        <article
                            class="blog-mag-hero group relative overflow-hidden rounded-3xl bg-neutral-950 shadow-[0_24px_60px_-12px_rgba(0,0,0,0.35)] ring-1 ring-white/10">
                            <a href="{{ route('campaigns.show', $heroCampaign) }}" class="relative block">
                                @if ($heroUrl)
                                    <div class="aspect-[16/10] w-full sm:aspect-[21/9]">
                                        <img src="{{ $heroUrl }}" alt=""
                                            class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]"
                                            loading="eager" decoding="async" fetchpriority="high">
                                    </div>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent sm:via-black/30"></div>
                                @else
                                    <div
                                        class="flex aspect-[16/10] min-h-[14rem] items-end bg-gradient-to-br from-sky-600 via-primary to-neutral-900 p-8 sm:aspect-[21/9] sm:min-h-[16rem]">
                                    </div>
                                @endif
                                <div class="absolute inset-x-0 bottom-0 p-6 sm:p-10">
                                    @if ($heroCampaign->topic)
                                        <span
                                            class="inline-block rounded-full bg-sky-500 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-white">{{ $heroCampaign->topic->name }}</span>
                                    @else
                                        <span
                                            class="inline-block rounded-full bg-primary px-3 py-1 text-[10px] font-black uppercase tracking-wider text-white">{{ __('SSR kampanyası') }}</span>
                                    @endif
                                    <h2
                                        class="blog-mag-hero__title mt-3 font-heading text-[clamp(1.35rem,3.5vw,2.35rem)] font-black leading-[1.1] tracking-tight text-white drop-shadow-sm">
                                        {{ $heroCampaign->title }}
                                    </h2>
                                    <p class="mt-3 line-clamp-2 max-w-2xl text-[15px] font-medium leading-relaxed text-white/90">
                                        {{ \Illuminate\Support\Str::limit(strip_tags((string) ($heroCampaign->excerpt ?? $heroCampaign->description)), 200) }}
                                    </p>
                                    <div class="mt-4 flex flex-wrap items-center gap-3 text-[13px] font-semibold text-white/85">
                                        <span>❤ {{ number_format(max(0, (int) $heroCampaign->supporter_count)) }} {{ __('destek') }}</span>
                                        @if ($heroCampaign->city)
                                            <span class="rounded-full bg-white/15 px-2.5 py-0.5 backdrop-blur-sm">{{ $heroCampaign->city->name }}</span>
                                        @endif
                                        <span class="ml-auto inline-flex items-center gap-1 rounded-full bg-white/15 px-3 py-1 text-white backdrop-blur-sm transition group-hover:bg-white/25">
                                            {{ __('Öne çıkan') }} →
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </article>
                    @endif

                    <div class="grid gap-5 sm:grid-cols-2 sm:gap-6">
                        @foreach ($campaigns as $campaign)
                            @if ($heroCampaign !== null && $loop->first)
                                @continue
                            @endif
                            @php $cardUrl = $campaignHeroUrl($campaign); @endphp
                            <article
                                class="blog-mag-card group flex flex-col overflow-hidden rounded-2xl border border-neutral-200/80 bg-white shadow-sm ring-1 ring-black/[0.03] transition hover:-translate-y-0.5 hover:border-primary/25 hover:shadow-lg hover:shadow-primary/5">
                                <a href="{{ route('campaigns.show', $campaign) }}" class="relative block shrink-0 overflow-hidden">
                                    @if ($cardUrl)
                                        <div class="aspect-[16/10] w-full">
                                            <img src="{{ $cardUrl }}" alt=""
                                                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                                loading="lazy" decoding="async">
                                        </div>
                                    @else
                                        <div
                                            class="blog-mag-card__placeholder flex aspect-[16/10] items-center justify-center bg-gradient-to-br from-sky-50 via-orange-50/80 to-neutral-100">
                                            <span class="font-heading text-4xl font-black text-sky-600/30" aria-hidden="true">
                                                {{ mb_substr(strip_tags($campaign->title), 0, 1) }}</span>
                                        </div>
                                    @endif
                                </a>
                                <div class="flex flex-1 flex-col p-5 sm:p-6">
                                    @if ($campaign->topic)
                                        <span
                                            class="w-fit rounded-md bg-sky-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-wide text-sky-800">{{ $campaign->topic->name }}</span>
                                    @else
                                        <span
                                            class="w-fit rounded-md bg-orange-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-wide text-primary">{{ __('SSR') }}</span>
                                    @endif
                                    <h3 class="mt-2 font-heading text-lg font-black leading-snug tracking-tight text-neutral-950 sm:text-xl">
                                        <a href="{{ route('campaigns.show', $campaign) }}"
                                            class="hover:text-primary">{{ $campaign->title }}</a>
                                    </h3>
                                    <p class="mt-2 line-clamp-3 flex-1 text-[14px] leading-relaxed text-neutral-600">
                                        {{ \Illuminate\Support\Str::limit(strip_tags((string) ($campaign->excerpt ?? $campaign->description)), 140) }}
                                    </p>
                                    <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-neutral-100 pt-3 text-[12px] font-semibold text-neutral-500">
                                        <span class="font-bold text-neutral-800">❤ {{ number_format(max(0, (int) $campaign->supporter_count)) }}</span>
                                        <span class="text-neutral-300">·</span>
                                        <span>👁 {{ number_format(max(0, (int) $campaign->view_count)) }}</span>
                                        @if ($campaign->city)
                                            <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-900">{{ $campaign->city->name }}</span>
                                        @else
                                            <span class="rounded-full bg-neutral-100 px-2 py-0.5 text-[10px] font-bold text-neutral-600">{{ __('Genel') }}</span>
                                        @endif
                                        <a href="{{ route('campaigns.show', $campaign) }}"
                                            class="ml-auto text-xs font-black uppercase tracking-wide text-primary hover:underline">{{ __('İncele') }}</a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="flex justify-center pt-4">{{ $campaigns->links() }}</div>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-8 lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-2xl border border-neutral-200 bg-gradient-to-b from-neutral-50 to-white p-6 shadow-sm">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-primary">{{ __('Liste') }}</p>
                    <p class="mt-2 font-heading text-xl font-black text-neutral-950">{{ __('Öne çıkanlar') }}</p>
                    <ul class="mt-5 space-y-4">
                        @foreach ($campaigns->take(5) as $i => $sideCampaign)
                            <li class="flex gap-3 border-b border-neutral-100 pb-4 last:border-0 last:pb-0">
                                <span
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-neutral-900 font-heading text-sm font-black text-white">{{ $i + 1 }}</span>
                                <div class="min-w-0">
                                    @if ($sideCampaign->topic)
                                        <p class="text-[10px] font-bold uppercase tracking-wide text-neutral-400">{{ $sideCampaign->topic->name }}</p>
                                    @endif
                                    <a href="{{ route('campaigns.show', $sideCampaign) }}"
                                        class="mt-0.5 line-clamp-2 text-sm font-bold leading-snug text-neutral-900 hover:text-primary">{{ $sideCampaign->title }}</a>
                                    <p class="mt-1 text-[11px] font-semibold text-neutral-400">
                                        ❤ {{ number_format(max(0, (int) $sideCampaign->supporter_count)) }} {{ __('destek') }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="rounded-2xl border-l-4 border-primary bg-neutral-900 p-6 text-white shadow-lg">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-primary">{{ __('Sen de başlat') }}</p>
                    <p class="mt-2 font-heading text-lg font-black leading-snug">{{ __('Toplumsal farkındalık için kampanya açın.') }}</p>
                    @auth
                        <a href="{{ route('campaigns.create') }}"
                            class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-primary px-4 py-3 text-center text-sm font-black text-white transition hover:bg-primary-hover">
                            {{ __('Kampanya başlat') }}
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                            class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-primary px-4 py-3 text-center text-sm font-black text-white transition hover:bg-primary-hover">
                            {{ __('Ücretsiz katıl') }}
                        </a>
                    @endauth
                </div>
            </aside>
        </div>
    </div>
@endsection
