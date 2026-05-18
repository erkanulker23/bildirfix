@extends('layouts.app')

@section('title', config('app.name').' • '.__('Akış'))

@section('content')
    @php
        $feedWithoutGeo = array_filter([
            'city_id' => $activeCityId,
            'district_id' => $activeDistrictId ?? null,
            'category_id' => request()->integer('category_id') ?: null,
            'q' => $searchQuery !== '' ? $searchQuery : null,
            'feed' => request('feed') ?: null,
        ], fn ($v) => $v !== null && $v !== '');
        $feedAllUrl = route('feed.index', array_filter(array_merge($feedWithoutGeo, ['feed' => null]), fn ($v) => $v !== null && $v !== ''));
        $feedRecentUrl = route('feed.index', array_filter(array_merge($feedWithoutGeo, ['feed' => 'recent']), fn ($v) => $v !== null && $v !== ''));
    @endphp

    <div
        class="relative left-1/2 z-0 mb-8 w-screen max-w-[100vw] -translate-x-1/2 border-b border-neutral-200/80 bg-gradient-to-b from-neutral-50 via-white to-[#eef1f8] text-neutral-950 shadow-[inset_0_1px_0_rgba(255,255,255,0.9)]">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-32 bg-gradient-to-b from-indigo-100/35 to-transparent blur-2xl"
            aria-hidden="true"></div>
        <div class="relative mx-auto max-w-[1200px] px-4 py-8 sm:px-5 sm:py-10">
            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-emerald-700">{{ __('Canlı kayıtlar') }}</p>
            <h1 class="mt-2 font-heading text-[clamp(1.65rem,3.2vw,2.35rem)] font-black leading-tight tracking-tight text-neutral-950">
                {{ __('Akış') }}</h1>
            <p class="mt-3 max-w-2xl text-[14px] leading-relaxed text-neutral-800">
                {{ __('Filtreleyin, sıralamayı seçin ve kent sorunlarını tek listede gezin — destek ve takip için giriş yapın.') }}</p>
            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('home') }}"
                    class="rounded-full border border-neutral-200 bg-white px-4 py-2 text-[12px] font-bold text-neutral-900 shadow-sm hover:border-neutral-300 hover:bg-neutral-50">{{ __('Ana sayfa') }}</a>
                <a href="{{ route('posts.create') }}"
                    class="rounded-full bg-emerald-500 px-4 py-2 text-[12px] font-black text-neutral-950 shadow-md shadow-emerald-500/25 ring-1 ring-emerald-400/40 hover:bg-emerald-400">{{ __('Kent sorunu bildir') }}</a>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-[1200px] px-4 sm:px-5">
        @include('partials.stories-strip', [
            'stories' => $feedStories,
            'moreHref' => route('home').'#hikayeler',
            'moreLabel' => __('Ana sayfada tüm hikâyeler'),
        ])
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_16.5rem] xl:items-start xl:gap-8">
        <div class="min-w-0 space-y-6">
            <section class="rounded-3xl border border-neutral-200/80 bg-white p-5 shadow-[0_20px_50px_-38px_rgba(15,23,42,0.28)] sm:p-6"
                aria-labelledby="akis-siralama">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-neutral-100 pb-4">
                    <div>
                        <h2 id="akis-siralama" class="text-lg font-black text-neutral-950">{{ __('Filtreler ve sıralama') }}</h2>
                        <p class="mt-1 text-[13px] font-medium text-neutral-600">{{ __('İl ve kategoriyle daraltın; konum açıksa sıralamaya dahil edilir.') }}</p>
                    </div>
                    <span class="rounded-full bg-violet-50 px-3 py-1.5 text-[12px] font-black tabular-nums text-violet-900 ring-1 ring-violet-200">
                        {{ number_format($posts->total(), 0, ',', '.') }} {{ __('kayıt') }}</span>
                </div>

                <div class="mt-4 flex flex-wrap gap-2" role="tablist" aria-label="{{ __('Sıralama') }}">
                    <a href="{{ $feedAllUrl }}"
                        class="rounded-full px-4 py-2 text-[12px] font-black transition {{ request('feed', 'all') !== 'recent' ? 'bg-neutral-950 text-white shadow-md' : 'border border-neutral-200 bg-neutral-50 text-neutral-800 hover:border-violet-300' }}">{{ __('Öne çıkan') }}</a>
                    <a href="{{ $feedRecentUrl }}"
                        class="rounded-full px-4 py-2 text-[12px] font-black transition {{ request('feed') === 'recent' ? 'bg-violet-600 text-white shadow-md' : 'border border-neutral-200 bg-neutral-50 text-neutral-800 hover:border-violet-300' }}">{{ __('En yeni') }}</a>
                </div>

                <form method="get" action="{{ route('feed.index') }}" id="akisfiltre-form-feed" class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-12">
                    @if (request()->filled('feed'))
                        <input type="hidden" name="feed" value="{{ request('feed') }}">
                    @endif
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
                            class="mt-1.5 w-full rounded-2xl border-0 bg-neutral-100 px-4 py-3 text-sm font-bold text-neutral-900 shadow-inner outline-none ring-2 ring-transparent focus:ring-violet-500"
                            onchange="document.cookie='simdibildir_city_id='+encodeURIComponent(this.value)+';path=/;max-age='+60*60*24*365+';SameSite=Lax'; this.form.submit();">
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" @selected((int) $activeCityId === (int) $city->id)>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-1 lg:col-span-4">
                        <label class="text-[11px] font-bold uppercase tracking-wide text-neutral-500">{{ __('İlçe') }}</label>
                        <select name="district_id"
                            class="mt-1.5 w-full rounded-2xl border-0 bg-neutral-100 px-4 py-3 text-sm font-semibold outline-none ring-2 ring-transparent focus:ring-violet-500"
                            onchange="this.form.submit()">
                            <option value="">{{ __('Tüm ilçeler') }}</option>
                            @foreach (($districts ?? collect()) as $d)
                                <option value="{{ $d->id }}" @selected((int) ($activeDistrictId ?? 0) === (int) $d->id)>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-1 lg:col-span-4">
                        <label class="text-[11px] font-bold uppercase tracking-wide text-neutral-500">{{ __('Kategori') }}</label>
                        <select name="category_id"
                            class="mt-1.5 w-full rounded-2xl border-0 bg-neutral-100 px-4 py-3 text-sm font-semibold outline-none ring-2 ring-transparent focus:ring-violet-500"
                            onchange="this.form.submit()">
                            <option value="">{{ __('Tüm kategoriler') }}</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(request()->integer('category_id') === $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if (! empty($geoActive))
                        <div class="flex items-end sm:col-span-2 lg:col-span-12">
                            <label class="flex w-full cursor-pointer items-start gap-3 rounded-2xl bg-emerald-50/90 px-4 py-3 font-bold text-emerald-950 ring-1 ring-emerald-100">
                                <input type="checkbox" class="mt-0.5 h-5 w-5 rounded border-emerald-300 text-emerald-600"
                                    id="relax_toggle" @checked(! empty($relaxNearby))
                                    onchange="document.getElementById('relax_city_sent').value=this.checked?'1':'0';this.form.submit();">
                                <span class="text-[13px] leading-snug">{{ __('Geniş yakınlık kutusu') }}</span>
                            </label>
                        </div>
                    @endif
                </form>

                @if ($activeDistrictId)
                    <p class="mt-3 flex flex-wrap items-center gap-2 text-[13px] font-semibold text-violet-900">
                        <span>{{ __('İlçe filtresi etkin.') }}</span>
                        <a href="{{ route('feed.index', array_filter(['city_id' => $activeCityId, 'category_id' => request()->integer('category_id') ?: null, 'q' => $searchQuery !== '' ? $searchQuery : null, 'feed' => request('feed') ?: null])) }}"
                            class="rounded-full border border-violet-200 bg-white px-3 py-1 text-[12px] font-bold text-violet-800 hover:bg-violet-50">{{ __('İlçeyi kaldır') }}</a>
                    </p>
                @endif
            </section>

            <section class="space-y-5" aria-label="{{ __('Gönderi listesi') }}">
                @forelse ($posts as $post)
                    @include('feed._post-article', ['post' => $post])
                    @if ($loop->iteration % 4 === 0)
                        <x-ad-slot placement="feed_inline" />
                    @endif
                @empty
                    <div
                        class="rounded-3xl border-2 border-dashed border-neutral-200 bg-gradient-to-br from-neutral-50 to-violet-50/30 py-24 text-center text-[17px] font-bold text-neutral-700">
                        {{ __('Bu il ve filtrede kayıt yok. Farklı bir il seçin ya da aramayı sıfırlayın.') }}</div>
                @endforelse
            </section>

            @if ($posts->total() > 0 && ! $posts->hasPages())
                <p class="pt-4 text-center text-[13px] font-semibold text-neutral-500">
                    {{ trans_choice(':count kayıt gösteriliyor', $posts->total(), ['count' => number_format($posts->total(), 0, ',', '.')]) }}</p>
            @endif

            <div class="flex justify-center">{{ $posts->links('pagination.load-more') }}</div>
        </div>

        <aside class="hidden space-y-4 xl:block">
            <x-ad-slot placement="feed_sidebar" />

            <section class="overflow-hidden rounded-2xl border border-neutral-200/80 bg-white shadow-sm">
                <div class="border-b border-neutral-100 bg-neutral-50/90 px-4 py-3">
                    <p class="text-[11px] font-black uppercase tracking-wider text-neutral-500">{{ __('Şeffaflık') }}</p>
                    <p class="mt-0.5 text-[15px] font-black text-neutral-950">{{ __('Neden güvenilir?') }}</p>
                </div>
                <ul class="space-y-3 px-4 py-4 text-[13px] font-medium leading-relaxed text-neutral-700">
                    <li class="flex gap-2">
                        <span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500" aria-hidden="true"></span>
                        <span>{{ __('Yayın öncesi moderasyon') }}</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-violet-500" aria-hidden="true"></span>
                        <span>{{ __('Konum ve etkileşime göre sıralama kuralları görünür') }}</span>
                    </li>
                </ul>
            </section>

            <section class="rounded-2xl border border-neutral-200/80 bg-white p-5 shadow-sm">
                <h3 class="text-[14px] font-black text-neutral-950">{{ __('Kategoriler') }}</h3>
                <p class="mt-1 text-[12px] font-medium text-neutral-500">{{ __('Tek dokunuşla filtrele') }}</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($categories as $cat)
                        <a href="{{ route('feed.index', array_merge($feedWithoutGeo, ['category_id' => $cat->id])) }}"
                            class="inline-flex items-center rounded-full border px-3 py-1.5 text-[12px] font-bold transition {{ request()->integer('category_id') === $cat->id ? 'border-violet-400 bg-violet-50 text-violet-950 ring-2 ring-violet-200' : 'border-neutral-200 bg-neutral-50 text-neutral-800 hover:border-violet-200 hover:bg-white' }}">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </section>
        </aside>
    </div>

    <x-story-viewer />
@endsection

@push('scripts')
    <script>
        window.__storiesFeed = {!! \Illuminate\Support\Js::from($storiesViewerPayload) !!};
    </script>
@endpush
