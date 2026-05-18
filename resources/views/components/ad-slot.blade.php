@props([
    'slot' => '',
    'format' => 'auto',
    'layout' => null,
    'label' => __('Reklam'),
    'class' => '',
])

@php
    $enabled = config('adsense.enabled') && filled(config('adsense.client'));
    $slotId = $slot !== '' ? $slot : config('adsense.default_slot');
@endphp

@if ($enabled && filled($slotId))
    <aside {{ $attributes->merge(['class' => 'ad-slot my-4 flex min-h-[90px] flex-col items-center justify-center rounded-xl border border-dashed border-neutral-200/90 bg-neutral-50/80 px-3 py-3 '.$class]) }}
        aria-label="{{ $label }}">
        <p class="mb-2 text-[10px] font-bold uppercase tracking-widest text-neutral-400">{{ $label }}</p>
        <ins class="adsbygoogle block w-full"
            style="display:block"
            data-ad-client="{{ config('adsense.client') }}"
            data-ad-slot="{{ $slotId }}"
            data-ad-format="{{ $format }}"
            @if ($layout) data-ad-layout="{{ $layout }}" @endif
            data-full-width-responsive="true"></ins>
    </aside>
    @push('scripts')
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    @endpush
@endif
