@props([
    'type' => 'card',
    'count' => 3,
])

@for ($i = 0; $i < $count; $i++)
    @if ($type === 'card')
        <div class="card-post overflow-hidden" aria-hidden="true">
            <div class="flex items-center gap-3 px-4 pb-3 pt-4">
                <div class="h-11 w-11 shrink-0 animate-shimmer rounded-full bg-gray-200"></div>
                <div class="min-w-0 flex-1 space-y-2">
                    <div class="h-3.5 w-28 animate-shimmer rounded bg-gray-200"></div>
                    <div class="h-3 w-24 animate-shimmer rounded bg-gray-200"></div>
                </div>
            </div>
            <div class="aspect-video w-full animate-shimmer bg-gray-200"></div>
            <div class="space-y-2 px-4 py-3">
                <div class="h-3 w-16 animate-shimmer rounded-full bg-gray-200"></div>
                <div class="h-4 w-full animate-shimmer rounded bg-gray-200"></div>
                <div class="h-4 w-[75%] animate-shimmer rounded bg-gray-200"></div>
            </div>
            <div class="flex items-center gap-4 border-t border-gray-100 px-4 py-3">
                <div class="h-9 w-16 animate-shimmer rounded-full bg-gray-200"></div>
                <div class="h-9 w-14 animate-shimmer rounded-full bg-gray-200"></div>
            </div>
        </div>
    @elseif ($type === 'story')
        <div class="flex shrink-0 snap-item-start flex-col items-center gap-1.5">
            <div class="h-[72px] w-[72px] animate-shimmer rounded-full bg-gray-200"></div>
            <div class="h-3 w-12 animate-shimmer rounded bg-gray-200"></div>
        </div>
    @endif
@endfor
