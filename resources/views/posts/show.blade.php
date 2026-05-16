@extends('layouts.app')

@section('title', $post->title.' • '.config('app.name'))

@section('toolbar')
    <div class="flex w-full flex-wrap items-center gap-3 sm:flex-1 sm:justify-end">
        <a href="{{ route('home') }}"
            class="rounded-2xl bg-white/80 px-4 py-2 text-sm font-bold text-teal-900 shadow-sm ring-1 ring-teal-100 hover:bg-white">
            ← {{ __('Akışa dön') }}
        </a>
    </div>
@endsection

@section('content')
    @php
        $status = $post->status;
        $badge = match ($status) {
            \App\Enums\PostStatus::Open => 'bg-amber-50 text-amber-950 ring-amber-200',
            \App\Enums\PostStatus::InProgress => 'bg-sky-50 text-sky-950 ring-sky-200',
            \App\Enums\PostStatus::Resolved => 'bg-teal-50 text-teal-950 ring-teal-200',
            \App\Enums\PostStatus::Rejected => 'bg-slate-100 text-slate-700 ring-slate-300',
        };
        $pm = \App\Support\PostMediaPresenter::primary($post);
        $ytId = null;
        if (! empty($pm['url'])) {
            preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([\w-]+)~', (string) $pm['url'], $y);
            $ytId = $y[1] ?? null;
        }
        $__coords = filled($post->latitude) && filled($post->longitude);
    @endphp

    @if (! $post->isPubliclyApproved())
        <article class="mx-auto mb-8 max-w-3xl rounded-3xl border border-amber-200 bg-amber-50 px-6 py-4 text-sm text-amber-950 shadow-sm shadow-amber-100/70">
            <p class="font-bold">{{ __('Yayına alınma durumu') }}: {{ $post->moderation_status->label() }}</p>
            @if ($post->moderation_note)
                <p class="mt-2">{{ $post->moderation_note }}</p>
            @endif
        </article>
    @endif

    @if ($pm)
        <div class="mx-auto mb-8 max-w-3xl overflow-hidden rounded-[1.85rem] border border-teal-100 shadow-2xl shadow-teal-900/25">
            @if (($pm['type'] ?? '') === 'video' && $ytId !== null && $ytId !== '')
                <iframe class="aspect-video w-full" src="https://www.youtube.com/embed/{{ $ytId }}?rel=0"
                    title="{{ __('Video') }}" allowfullscreen loading="lazy"></iframe>
            @elseif (($pm['type'] ?? '') === 'video')
                <div class="relative aspect-video bg-slate-900">
                    @if (! empty($pm['poster']))
                        <img src="{{ $pm['poster'] }}" alt="" class="h-full w-full object-cover opacity-60" loading="lazy">
                    @endif
                    <a href="{{ $pm['url'] }}" target="_blank" rel="noopener noreferrer"
                        class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-white">
                        <span class="rounded-full bg-white/20 px-4 py-2 text-xs font-black uppercase">{{ __('videoyu aç') }}</span>
                    </a>
                </div>
            @else
                <img src="{{ $pm['url'] }}" alt="{{ $post->title }}"
                    class="max-h-[32rem] w-full object-cover" loading="eager">
            @endif
        </div>
    @elseif ($__coords)
        <div class="mx-auto mb-8 max-w-3xl">
            <x-osm-static-map :lat="$post->latitude" :lng="$post->longitude" class="rounded-[1.85rem]" :width="900" :height="420"
                :zoom="16" />
        </div>
    @endif

    <article
        class="mx-auto max-w-3xl rounded-[1.85rem] border border-white/80 bg-white/95 p-6 shadow-xl shadow-teal-500/10 ring-1 ring-teal-50 backdrop-blur-sm sm:p-10">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0 flex-1">
                @if ($post->category)
                    <span class="text-[11px] font-black uppercase tracking-[0.2em] text-teal-900/65">{{ $post->category->name }}</span>
                @endif
                <h1 class="mt-2 text-3xl font-black leading-tight text-slate-950 sm:text-4xl">{{ $post->title }}</h1>
            </div>
            <span class="rounded-full px-4 py-1.5 text-xs font-black uppercase tracking-wide ring-1 {{ $badge }}">{{ $status->label() }}</span>
        </div>

        <div class="mt-5 flex flex-wrap gap-x-5 gap-y-2 text-xs font-semibold text-slate-600">
            <span class="text-slate-900">{{ $post->user?->name }}</span>
            <span>{{ $post->city?->name }}@if ($post->district) • {{ $post->district->name }}@endif @if ($post->neighborhood)
                    • {{ $post->neighborhood->name }}
                @endif</span>
            @php
                $pbShow = \App\Support\PublishTimeBadge::for($post->created_at);
            @endphp
            <time datetime="{{ $post->created_at->toIso8601String() }}" title="{{ $pbShow['title'] }}"
                class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-black {{ $pbShow['class'] }}">{{ $pbShow['text'] }}</time>
        </div>

        @if ($post->institution)
            <a href="{{ route('institutions.show', $post->institution) }}"
                class="mt-6 flex flex-wrap items-center gap-3 rounded-3xl border border-teal-200/80 bg-gradient-to-r from-teal-50/90 to-teal-100/50 px-5 py-4 text-sm shadow-inner shadow-teal-100/70 outline-none ring-teal-400 ring-offset-2 transition hover:border-teal-400 focus-visible:ring-2">
                <span class="rounded-2xl bg-teal-700 px-4 py-1.5 text-[11px] font-black uppercase tracking-widest text-white">{{ __('Hedef birim') }}</span>
                <span class="text-lg font-black text-teal-950">{{ $post->institution->name }}</span>
                @if ($post->institution->verified)
                    <span class="text-xs font-black text-teal-900">{{ __('Onaylı kurum') }}</span>
                @endif
                <span class="text-xs font-bold text-teal-800 underline">{{ __('kurum yayınlarını gör') }}</span>
            </a>
        @endif

        @if ($__coords && $pm)
            <div class="mx-auto mt-10 max-w-3xl">
                <div class="mb-3 flex items-center justify-between gap-3 px-2">
                    <h2 id="konum-etiket" class="text-sm font-black uppercase tracking-[0.18em] text-teal-900/65">{{ __('Şikâyet konumu') }}</h2>
            @php
                $osmHref = sprintf('https://www.openstreetmap.org/?mlat=%s&mlon=%s#map=16/%s/%s', $post->latitude, $post->longitude, $post->latitude, $post->longitude);
            @endphp
                    <a href="{{ $osmHref }}" target="_blank" rel="noopener noreferrer"
                        class="text-xs font-bold text-teal-800 underline">{{ __('Tam ekranda aç') }}</a>
                </div>
                <x-osm-static-map :lat="$post->latitude" :lng="$post->longitude" :width="780" :height="360" aria-labelledby="konum-etiket" />
            </div>
        @endif

        @if ($post->description)
            <div class="mt-9 space-y-4 text-[15px] leading-relaxed text-slate-700">
                @foreach (preg_split('/\R{2,}/', (string) $post->description) ?: [] as $para)
                    @if (trim((string) $para) !== '')
                        <p>{{ trim((string) $para) }}</p>
                    @endif
                @endforeach
            </div>
        @endif

        @if ($post->isPubliclyApproved())
            @auth
                <div class="mt-10 flex flex-wrap gap-4 border-y border-teal-100 py-8">
                    <form method="POST" action="{{ route('posts.support.web', $post) }}">
                        @csrf
                        <button type="submit"
                            class="{{ ! empty($post->viewer_supported) ? 'border-teal-800 bg-teal-800 text-white' : 'border-teal-200 bg-white hover:bg-teal-50/80' }} rounded-full border-2 px-7 py-3 text-xs font-black uppercase tracking-[0.2em] shadow-sm shadow-teal-500/25">
                            {{ ! empty($post->viewer_supported) ? __('Destek verildi') : __('Destek ver') }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('posts.follow.web', $post) }}">
                        @csrf
                        <button type="submit"
                            class="{{ ! empty($post->viewer_following) ? 'border-indigo-800 bg-indigo-800 text-white' : 'border-indigo-200 bg-white hover:bg-indigo-50/80' }} rounded-full border-2 px-7 py-3 text-xs font-black uppercase tracking-[0.18em] shadow-sm shadow-indigo-600/25">
                            {{ ! empty($post->viewer_following) ? __('Takip ediliyor') : __('Çözüm sürecini takip et') }}
                        </button>
                    </form>
                </div>
            @else
                <div class="mt-10 rounded-3xl border border-dashed border-teal-100 bg-teal-50/40 px-5 py-4 text-xs font-semibold text-teal-950">
                    <a href="{{ route('login') }}" class="underline">{{ __('Giriş yap') }}</a>
                    {{ __('— destek ve çözüm takibi için gereklidir.') }}
                </div>
            @endauth
        @endif

        <dl class="mt-10 grid grid-cols-2 gap-6 text-xs font-bold uppercase tracking-wide text-slate-800 sm:grid-cols-4">
            <div class="rounded-2xl bg-slate-50/80 px-4 py-3 ring-1 ring-slate-100">
                <dt class="font-black text-teal-900/65">{{ __('Destek') }}</dt>
                <dd class="mt-2 text-xl font-black text-teal-900">{{ number_format(max(0, (int) $post->support_count)) }}</dd>
            </div>
            <div class="rounded-2xl bg-indigo-50/80 px-4 py-3 ring-1 ring-indigo-100">
                <dt class="font-black text-indigo-900/65">{{ __('Takip') }}</dt>
                <dd class="mt-2 text-xl font-black text-indigo-900">{{ number_format(max(0, (int) $post->follow_count)) }}</dd>
            </div>
            <div class="rounded-2xl bg-sky-50/80 px-4 py-3 ring-1 ring-sky-100">
                <dt class="font-black text-sky-900/65">{{ __('Yorum') }}</dt>
                <dd class="mt-2 text-xl font-black text-sky-950">{{ number_format(max(0, (int) $post->comments_count)) }}</dd>
            </div>
            @if ($post->latitude && $post->longitude)
                <div class="rounded-2xl bg-white px-4 py-3 ring-1 ring-slate-200">
                    <dt class="font-black text-slate-500">{{ __('Koordinat') }}</dt>
                    <dd class="mt-2 font-mono text-[11px] text-slate-800">{{ $post->latitude }}, {{ $post->longitude }}</dd>
                </div>
            @endif
        </dl>
    </article>

    <section class="mx-auto mt-10 max-w-3xl">
        <h2 class="flex items-center gap-2 text-lg font-black text-slate-900">
            {{ __('Yorumlar') }}
            <span class="rounded-full bg-teal-100 px-2 py-1 text-[11px] font-black uppercase text-teal-900">{{ $comments->total() }}</span>
        </h2>
        <div class="mt-4 space-y-3">
            @forelse ($comments as $comment)
                <div class="rounded-3xl border border-white bg-white/90 p-5 shadow-md shadow-teal-500/[0.04] ring-1 ring-teal-50">
                    <div class="flex flex-wrap items-center justify-between gap-2 text-[11px] font-bold uppercase tracking-wide text-slate-600">
                        <span class="text-slate-900">{{ $comment->user?->name }}</span>
                        <span>{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mt-3 text-[15px] leading-relaxed text-slate-800">{{ $comment->content }}</p>
                </div>
            @empty
                <p class="text-sm font-medium text-slate-500">{{ __('Henüz yorum yok — ilk sessiz tepkiyi yaz.') }}</p>
            @endforelse
        </div>
        <div class="mt-6 flex justify-center">
            {{ $comments->links() }}
        </div>
    </section>
@endsection
