@extends('layouts.app')

@section('title', $institution->name.' • '.__('Kurum kayıtları').' • '.config('app.name'))

@section('content')
    <div class="mb-10 space-y-8">
        <header class="rounded-3xl border border-white/80 bg-white/90 p-6 shadow-xl shadow-teal-500/10 ring-1 ring-teal-50 sm:p-8">
            <p class="text-xs font-black uppercase tracking-[0.3em] text-teal-800/65">{{ __('Kurum') }}</p>
            <div class="mt-3 flex flex-wrap items-start justify-between gap-4">
                <h1 class="text-3xl font-black leading-tight text-slate-950 sm:text-4xl">{{ $institution->name }}</h1>
                <a href="{{ route('home', array_filter(['city_id' => $institution->city?->id])) }}"
                    class="shrink-0 rounded-full border border-teal-200 bg-teal-50 px-5 py-2 text-xs font-black uppercase tracking-wide text-teal-950 shadow-sm">{{ __('Şehir akışına dön') }}</a>
            </div>
            @if ($institution->verified)
                <p class="mt-4 inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-950">{{ __('Doğrulanmış kurum') }}</p>
            @endif
        </header>

        <section>
            <div class="flex items-center justify-between border-b border-teal-100/80 pb-3">
                <h2 class="text-lg font-black text-teal-950">{{ __('Yayına alınmış şikâyetler') }}</h2>
                <span class="rounded-full bg-white/80 px-3 py-1 text-xs font-bold text-teal-900 shadow-sm ring-1 ring-teal-100">{{ $posts->total() }}</span>
            </div>

            <div class="mt-8 space-y-6">
                @forelse ($posts as $post)
                    @php
                        $badge = \App\Support\PublishTimeBadge::for($post->created_at);
                    @endphp
                    <article class="rounded-3xl border border-white/75 bg-white/90 p-5 shadow-lg shadow-teal-500/[0.06] ring-1 ring-teal-50 transition hover:border-teal-200/70 sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <a href="{{ route('posts.show', $post) }}"
                                class="min-w-0 flex-1 text-lg font-black leading-snug text-slate-950 hover:text-teal-800">{{ $post->title }}</a>
                            <span class="inline-flex shrink-0 items-center rounded-full px-2.5 py-1 text-[10px] font-black tracking-wide {{ $badge['class'] }}" title="{{ $badge['title'] }}">{{ $badge['text'] }}</span>
                        </div>
                        <p class="mt-3 line-clamp-2 text-sm leading-relaxed text-slate-600">{{ \Illuminate\Support\Str::limit(strip_tags((string) $post->description), 200) }}</p>
                        @if ($post->category || $post->city)
                            <p class="mt-3 text-[11px] font-bold text-teal-800">
                                {{ $post->city?->name }}@if ($post->district) • {{ $post->district->name }} @endif@if ($post->category) • {{ $post->category->name }}@endif</p>
                        @endif
                    </article>
                @empty
                    <p class="rounded-3xl border border-dashed border-teal-200 bg-white/60 p-10 text-center text-sm font-semibold text-slate-600">
                        {{ __('Bu kurum için yayınlanmış şikâyet görünmüyor.') }}</p>
                @endforelse
            </div>

            <div class="flex justify-center pt-8">{{ $posts->links() }}</div>
        </section>
    </div>
@endsection
