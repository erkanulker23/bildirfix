@props([
    'campaign',
])

@php
    /** @var \App\Models\Campaign $campaign */
    $sup = max(0, (int) $campaign->supporter_count);
    $goal = max(0, (int) ($campaign->goal_supporters ?? 0));
    $pct = $goal > 0 ? min(100, (int) round(100 * $sup / $goal)) : min(92, 8 + min(84, $sup * 3));
    $hero = trim((string) ($campaign->hero_image_url ?? ''));
@endphp

<a href="{{ route('campaigns.show', $campaign) }}"
    class="group flex h-[114px] w-[min(17.25rem,calc(100vw-2rem))] shrink-0 overflow-hidden rounded-xl border border-neutral-200/90 bg-white shadow-sm ring-1 ring-black/[0.03] transition hover:border-primary/40 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/45 focus-visible:ring-offset-2 sm:h-[118px] sm:w-[19.5rem]">
    <div class="relative h-full w-[96px] shrink-0 bg-neutral-100 sm:w-[104px]">
        @if ($hero !== '')
            <img src="{{ $hero }}" alt="" loading="lazy" decoding="async"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.04]">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary-light/90 via-white to-neutral-100">
                <span
                    class="rounded-full bg-white/95 px-2 py-0.5 text-[9px] font-black uppercase tracking-wide text-primary ring-1 ring-primary/25">{{ __('Kampanya') }}</span>
            </div>
        @endif
        <span
            class="absolute bottom-1 left-1 max-w-[calc(100%-0.5rem)] truncate rounded bg-white/95 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-neutral-800 shadow-sm ring-1 ring-neutral-200/60">
            @if ($campaign->relationLoaded('city') && $campaign->city)
                {{ $campaign->city->name }}
            @elseif ($campaign->city_id === null)
                {{ __('Türkiye') }}
            @else
                {{ __('Kent') }}
            @endif
        </span>
    </div>
    <div class="flex min-w-0 flex-1 flex-col justify-center gap-1.5 px-2.5 py-2 sm:px-3">
        <h3 class="line-clamp-2 text-[12px] font-black leading-[1.3] tracking-tight text-neutral-950 sm:text-[13px] group-hover:text-violet-700">
            {{ $campaign->title }}</h3>
        <div class="h-1.5 overflow-hidden rounded-full bg-violet-100/80">
            <div class="h-full rounded-full bg-violet-600 transition-all group-hover:bg-violet-500" style="width: {{ $pct }}%"></div>
        </div>
        <div class="flex items-center justify-between gap-2">
            <span class="inline-flex min-w-0 items-center gap-1 text-[11px] font-black tabular-nums text-violet-700 sm:text-[12px]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                    class="h-3.5 w-3.5 shrink-0 opacity-90" aria-hidden="true">
                    <path
                        d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.003-.002.001h-.002Z" />
                </svg>
                <span class="truncate">{{ number_format($sup) }} {{ __('destekçi') }}</span>
                @if ($goal > 0)
                    <span class="shrink-0 font-semibold text-neutral-400">/ {{ number_format($goal) }}</span>
                @endif
            </span>
            <span
                class="shrink-0 rounded-full bg-violet-600 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-white shadow-sm group-hover:bg-violet-700">{{ __('Destek ol') }}</span>
        </div>
    </div>
</a>
