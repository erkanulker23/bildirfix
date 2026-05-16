@props([
    'lat',
    'lng',
    'zoom' => 16,
    'width' => 780,
    'height' => 340,
])

@php
    $latF = round((float) $lat, 6);
    $lngF = round((float) $lng, 6);
    $w = max(64, min(2048, (int) $width));
    $h = max(64, min(2048, (int) $height));
    $z = max(5, min(19, (int) $zoom));
    $q = http_build_query([
        'center' => $latF.','.$lngF,
        'zoom' => $z,
        'size' => $w.'x'.$h,
        'markers' => $latF.','.$lngF.',lightblue1',
    ]);
    $src = 'https://staticmap.openstreetmap.de/staticmap.php?'.$q;
    $osm = sprintf('https://www.openstreetmap.org/?mlat=%s&mlon=%s#map=%s/%s/%s', rawurlencode((string) $latF), rawurlencode((string) $lngF), rawurlencode((string) $z), rawurlencode((string) $latF), rawurlencode((string) $lngF));
@endphp

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-[1.25rem] border border-teal-100 bg-teal-50/40 shadow-inner ring-1 ring-teal-50']) }}>
    <a href="{{ $osm }}" target="_blank" rel="noopener noreferrer nofollow"
        class="relative block outline-none ring-teal-400 focus-visible:ring-2 focus-visible:ring-offset-2">
        <span class="sr-only">{{ __('Haritayı aç') }}</span>
        <img src="{{ $src }}" alt="{{ __('Şikâyet konumu • OpenStreetMap') }}" loading="lazy" decoding="async"
            width="{{ $w }}" height="{{ $h }}" fetchpriority="low"
            class="h-auto w-full object-cover">
    </a>
</div>
