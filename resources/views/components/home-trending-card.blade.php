@props([
    'post',
    'showMedia' => true,
])

@php
    /** @var \App\Models\Post $post */
    $pm = $showMedia ? \App\Support\PostMediaPresenter::primary($post) : null;
    $thumbUrl = null;
    $thumbIsVideo = false;
    if ($showMedia && $pm !== null) {
        $type = (string) ($pm['type'] ?? '');
        if ($type === 'video') {
            $poster = trim((string) ($pm['poster'] ?? ''));
            $thumbUrl = $poster !== '' ? $poster : null;
            $thumbIsVideo = true;
        } else {
            $url = trim((string) ($pm['url'] ?? ''));
            $thumbUrl = $url !== '' ? $url : null;
        }
    }

    $avatarPalette = ['bg-violet-600', 'bg-emerald-600', 'bg-indigo-600', 'bg-rose-500', 'bg-amber-500', 'bg-sky-600'];
    $uname = trim((string) ($post->user?->name ?? '?'));
    preg_match_all('/\p{L}/u', $uname, $um);
    $initial = (($um[0] ?? []) !== [])
        ? mb_strtoupper(implode('', array_slice($um[0], 0, 1)))
        : mb_strtoupper(mb_substr($uname, 0, 1));
    $avatarBg = $avatarPalette[crc32((string) $post->getKey()) % count($avatarPalette)];
    $targets = ($post->relationLoaded('institutions') && $post->institutions->isNotEmpty())
        ? $post->institutions
        : ($post->institution ? collect([$post->institution]) : collect());
    $inst = $targets->first();
    $targetLabel = $targets->count() > 1 && $inst
        ? $inst->name.' +' . (string) ($targets->count() - 1)
        : ($inst?->name ?? $post->city?->name ?? __('Kent geneli'));
    $heat = max(
        0,
        (int) $post->support_count * 24 + (int) $post->comments_count * 16 + (int) ($post->follow_count ?? 0) * 10,
    );
    $supportN = max(0, (int) $post->support_count);
    $commentsN = max(0, (int) $post->comments_count);
@endphp

