@extends('layouts.app')

@section('title', $post->title.' • '.config('app.name'))

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-end gap-3 sm:justify-end">
        <a href="{{ route('home') }}"
            class="rounded-2xl bg-white/80 px-4 py-2 text-sm font-bold text-teal-900 shadow-sm ring-1 ring-teal-100 hover:bg-white">
            ← {{ __('Akışa dön') }}
        </a>
    </div>
    @php
        $status = $post->status;
        $badge = match ($status) {
            \App\Enums\PostStatus::Open => 'bg-amber-50 text-amber-950 ring-amber-200',
            \App\Enums\PostStatus::InProgress => 'bg-sky-50 text-sky-950 ring-sky-200',
            \App\Enums\PostStatus::Resolved => 'bg-teal-50 text-teal-950 ring-teal-200',
            \App\Enums\PostStatus::Rejected => 'bg-slate-100 text-slate-700 ring-slate-300',
        };
        $__targets = ($post->relationLoaded('institutions') && $post->institutions->isNotEmpty())
            ? $post->institutions
            : ($post->institution ? collect([$post->institution]) : collect());
        $__galleryAll = \App\Support\PostMediaPresenter::all($post);
        $__coords = filled($post->latitude) && filled($post->longitude);
        $__mapsOpenHref = null;
        if ($__coords) {
            $__mapsOpenHref = sprintf(
                'https://www.google.com/maps?q=%s,%s&z=17&hl=%s',
                rawurlencode((string) round((float) $post->latitude, 7)),
                rawurlencode((string) round((float) $post->longitude, 7)),
                rawurlencode(str_replace('_', '-', app()->getLocale()))
            );
        }
        $__shareUrl = route('posts.show', $post, absolute: true);
    @endphp

    @if (! $post->isPubliclyApproved())
        <article class="mx-auto mb-8 max-w-6xl rounded-3xl border border-amber-200 bg-amber-50 px-6 py-4 text-sm text-amber-950 shadow-sm shadow-amber-100/70">
            <p class="font-bold">{{ __('Yayına alınma durumu') }}: {{ $post->moderation_status->label() }}</p>
            @if ($post->moderation_note)
                <p class="mt-2">{{ $post->moderation_note }}</p>
            @endif
        </article>
    @endif

    <div class="mx-auto max-w-6xl space-y-8">
        {{-- ≈ col-8 / col-4: flex 2:1, grid-col-span yerine (daha tutarlı) --}}
        <div class="flex flex-col gap-8 md:flex-row md:items-start md:gap-8">
            <div class="min-w-0 md:grow-[2] md:shrink md:basis-0">
                <article
                    class="rounded-[1.85rem] border border-white/80 bg-white/95 p-6 shadow-xl shadow-teal-500/10 ring-1 ring-teal-50 backdrop-blur-sm sm:p-10">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            @if ($post->category)
                                <span class="text-[11px] font-black uppercase tracking-[0.2em] text-teal-900">{{ $post->category->name }}</span>
                            @endif
                            <h1 class="mt-2 text-3xl font-black leading-tight text-slate-950 sm:text-4xl">{{ $post->title }}</h1>
                        </div>
                        <span class="rounded-full px-4 py-1.5 text-xs font-black uppercase tracking-wide ring-1 {{ $badge }}">{{ $status->label() }}</span>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-x-3 gap-y-2 text-xs font-semibold text-slate-600">
                        <span class="text-slate-900">{{ $post->user?->name }}</span>
                        <span class="inline-flex flex-wrap items-center gap-x-1.5 gap-y-1">
                            @if ($post->city)
                                <a href="{{ route('cities.show', $post->city) }}"
                                    class="font-bold text-teal-800 underline decoration-teal-200 underline-offset-2 hover:text-teal-950">{{ $post->city->name }}</a>
                            @endif
                            @if ($post->district)
                                <span class="text-slate-400" aria-hidden="true">•</span>
                                <a href="{{ route('feed.index', array_filter(['city_id' => $post->city_id, 'district_id' => $post->district_id])) }}"
                                    class="font-bold text-teal-800 underline decoration-teal-200 underline-offset-2 hover:text-teal-950">{{ $post->district->name }}</a>
                            @endif
                            @if ($post->neighborhood)
                                <span class="text-slate-400" aria-hidden="true">•</span>
                                <span>{{ $post->neighborhood->name }}</span>
                            @endif
                        </span>
                        @php
                            $pbShow = \App\Support\PublishTimeBadge::for($post->created_at);
                        @endphp
                        <time datetime="{{ $post->created_at->toIso8601String() }}" title="{{ $pbShow['title'] }}"
                            class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-black {{ $pbShow['class'] }}">{{ $pbShow['text'] }}</time>
                    </div>

                    @if ($post->description)
                        <div class="mt-9 space-y-4 text-[15px] leading-relaxed text-slate-700">
                            @foreach (preg_split('/\R{2,}/', (string) $post->description) ?: [] as $para)
                                @if (trim((string) $para) !== '')
                                    <p>{{ trim((string) $para) }}</p>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if (count($__galleryAll) > 0)
                        <section class="mt-10 border-t border-teal-100/80 pt-10" aria-labelledby="post-medya-baslik">
                            <h2 id="post-medya-baslik" class="mb-4 text-xs font-black uppercase tracking-[0.18em] text-teal-900">{{ __('Fotoğraf ve videolar') }}</h2>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @foreach ($__galleryAll as $gx)
                                    @php
                                        $gYt = null;
                                        $gUrl = (string) ($gx['url'] ?? '');
                                        if (($gx['type'] ?? '') === 'video' && $gUrl !== '') {
                                            preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([\w-]+)~', $gUrl, $gm);
                                            $gYt = $gm[1] ?? null;
                                        }
                                    @endphp
                                    @if (($gx['type'] ?? '') === 'video' && $gYt !== null && $gYt !== '')
                                        <div class="overflow-hidden rounded-2xl border border-teal-100 shadow-md sm:col-span-2">
                                            <iframe class="aspect-video w-full" src="https://www.youtube.com/embed/{{ $gYt }}?rel=0"
                                                title="{{ __('Video') }}" allowfullscreen loading="lazy"></iframe>
                                        </div>
                                    @elseif (($gx['type'] ?? '') === 'video')
                                        <div class="relative aspect-video overflow-hidden rounded-2xl border border-teal-100 bg-slate-900 shadow-md sm:col-span-2">
                                            @if (! empty($gx['poster'] ?? null))
                                                <img src="{{ $gx['poster'] }}" alt="" class="h-full w-full object-cover opacity-60" loading="lazy">
                                            @endif
                                            <a href="{{ $gx['url'] }}" target="_blank" rel="noopener noreferrer"
                                                class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-white">
                                                <span class="rounded-full bg-white/20 px-4 py-2 text-xs font-black uppercase">{{ __('videoyu aç') }}</span>
                                            </a>
                                        </div>
                                    @else
                                        <a href="{{ $gx['url'] }}" target="_blank" rel="noopener noreferrer"
                                            class="block overflow-hidden rounded-2xl ring-1 ring-teal-100">
                                            <img src="{{ $gx['url'] }}" alt="" class="max-h-80 w-full object-cover transition hover:brightness-105" loading="lazy">
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </section>
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

                    @php
                        $statGridClass = $__coords
                            ? 'grid grid-cols-2 gap-6 text-xs font-bold uppercase tracking-wide text-slate-800 sm:grid-cols-3'
                            : 'grid grid-cols-2 gap-6 text-xs font-bold uppercase tracking-wide text-slate-800 sm:grid-cols-4';
                    @endphp
                    <dl class="mt-10 {{ $statGridClass }}">
                        <div class="rounded-2xl bg-slate-50/80 px-4 py-3 ring-1 ring-slate-100">
                            <dt class="font-black text-teal-900">{{ __('Destek') }}</dt>
                            <dd class="mt-2 text-xl font-black text-teal-900">{{ number_format(max(0, (int) $post->support_count)) }}</dd>
                        </div>
                        <div class="rounded-2xl bg-indigo-50/80 px-4 py-3 ring-1 ring-indigo-100">
                            <dt class="font-black text-indigo-900">{{ __('Takip') }}</dt>
                            <dd class="mt-2 text-xl font-black text-indigo-900">{{ number_format(max(0, (int) $post->follow_count)) }}</dd>
                        </div>
                        <div class="rounded-2xl bg-sky-50/80 px-4 py-3 ring-1 ring-sky-100">
                            <dt class="font-black text-sky-950">{{ __('Yorum') }}</dt>
                            <dd class="mt-2 text-xl font-black text-sky-950">{{ number_format(max(0, (int) $post->comments_count)) }}</dd>
                        </div>
                        @if (! $__coords && $post->latitude && $post->longitude)
                            <div class="rounded-2xl bg-white px-4 py-3 ring-1 ring-slate-200">
                                <dt class="font-black text-slate-700">{{ __('Koordinat') }}</dt>
                                <dd class="mt-2 font-mono text-[11px] text-slate-900">{{ $post->latitude }}, {{ $post->longitude }}</dd>
                            </div>
                        @endif
                    </dl>
                </article>
            </div>

            {{-- Sağ: harita, sorumlusu (kurumlar), paylaşım — yapışkan panel --}}
            <aside
                class="min-w-0 space-y-6 md:min-w-[17.5rem] md:max-w-md md:grow md:shrink-0 md:basis-0 md:sticky md:top-28 md:self-start"
                aria-label="{{ __('Şikâyet yan paneli') }}">
                @if ($__coords)
                    <div class="rounded-2xl border border-teal-100/80 bg-white p-4 shadow-sm ring-1 ring-teal-50">
                        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-xs font-black uppercase tracking-[0.18em] text-teal-900">{{ __('Konum') }}</h2>
                            @if ($__mapsOpenHref)
                                <a href="{{ $__mapsOpenHref }}" target="_blank" rel="noopener noreferrer"
                                    class="text-[11px] font-bold text-teal-800 underline">{{ __('Haritada aç') }}</a>
                            @endif
                        </div>
                        <x-post-location-map :lat="$post->latitude" :lng="$post->longitude" :zoom="17" :width="640" :height="360"
                            class="!shadow-none" />
                        <p class="mt-3 font-mono text-[11px] font-medium text-slate-700">
                            {{ number_format((float) $post->latitude, 7) }}, {{ number_format((float) $post->longitude, 7) }}
                        </p>
                    </div>
                @endif

                @if ($__targets->isNotEmpty())
                    <div class="rounded-2xl border border-teal-100/80 bg-gradient-to-b from-teal-50/50 to-white p-5 shadow-sm ring-1 ring-teal-50">
                        <h2 class="text-xs font-black uppercase tracking-[0.18em] text-teal-900">{{ __('Sorumlusu') }}</h2>
                        <p class="mt-1 text-[11px] font-semibold text-slate-600">{{ __('Hedef birim veya kurum') }}</p>
                        <ul class="mt-4 space-y-3">
                            @foreach ($__targets as $__inst)
                                <li>
                                    <a href="{{ route('institutions.show', $__inst) }}"
                                        class="flex flex-col gap-1 rounded-xl border border-teal-200/70 bg-white/90 px-4 py-3 text-sm shadow-sm transition hover:border-teal-400 hover:shadow-md">
                                        <span class="text-[10px] font-black uppercase tracking-wider text-teal-700">{{ __('Hedef birim') }}</span>
                                        <span class="font-black text-teal-950">{{ $__inst->name }}</span>
                                        @if ($__inst->verified)
                                            <span class="text-[11px] font-bold text-teal-800">{{ __('Onaylı kurum') }}</span>
                                        @endif
                                        <span class="text-[11px] font-bold text-teal-700 underline">{{ __('Kurum sayfası →') }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <x-post-share-sidebar :url="$__shareUrl" :title="$post->title" />
            </aside>
        </div>

        @if ($post->isPubliclyApproved() && ($supportUsers->isNotEmpty() || $followUsers->isNotEmpty()))
            <div class="flex flex-col gap-6">
                <x-post-engagement-avatars :users="$supportUsers" :heading="__('Destek verenler')" accent="teal" />
                <x-post-engagement-avatars :users="$followUsers" :heading="__('Çözüm sürecini takip edenler')" accent="indigo" />
            </div>
        @endif

        <section id="yorumlar" class="scroll-mt-24">
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
    </div>
@endsection
