@props([
    'story' => null,
    'isAdd' => false,
    'isViewed' => false,
])

@php
    use App\Enums\UserRole;
@endphp

@if ($isAdd)
    <button type="button" onclick="window.dsOpenCreateStory?.()"
        class="flex shrink-0 snap-item-start flex-col items-center gap-1.5">
        <div class="relative h-[72px] w-[72px]">
            <div
                class="flex h-full w-full items-center justify-center rounded-full border-2 border-dashed border-gray-300 bg-gray-100 transition-colors hover:border-primary hover:bg-primary-light">
                @auth
                    @php
                        $n = trim((string) (auth()->user()->name ?? '?'));
                        preg_match_all('/\p{L}/u', $n, $mm);
                        $iu = (($mm[0] ?? []) !== []) ? mb_strtoupper(implode('', array_slice($mm[0], 0, 2))) : mb_strtoupper(mb_substr($n, 0, 2));
                    @endphp
                    <span
                        class="font-heading flex h-[64px] w-[64px] items-center justify-center rounded-full bg-white/80 text-sm font-bold text-gray-500">{{ mb_substr($iu ?: '?', 0, 2) }}</span>
                @else
                    <svg class="h-7 w-7 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                @endauth
            </div>
            <div
                class="absolute -bottom-0.5 -right-0.5 flex h-5 w-5 items-center justify-center rounded-full border-2 border-white bg-primary shadow-sm">
                <svg class="h-2.5 w-2.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="3" aria-hidden="true">
                    <path d="M12 5v14M5 12h14" />
                </svg>
            </div>
        </div>
        <span class="max-w-[72px] truncate text-center text-[11px] font-medium leading-tight text-gray-600">{{ __('Hikâye') }}</span>
    </button>
@elseif ($story)
    @php
        $sname = trim((string) ($story->user?->name ?? '?'));
        preg_match_all('/\p{L}/u', $sname, $sm);
        $si = (($sm[0] ?? []) !== []) ? mb_strtoupper(implode('', array_slice($sm[0], 0, 2))) : mb_strtoupper(mb_substr($sname, 0, 2));
        $si = mb_substr($si ?: '?', 0, 2);
        $isInstitution = ($story->user?->role ?? null) === UserRole::Institution;
        $thumb = $story->media_url;
    @endphp
    <button type="button" onclick="window.dsOpenStory?.({{ $story->id }}, window.__storiesFeed || [])"
        class="flex shrink-0 snap-item-start flex-col items-center gap-1.5">
        <div class="relative h-[72px] w-[72px] rounded-full p-[3px] {{ $isViewed ? 'bg-gray-200' : 'story-ring-unread' }}">
            <div class="h-full w-full rounded-full bg-white p-[2px]">
                @if ($thumb)
                    <img src="{{ $thumb }}" alt="" class="h-[64px] w-[64px] rounded-full object-cover"
                        loading="lazy">
                @else
                    <span
                        class="font-heading flex h-[64px] w-[64px] items-center justify-center rounded-full bg-gradient-to-br from-primary to-orange-400 text-sm font-bold text-white">{{ $si }}</span>
                @endif
            </div>
            @if ($isInstitution)
                <div
                    class="absolute -bottom-0.5 -right-0.5 flex h-5 w-5 items-center justify-center rounded-full border-2 border-white bg-info">
                    <svg class="h-2.5 w-2.5 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    </svg>
                </div>
            @endif
        </div>
        <span
            class="max-w-[72px] truncate text-center text-[11px] font-medium leading-tight {{ $isViewed ? 'text-gray-400' : 'text-gray-700' }}">{{ \Illuminate\Support\Str::limit($sname, 10) }}</span>
    </button>
@endif
