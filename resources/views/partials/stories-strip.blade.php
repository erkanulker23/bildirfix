@props([
    'stories',
    'title' => __('Kent hikâyeleri'),
    'moreHref' => null,
    'moreLabel' => __('Tüm hikâyeler'),
])

<section class="mb-6 scroll-mt-4" aria-label="{{ __('Hikâyeler') }}">
    <div class="rounded-2xl border border-neutral-200/80 bg-white px-3 py-3 shadow-sm ring-1 ring-black/[0.03] sm:px-4 sm:py-3.5">
        <div class="mb-2 flex flex-wrap items-center justify-between gap-2 px-1">
            <p class="text-[11px] font-black uppercase tracking-wider text-emerald-800">{{ $title }}</p>
            @if ($moreHref)
                <a href="{{ $moreHref }}"
                    class="text-[11px] font-bold text-violet-700 underline-offset-2 hover:underline">{{ $moreLabel }}</a>
            @endif
        </div>
        <div
            class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-1 pt-1 [-ms-overflow-style:none] [scrollbar-width:thin] [&::-webkit-scrollbar]:h-1.5 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-neutral-300"
            style="-webkit-overflow-scrolling:touch">
            <div class="snap-start shrink-0">
                <x-story-circle :is-add="true" />
            </div>
            @foreach ($stories as $story)
                <div class="snap-start shrink-0">
                    <x-story-circle :story="$story" :is-viewed="false" />
                </div>
            @endforeach
        </div>
        @if ($stories->isEmpty())
            <p class="mt-2 px-1 text-[12px] font-medium text-neutral-500">{{ __('Bu bölgede henüz hikâye yok.') }}</p>
        @endif
    </div>
</section>