@if ($showMedia)
    <article
        class="flex h-full flex-col overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm ring-1 ring-black/[0.02] transition hover:border-primary/30 hover:shadow-md">
        <a href="{{ route('posts.show', $post) }}" class="group flex min-h-0 flex-1 flex-col text-left outline-none focus-visible:ring-2 focus-visible:ring-primary/45 focus-visible:ring-offset-2">
            @if ($thumbUrl !== null)
                <div
                    class="relative h-36 shrink-0 overflow-hidden bg-gradient-to-br from-primary-light via-neutral-100 to-neutral-200">
                    <img src="{{ $thumbUrl }}" alt="" loading="lazy" decoding="async"
                        class="relative z-10 h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                        onerror="this.style.opacity='0'">
                    @if ($thumbIsVideo)
                        <span class="pointer-events-none absolute inset-0 flex items-center justify-center bg-neutral-950/20">
                            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white/95 text-base shadow-lg ring-1 ring-black/5"
                                aria-hidden="true">▶</span>
                        </span>
                    @endif
                </div>
            @else
                <div class="h-1 shrink-0 bg-gradient-to-r from-primary via-orange-400 to-primary-light" aria-hidden="true"></div>
            @endif

            <div class="flex flex-1 flex-col p-5">
                <div class="flex items-start gap-3.5">
                    <span
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-[13px] font-black text-white shadow-inner ring-2 ring-white {{ $avatarBg }}"
                        aria-hidden="true">{{ $initial }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[13px] font-bold text-neutral-900">{{ \Illuminate\Support\Str::limit($uname, 26) }}</p>
                        <p class="mt-0.5 truncate text-[12px] font-semibold text-neutral-600">
                            {{ \Illuminate\Support\Str::limit($targetLabel, 34) }}
                            @if ($inst && $inst->verified)
                                <span class="ml-1 inline-flex rounded bg-emerald-100 px-1.5 py-0.5 text-[9px] font-black uppercase text-emerald-900">{{ __('Onaylı') }}</span>
                            @endif
                        </p>
                        <p class="mt-1 text-[11px] font-semibold tabular-nums text-neutral-400"
                            title="{{ __('Destek, yorum ve takibe dayalı görünürlük göstergesi') }}">{{ __('Görünürlük') }}
                            {{ $heat > 0 ? number_format($heat, 0, ',', '.') : '—' }}</p>
                    </div>
                </div>

                <h3 class="mt-4 line-clamp-3 flex-1 text-[15px] font-black leading-snug tracking-tight text-neutral-950">
                    {{ $post->title }}</h3>

                @if ($post->category)
                    <p class="mt-4 text-[10px] font-bold uppercase tracking-wide text-primary">{{ $post->category->name }}</p>
                @endif
            </div>
        </a>
    </article>
@else
    {{-- Ana sayfa şeridi: görsel yok; modern platform kartı (kişi → hedef + etkileşim) --}}
    <article
        class="flex h-full min-h-[148px] flex-col overflow-hidden rounded-2xl border border-neutral-200/90 bg-white shadow-[0_10px_36px_-18px_rgba(15,23,42,0.14)] ring-1 ring-black/[0.03] transition hover:border-violet-300/70 hover:shadow-lg">
        <a href="{{ route('posts.show', $post) }}"
            class="group flex min-h-0 flex-1 flex-col text-left outline-none focus-visible:ring-2 focus-visible:ring-violet-500/45 focus-visible:ring-offset-2">
            <div class="flex flex-1 flex-col p-4">
                <div class="flex items-start gap-3">
                    <span
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-[12px] font-black text-white shadow-md ring-2 ring-white {{ $avatarBg }}"
                        aria-hidden="true">{{ $initial }}</span>
                    <div class="min-w-0 flex-1 space-y-2">
                        <p class="text-[11px] leading-snug text-neutral-800">
                            <span class="font-bold text-neutral-950">{{ \Illuminate\Support\Str::limit($uname, 16) }}</span>
                            <span class="mx-1 inline text-violet-500" aria-hidden="true">→</span>
                            <span class="font-semibold text-violet-700">{{ \Illuminate\Support\Str::limit($targetLabel, 24) }}</span>
                            @if ($inst && $inst->verified)
                                <span
                                    class="ml-1 align-middle inline-flex rounded bg-emerald-100 px-1 py-0.5 text-[8px] font-black uppercase text-emerald-900">{{ __('Onaylı') }}</span>
                            @endif
                        </p>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold tabular-nums text-rose-600"
                                title="{{ __('Destek sayısı') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5 opacity-90"
                                    aria-hidden="true">
                                    <path
                                        d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.003-.002.001h-.002Z" />
                                </svg>
                                {{ number_format($supportN) }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold tabular-nums text-emerald-600"
                                title="{{ __('Yorum') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                                    stroke="currentColor" class="h-3.5 w-3.5 opacity-90" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                                </svg>
                                {{ number_format($commentsN) }}
                            </span>
                        </div>
                    </div>
                </div>

                <h3 class="mt-3 line-clamp-3 text-[13px] font-black leading-snug tracking-tight text-neutral-900 group-hover:text-violet-700">
                    {{ $post->title }}</h3>

                <div class="mt-auto flex items-center justify-between gap-2 pt-3">
                    @if ($post->category)
                        <p class="text-[10px] font-bold uppercase tracking-wide text-violet-600">{{ $post->category->name }}</p>
                    @else
                        <span></span>
                    @endif
                    <p class="flex max-w-[55%] items-center gap-0.5 text-[10px] font-bold text-neutral-500">
                        <svg class="h-3 w-3 shrink-0 text-violet-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="truncate">{{ \Illuminate\Support\Str::limit($targetLabel, 20) }}</span>
                    </p>
                </div>
            </div>
        </a>
    </article>
@endif
