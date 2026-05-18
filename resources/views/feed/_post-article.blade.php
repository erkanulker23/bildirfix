@php
    $status = $post->status;
    $badge = match ($status) {
        \App\Enums\PostStatus::Open => 'bg-amber-100 text-amber-950 ring-1 ring-amber-200',
        \App\Enums\PostStatus::InProgress => 'bg-sky-50 text-sky-950 ring-1 ring-sky-200',
        \App\Enums\PostStatus::Resolved => 'bg-emerald-100 text-emerald-950 ring-1 ring-emerald-100',
        \App\Enums\PostStatus::Rejected => 'bg-neutral-100 text-neutral-900 ring-1 ring-neutral-200',
    };
    $pm = \App\Support\PostMediaPresenter::primary($post);
    $hasMedia = $pm !== null;
    $mediaType = $pm['type'] ?? 'image';
    $uname = trim((string) ($post->user?->name ?? '?'));
    $ini = '?';
    if (preg_match_all('/\p{L}/u', $uname, $__ch) && ($__ch[0] ?? []) !== []) {
        $slice = array_slice($__ch[0], 0, 2);
        $ini = mb_strtoupper(implode('', $slice));
    }
@endphp
<article
    class="relative overflow-hidden rounded-2xl border border-neutral-200/80 bg-white shadow-[0_8px_28px_-20px_rgba(15,23,42,0.2)] transition hover:border-violet-200/60 hover:shadow-[0_12px_36px_-18px_rgba(91,33,182,0.15)]">
    <span class="pointer-events-none absolute inset-y-4 left-0 w-1 rounded-full bg-gradient-to-b from-violet-500 via-fuchsia-500 to-emerald-400"
        aria-hidden="true"></span>

    <div class="flex flex-wrap gap-4 p-5 pb-3 pl-6">
        <div
            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 text-[13px] font-black text-white shadow-inner">
            {{ mb_substr($ini, 0, 2) }}
        </div>
        <div class="flex min-w-0 flex-1 flex-col gap-1">
            <div class="flex flex-wrap items-baseline gap-2">
                <span class="text-[16px] font-black text-neutral-900">{{ $uname }}</span>
                @if ($post->category)
                    <span class="truncate text-[12px] font-bold text-violet-900">{{ $post->category->name }}</span>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[12px] font-semibold text-neutral-600">
                <span class="inline-flex flex-wrap items-center gap-x-1">
                    @if ($post->city)
                        <a href="{{ route('cities.show', $post->city) }}"
                            class="font-bold text-violet-800 hover:underline">{{ $post->city->name }}</a>
                    @endif
                    @if ($post->district)
                        <span class="text-neutral-400" aria-hidden="true">·</span>
                        <a href="{{ route('feed.index', array_filter(['city_id' => $post->city_id, 'district_id' => $post->district_id])) }}"
                            class="font-bold text-violet-800 hover:underline">{{ $post->district->name }}</a>
                    @endif
                </span>
                @php
                    $pbFeed = \App\Support\PublishTimeBadge::for($post->created_at);
                @endphp
                <time datetime="{{ $post->created_at->toIso8601String() }}" title="{{ $pbFeed['title'] }}"
                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-black uppercase tracking-wide {{ $pbFeed['class'] }}">{{ $pbFeed['text'] }}</time>
            </div>
        </div>
        <span
            class="h-fit shrink-0 rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest ring-2 ring-black/[0.04] {{ $badge }}">{{ $status->label() }}</span>
    </div>

    <div class="px-8 pb-6 pl-10 pt-1">
        <div class="flex flex-wrap items-start gap-3">
            <a href="{{ route('posts.show', $post) }}"
                class="min-w-0 flex-1 text-[1.2rem] font-black leading-snug tracking-tight text-neutral-950 hover:text-violet-800">{{ $post->title }}</a>
            @if ($hasMedia)
                <span
                    class="inline-flex shrink-0 items-center gap-1 rounded-full bg-violet-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-violet-800 ring-1 ring-violet-100">
                    {{ $mediaType === 'video' ? __('Video') : __('Fotoğraf') }}
                </span>
            @elseif ($post->latitude !== null && $post->longitude !== null)
                <span
                    class="inline-flex shrink-0 items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-800 ring-1 ring-emerald-100">{{ __('Harita') }}</span>
            @endif
        </div>

        @php
            $__targets = ($post->relationLoaded('institutions') && $post->institutions->isNotEmpty())
                ? $post->institutions
                : ($post->institution ? collect([$post->institution]) : collect());
        @endphp
        @if ($__targets->isNotEmpty())
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($__targets->take(3) as $__inst)
                    <a href="{{ route('institutions.show', $__inst) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-neutral-50 px-3 py-1.5 text-[12px] font-semibold text-neutral-800 ring-1 ring-neutral-200 hover:bg-violet-50">
                        {{ \Illuminate\Support\Str::limit($__inst->name, 32) }}
                    </a>
                @endforeach
            </div>
        @endif

        <p class="mt-3 text-[15px] leading-relaxed text-neutral-700">{{ \Illuminate\Support\Str::limit(strip_tags((string) $post->description), 280) }}</p>

        @if ($hasMedia)
            <p class="mt-2 text-[12px] font-semibold text-violet-700">{{ __('Galeri ve tam görseller için gönderiye git →') }}</p>
        @endif

        @php
            $sup = number_format(max(0, (int) $post->support_count));
            $flw = number_format(max(0, (int) $post->follow_count));
        @endphp
        <p class="mt-3 text-[11px] font-semibold uppercase tracking-wide text-neutral-500">
            ❤️ {{ __('Topluluktan :s · takipçi :t', ['s' => $sup, 't' => $flw]) }}</p>

        <div class="mt-5 flex flex-wrap items-center gap-3 border-t border-neutral-100 pt-5">
            @auth
                <form method="POST" action="{{ route('posts.support.web', $post) }}" class="inline shrink-0">
                    @csrf
                    <button type="submit"
                        class="rounded-full px-5 py-2 text-[11px] font-black uppercase tracking-widest shadow-sm ring-2 ring-black/[0.04] transition {{ ! empty($post->viewer_supported) ? 'bg-violet-700 text-white' : 'bg-neutral-100 text-neutral-950 hover:bg-neutral-200' }}">{{ ! empty($post->viewer_supported) ? __('Destek gönderdin') : __('Destek') }}</button>
                </form>
                <form method="POST" action="{{ route('posts.follow.web', $post) }}" class="inline shrink-0">
                    @csrf
                    <button type="submit"
                        class="rounded-full px-5 py-2 text-[11px] font-black uppercase tracking-wide shadow-sm ring-2 ring-black/[0.04] transition {{ ! empty($post->viewer_following) ? 'bg-neutral-950 text-white' : 'border border-violet-200 bg-white text-neutral-950 hover:bg-violet-50' }}">{{ ! empty($post->viewer_following) ? __('Süreci izliyorum') : __('Süreci izle') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="text-[13px] font-black text-violet-800 underline decoration-4 underline-offset-4">{{ __('Giriş yap — destekle') }}</a>
            @endauth
            <a href="{{ route('posts.show', $post) }}"
                class="ml-auto inline-flex items-center justify-center rounded-full bg-neutral-950 px-5 py-2.5 text-[12px] font-black text-white hover:bg-neutral-900">{{ __('Detay →') }}</a>
        </div>
    </div>
</article>
