{{-- Full list: only "load more" control, no "Showing X to Y" --}}
@if ($paginator->hasPages())
    <nav class="flex flex-col items-center gap-3 pt-10 pb-2" role="navigation" aria-label="{{ __('Sayfalama') }}">
        @if ($paginator->onFirstPage())
            <span class="text-[13px] font-semibold text-neutral-500 tabular-nums">
                {{ trans_choice(':count kayıt listeleniyor', $paginator->total(), ['count' => number_format($paginator->total(), 0, ',', '.')]) }}
            </span>
        @endif
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
                class="inline-flex min-w-[14rem] items-center justify-center rounded-full bg-neutral-950 px-8 py-3.5 text-[13px] font-black uppercase tracking-wide text-white shadow-lg shadow-neutral-900/20 ring-2 ring-white transition hover:bg-neutral-800">
                {{ __('Daha fazla yükle') }}
            </a>
        @else
            @if (! $paginator->onFirstPage())
                <span class="text-[13px] font-semibold text-neutral-500">{{ __('Bu filtrede başka kayıt yok.') }}</span>
            @endif
        @endif
    </nav>
@endif
