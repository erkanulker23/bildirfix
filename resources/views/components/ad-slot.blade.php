@props([
    'placement' => '',
    'slot' => '',
    'format' => 'auto',
    'layout' => null,
    'label' => __('Reklam'),
    'class' => '',
])

@php
    use App\Models\AdPlacement;

    $key = $placement !== '' ? $placement : '';
    $db = $key !== '' ? AdPlacement::findActive($key) : null;

    $adsenseClient = config('adsense.client');
    $adsenseEnabled = (bool) config('adsense.enabled');

    $slotId = $slot;
    $mediaUrl = null;
    $linkUrl = null;
    $mediaType = null;

    if ($db !== null) {
        $label = $db->label ?: $label;
        if ($db->type === 'adsense') {
            $slotId = $db->adsense_slot ?: (config('adsense.slots.'.$key) ?: config('adsense.default_slot'));
        } else {
            $mediaUrl = $db->media_url;
            $linkUrl = $db->link_url;
            $mediaType = $db->type;
        }
    } elseif ($key !== '' && $slotId === '') {
        $slotId = config('adsense.slots.'.$key) ?: config('adsense.default_slot');
    }

    $showAdsense = $adsenseEnabled && filled($adsenseClient) && filled($slotId) && ($db === null || $db->type === 'adsense');
    $showMedia = filled($mediaUrl) && in_array($mediaType, ['image', 'video'], true);
@endphp

@if ($showMedia)
    <aside {{ $attributes->merge(['class' => 'ad-slot my-4 overflow-hidden rounded-xl border border-neutral-200/90 bg-neutral-50/80 '.$class]) }}
        aria-label="{{ $label }}">
        <p class="px-3 pt-3 text-[10px] font-bold uppercase tracking-widest text-neutral-400">{{ $label }}</p>
        @if ($linkUrl)
            <a href="{{ $linkUrl }}" target="_blank" rel="noopener sponsored" class="block">
        @endif
        @if ($mediaType === 'video')
            <video src="{{ $mediaUrl }}" class="mx-auto max-h-64 w-full object-contain" controls playsinline></video>
        @else
            <img src="{{ $mediaUrl }}" alt="{{ $label }}" class="mx-auto max-h-64 w-full object-contain" loading="lazy">
        @endif
        @if ($linkUrl)
            </a>
        @endif
    </aside>
@elseif ($showAdsense)
    <aside {{ $attributes->merge(['class' => 'ad-slot my-4 flex min-h-[90px] flex-col items-center justify-center rounded-xl border border-dashed border-neutral-200/90 bg-neutral-50/80 px-3 py-3 '.$class]) }}
        aria-label="{{ $label }}">
        <p class="mb-2 text-[10px] font-bold uppercase tracking-widest text-neutral-400">{{ $label }}</p>
        <ins class="adsbygoogle block w-full"
            style="display:block"
            data-ad-client="{{ $adsenseClient }}"
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
