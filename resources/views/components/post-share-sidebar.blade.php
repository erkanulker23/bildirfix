@props([
    'url',
    'title',
    'heading' => null,
])

@php
    $shareUrl = trim((string) $url);
    $shareTitle = trim((string) $title);
    if ($shareUrl === '') {
        $shareUrl = url()->current();
    }
    $encUrl = rawurlencode($shareUrl);
    $encTitle = rawurlencode($shareTitle);
    $waHref = 'https://wa.me/?text='.rawurlencode($shareTitle !== '' ? $shareTitle.' — '.$shareUrl : $shareUrl);
    $xHref = 'https://twitter.com/intent/tweet?url='.$encUrl.($encTitle !== '' ? '&text='.$encTitle : '');
    $fbHref = 'https://www.facebook.com/sharer/sharer.php?u='.$encUrl;
@endphp

<section {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200/90 bg-white p-5 shadow-sm ring-1 ring-slate-100']) }}
    aria-labelledby="post-share-sidebar-heading">
    <h2 id="post-share-sidebar-heading" class="text-sm font-black uppercase tracking-[0.14em] text-slate-900">{{ $heading ?? __('Paylaşım') }}</h2>
    <p class="mt-2 text-xs font-medium leading-relaxed text-slate-600">{{ __('Bağlantıyı kopyalayın veya doğrudan paylaşın — hesap gerekmez.') }}</p>

    <div class="mt-4 flex flex-col gap-2">
        <button type="button"
            class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-teal-700 px-4 text-xs font-black uppercase tracking-wide text-white shadow-sm transition hover:bg-teal-800"
            onclick="window.dsSharePage(@js($shareUrl), @js($shareTitle))">
            {{ __('Paylaş veya bağlantıyı kopyala') }}
        </button>

        <div class="grid grid-cols-2 gap-2 pt-1">
            <a href="{{ $waHref }}" target="_blank" rel="noopener noreferrer"
                class="inline-flex min-h-10 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-2 text-center text-[11px] font-bold text-slate-800 transition hover:bg-white">{{ __('WhatsApp') }}</a>
            <a href="{{ $xHref }}" target="_blank" rel="noopener noreferrer"
                class="inline-flex min-h-10 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-2 text-center text-[11px] font-bold text-slate-800 transition hover:bg-white">X</a>
            <a href="{{ $fbHref }}" target="_blank" rel="noopener noreferrer"
                class="col-span-2 inline-flex min-h-10 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-2 text-center text-[11px] font-bold text-slate-800 transition hover:bg-white">{{ __('Facebook') }}</a>
        </div>
    </div>
</section>
