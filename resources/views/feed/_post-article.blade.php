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
    class="relative overflow-hidden rounded-2xl border border-neutral-200/80 bg-white shadow-[0_12px_40px_-28px_rgba(15,23,42,0.22)] transition hover:border-violet-200/60 hover:shadow-[0_18px_50px_-24px_rgba(91,33,182,0.18)]">
    <span class="pointer-events-none absolute inset-y-4 left-0 w-1 rounded-full bg-gradient-to-b from-violet-500 via-fuchsia-500 to-emerald-400"
        aria-hidden="true"></span>

    <div class="flex flex-wrap gap-4 p-5 pb-2 pl-6">
        <div
            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 text-[14px] font-black text-white shadow-inner">
            {{ mb_substr($ini, 0, 2) }}
        </div>
        <div class="flex min-w-0 flex-1 flex-col gap-1">
            <div class="flex flex-wrap items-baseline gap-2">
                <span class="text-[17px] font-black text-neutral-900">{{ $uname }}</span>
                <span class="text-[13px] text-neutral-700">{{ __('şikâyet bildirdi •') }}</span>
                @if ($post->category)
                    <span class="truncate text-[13px] font-bold text-violet-900">{{ $post->category->name }}</span>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[12px] font-semibold text-neutral-700">
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
                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-black uppercase tracking-wide {{ $pbFeed['class'] }}">{{ $pbFeed['text'] }}</time>
            </div>
        </div>
        <span
            class="h-fit shrink-0 rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest ring-2 ring-black/[0.04] {{ $badge }}">{{ $status->label() }}</span>
    </div>

    @if ($pm)
        <div class="-mt-px pl-2 pr-2">
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
                    class="mx-auto -mt-[2.95rem] ml-8 inline-flex rounded-full bg-white/92 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-violet-900 shadow">{{ __('medya') }}</span>
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
        <div class="px-4 pb-5 pl-6">
            <a href="{{ route('posts.show', $post) }}" class="relative block overflow-hidden rounded-2xl ring-2 ring-neutral-900/[0.04]">
                <img src="{{ $__mapSrc }}" alt="{{ __('Konum özeti • detay için tıklayın') }}" width="680" height="220" decoding="async" loading="lazy" class="aspect-[2.4/1] w-full object-cover">
                <span
                    class="absolute left-4 top-3 rounded-full bg-violet-600 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-white shadow">{{ __('harita konumu') }}</span>
            </a>
        </div>
    @endif

    <div class="{{ $pm ? 'px-8 pb-8 pt-2 pl-10' : 'px-8 pb-8 pt-1 pl-10' }}">
        <div class="-mt-1 flex flex-wrap items-start gap-4">
            <a href="{{ route('posts.show', $post) }}"
                class="min-w-0 flex-1 text-[1.375rem] font-black leading-snug tracking-tight text-neutral-950 hover:text-violet-800">{{ $post->title }}</a>
        </div>

        @php
            $__targets = ($post->relationLoaded('institutions') && $post->institutions->isNotEmpty())
                ? $post->institutions
                : ($post->institution ? collect([$post->institution]) : collect());
        @endphp
        @if ($__targets->isNotEmpty())
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($__targets as $__inst)
                    <a href="{{ route('institutions.show', $__inst) }}"
                        class="inline-flex flex-wrap items-center gap-2 rounded-xl bg-neutral-900/[0.04] px-4 py-2.5 text-[13px] font-semibold text-neutral-900 ring-2 ring-transparent transition hover:bg-violet-50 hover:ring-violet-200">
                        <span
                            class="rounded-lg bg-neutral-950 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-white">{{ __('Birim') }}</span>
                        <span class="font-bold">{{ $__inst->name }}</span>
                        @if ($__inst->verified)
                            <span class="text-[11px] font-black text-emerald-800">{{ __('Onaylı') }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
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
                        class="rounded-full px-6 py-2.5 text-[12px] font-black uppercase tracking-widest shadow-sm ring-4 ring-black/[0.04] transition {{ ! empty($post->viewer_supported) ? 'bg-violet-700 text-white' : 'bg-[#eef0f3] text-neutral-950 hover:bg-[#dfe3ea]' }}">{{ ! empty($post->viewer_supported) ? __('Destek gönderdin') : __('Destek') }}</button>
                </form>
                <form method="POST" action="{{ route('posts.follow.web', $post) }}" class="inline shrink-0">
                    @csrf
                    <button type="submit"
                        class="rounded-full px-6 py-2.5 text-[12px] font-black uppercase tracking-wide shadow-sm ring-4 ring-black/[0.04] transition {{ ! empty($post->viewer_following) ? 'border-4 border-transparent bg-neutral-950 text-white' : 'border-4 border-violet-200 bg-white text-neutral-950 hover:bg-violet-50' }}">{{ ! empty($post->viewer_following) ? __('Süreci izliyorum') : __('Süreci izle') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="text-[13px] font-black text-violet-800 underline decoration-4 underline-offset-4">{{ __('Giriş yap — destekle') }}</a>
            @endauth
            <a href="{{ route('posts.show', $post) }}"
                class="ml-auto inline-flex min-w-[180px] items-center justify-center rounded-full bg-neutral-950 px-6 py-3 text-[13px] font-black text-white hover:bg-neutral-900 sm:justify-center">{{ __('Gönderiye git →') }}</a>
        </div>
    </div>
</article>
