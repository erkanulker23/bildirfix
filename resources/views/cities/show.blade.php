@extends('layouts.app')

@section('title', $city->name.' • '.__('İl şikâyetleri').' • '.config('app.name'))

@section('content')
    <div class="mb-4 flex flex-wrap items-center gap-2 sm:justify-between">
        <a href="{{ route('cities.explore') }}"
            class="rounded-2xl bg-white px-4 py-2 text-sm font-bold text-gray-800 shadow-sm ring-1 ring-gray-200 hover:bg-gray-50">
            ← {{ __('Şehir seç') }}</a>
        @if (! empty($city->plate))
            <span
                class="rounded-full bg-emerald-50 px-3 py-1.5 text-[11px] font-black tabular-nums text-emerald-900 ring-1 ring-emerald-100">{{ $city->plate }}</span>
        @endif
    </div>

    @include('partials.stories-strip', [
        'stories' => $cityStories,
        'title' => $city->name.' • '.__('Hikâyeler'),
        'moreHref' => route('feed.index', ['city_id' => $city->id]),
        'moreLabel' => __('Akışta gör'),
    ])

    <x-ad-slot placement="city_top" class="mx-auto max-w-[1200px]" />

    <div class="space-y-8">
        <header class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm ring-1 ring-black/[0.03] sm:p-8">
            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-primary">{{ __('İl sayfası') }}</p>
            <h1 class="mt-2 text-[clamp(1.5rem,3vw,2.25rem)] font-black tracking-tight text-gray-950">{{ $city->name }}</h1>
            <p class="mt-3 max-w-2xl text-[15px] font-medium leading-relaxed text-gray-600">
                {{ __('Bu sayfada yalnızca seçtiğin ile ait, süper yönetici onayından geçmiş şikâyet yayınları listelenir.') }}</p>
        </header>

        <section aria-labelledby="city-posts-heading">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 pb-3">
                <h2 id="city-posts-heading" class="text-lg font-black text-gray-950">{{ __('Yayına alınmış şikâyetler') }}</h2>
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-800">{{ $posts->total() }}</span>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($posts as $post)
                    <x-post-card :post="$post" :compact="true" />
                @empty
                    <p class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-6 py-12 text-center text-sm font-semibold text-gray-600">
                        {{ __('Bu il için henüz listelenecek onaylı şikâyet yok.') }}</p>
                @endforelse
            </div>

            <div class="flex justify-center pt-8">{{ $posts->links() }}</div>
        </section>
    </div>

    <x-story-viewer />
@endsection

@push('scripts')
    <script>
        window.__storiesFeed = {!! \Illuminate\Support\Js::from($storiesViewerPayload) !!};
    </script>
@endpush
