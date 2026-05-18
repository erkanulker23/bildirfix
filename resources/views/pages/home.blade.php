@extends('layouts.app')

@section('title', config('app.name').' • '.__('Akış'))

@php
    $feedPreserve = array_filter([
        'city_id' => $activeCityId,
        'category_id' => request()->integer('category_id') ?: null,
        'q' => ($searchQuery ?? '') !== '' ? $searchQuery : null,
        'lat' => $nearLat,
        'lng' => $nearLng,
        'relax_city' => ($nearLat !== null && $nearLng !== null) ? ((! empty($relaxNearby)) ? '1' : '0') : null,
        'feed' => request('feed'),
    ], fn ($v) => $v !== null && $v !== '');
    $activeFeed = $activeFeed ?? request('feed', 'all');
@endphp

@section('content')
    <div class="min-h-screen bg-[#F9FAFB] pb-2 md:pb-8">
        @isset($platformStats)
            <div class="border-b border-[#252830]/80 bg-[#1f232d] px-4 py-2 text-[13px] sm:px-5 lg:py-2.5">
                <div class="mx-auto flex max-w-[1250px] flex-wrap items-center justify-center gap-3">
                    @if (($platformStats['campaigns_live'] ?? 0) > 0)
                        <a href="{{ route('campaigns.index') }}"
                            class="inline-flex items-center gap-2 rounded-full bg-[#7c6cf5]/20 px-3 py-1.5 text-[11px] font-black uppercase tracking-wider text-[#c4beff] ring-2 ring-[#c4beff]/30 hover:bg-[#7c6cf5]/30">
                            <span aria-hidden="true">✶</span>
                            {{ __(':n kampanya • sosyal sorumluluk', ['n' => number_format((int) $platformStats['campaigns_live'])]) }}
                        </a>
                    @endif
                    <span class="text-[13px] font-medium leading-snug text-neutral-400">{{ __('Kent sorunları ve güncellenen çözümleri takip et') }}</span>
                    <a href="{{ route('feed.index') }}"
                        class="inline-flex items-center gap-2 rounded-full bg-[#34d399] px-3.5 py-1.5 text-[11px] font-black uppercase tracking-wider text-neutral-950 shadow-sm ring-2 ring-white/10 hover:bg-emerald-300">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-white opacity-50"></span>
                            <span class="relative inline-block h-2 w-2 rounded-full bg-white"></span>
                        </span>
                        {{ __('Canlı izle') }}
                    </a>
                </div>
            </div>
        @endisset
        @include('partials.home-hero')
        @include('partials.home-csr-campaigns')

        <x-top-bar :city-name="$topBarCityName ?? ''" />

        <section class="border-b border-gray-100 bg-white py-3">
            <div class="scrollbar-none flex snap-scroll-x items-start gap-3 overflow-x-auto px-4">
                <div class="snap-item-start shrink-0">
                    <x-story-circle :is-add="true" />
                </div>
                @forelse ($stories as $story)
                    <div class="snap-item-start shrink-0">
                        <x-story-circle :story="$story" :is-viewed="false" />
                    </div>
                @empty
                    <div class="flex items-center gap-2 px-2 py-6 text-sm text-gray-500">
                        {{ __('Bu filtrede henüz hikâye yok.') }}
                    </div>
                @endforelse
            </div>
        </section>

        <div class="sticky top-14 z-30 border-b border-gray-100 bg-white px-4 py-2.5 md:relative md:top-0">
            @php
                $isRecent = ($activeFeed ?? '') === 'recent';
                $basePreserve = array_filter([
                    'city_id' => $activeCityId,
                    'category_id' => request()->integer('category_id') ?: null,
                    'q' => ($searchQuery ?? '') !== '' ? $searchQuery : null,
                    'lat' => $nearLat,
                    'lng' => $nearLng,
                    'relax_city' => ($nearLat !== null && $nearLng !== null) ? ((! empty($relaxNearby)) ? '1' : '0') : null,
                ], fn ($v) => $v !== null && $v !== '');
                $pillActive = 'bg-primary text-white shadow-cta';
                $pillIdle = 'bg-gray-100 text-gray-600 hover:bg-gray-200';
            @endphp
            <div class="scrollbar-none flex snap-scroll-x items-center gap-2 overflow-x-auto">
                <a href="{{ route('home', $basePreserve) }}"
                    class="flex shrink-0 snap-item-start items-center gap-1.5 whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition-all {{ ! $isRecent ? $pillActive : $pillIdle }}">
                    <span aria-hidden="true">🌍</span>{{ __('Tümü') }}
                </a>

                @if (! empty($geoActive))
                    <a href="{{ route('home', array_merge($basePreserve, ['relax_city' => '1'])) }}"
                        class="flex shrink-0 snap-item-start items-center gap-1.5 whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition-all {{ ! empty($relaxNearby) ? $pillActive : $pillIdle }}">
                        <span aria-hidden="true">📍</span>{{ __('Yakınım') }}
                    </a>
                @else
                    <button type="button" onclick="window.dsToast?.('{{ __('Konum sıralaması kapalı; ana akışta GPS kullanın.') }}', 'info')"
                        class="flex shrink-0 snap-item-start items-center gap-1.5 whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold text-gray-400 opacity-90 ring-1 ring-gray-200">
                        <span aria-hidden="true">📍</span>{{ __('Yakınım') }}
                    </button>
                @endif

                <a href="{{ route('home', array_merge($basePreserve, ['feed' => 'recent'])) }}"
                    class="flex shrink-0 snap-item-start items-center gap-1.5 whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition-all {{ $isRecent ? $pillActive : $pillIdle }}">
                    <span aria-hidden="true">🕐</span>{{ __('Son') }}
                </a>

                <span class="mx-1 hidden h-6 w-px shrink-0 bg-gray-200 sm:inline" aria-hidden="true"></span>

                <a href="{{ route('campaigns.index') }}"
                    class="ml-auto flex shrink-0 snap-item-start items-center gap-1.5 whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition-all {{ $pillIdle }}">
                    <span aria-hidden="true">🔥</span>{{ __('Trend') }}
                </a>

                <a href="{{ route('home', array_merge($feedPreserve, ['category_id' => null])) }}"
                    class="flex shrink-0 snap-item-start items-center gap-1.5 whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition-all {{ $pillIdle }}">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <line x1="4" x2="4" y1="21" y2="14" />
                        <line x1="4" x2="4" y1="10" y2="3" />
                        <line x1="12" x2="12" y1="21" y2="12" />
                        <line x1="12" x2="12" y1="8" y2="3" />
                        <line x1="20" x2="20" y1="21" y2="16" />
                        <line x1="20" x2="20" y1="12" y2="3" />
                        <line x1="2" x2="6" y1="14" y2="14" />
                        <line x1="10" x2="14" y1="8" y2="8" />
                        <line x1="18" x2="22" y1="16" y2="16" />
                    </svg>
                    {{ __('Filtre') }}
                </a>
            </div>
        </div>

        <main id="liste-sikayetleri"
            class="mx-auto max-w-lg scroll-mt-[6.75rem] space-y-3 px-3 py-3 md:max-w-3xl md:scroll-mt-28 md:px-4"
            aria-labelledby="liste-baslik">
            <h2 id="liste-baslik" class="sr-only">{{ __('Şikâyet akışı') }}</h2>
            @if ($errors->any())
                <div class="rounded-ds-md border border-danger/30 bg-danger-light px-4 py-3 text-sm text-danger" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            @forelse ($posts as $post)
                <x-post-card :post="$post" :compact="true" />
            @empty
                <div class="flex flex-col items-center justify-center rounded-ds-lg border border-dashed border-gray-200 bg-white px-6 py-16 text-center">
                    <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gray-100">
                        <svg class="h-9 w-9 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5" aria-hidden="true">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-gray-900">{{ __('Bu bölgede henüz kayıt yok') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('İlk bildiren siz olun.') }}</p>
                    <a href="{{ route('posts.create') }}" class="btn-primary mt-6">{{ __('Paylaşım oluştur') }}</a>
                </div>
            @endforelse

            @if ($posts->total() > 0)
                <div class="flex justify-center pt-4">{{ $posts->links() }}</div>
            @endif
        </main>

        <x-bottom-nav />
        <x-story-viewer />
    </div>
@endsection

@push('scripts')
    <script>
        window.__storiesFeed = {!! \Illuminate\Support\Js::from($storiesJson ?? []) !!};
    </script>
@endpush
