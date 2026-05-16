@props([
    'lat',
    'lng',
    'zoom' => 17,
    'width' => 780,
    'height' => 360,
])

@php
    $key = trim((string) config('services.google.maps_api_key', ''));
    $latF = round((float) $lat, 7);
    $lngF = round((float) $lng, 7);
    $z = max(5, min(21, (int) $zoom));

    $googleEmbed = $key !== ''
        ? 'https://www.google.com/maps/embed/v1/place?key='.rawurlencode($key).'&q='.rawurlencode($latF.','.$lngF).'&zoom='.$z
        : null;

    // OSM canlı harita (API anahtarı gerekmez). staticmap.openstreetmap.de sık kesildiği için iframe kullanıyoruz.
    $dlat = 0.004 * max(0.35, pow(2, max(0, 17 - min(19, $z))));
    $dlng = $dlat / max(0.2, cos(deg2rad(max(-85.0, min(85.0, $latF)))));
    $minLon = $lngF - $dlng;
    $minLat = $latF - $dlat;
    $maxLon = $lngF + $dlng;
    $maxLat = $latF + $dlat;
    $bboxStr = $minLon.','.$minLat.','.$maxLon.','.$maxLat;
    $osmEmbed = 'https://www.openstreetmap.org/export/embed.html?bbox='.rawurlencode($bboxStr).'&layer=mapnik&marker='.$latF.'%2C'.$lngF;
    $osmFull = sprintf(
        'https://www.openstreetmap.org/?mlat=%s&mlon=%s#map=%s/%s/%s',
        rawurlencode((string) $latF),
        rawurlencode((string) $lngF),
        rawurlencode((string) $z),
        rawurlencode((string) $latF),
        rawurlencode((string) $lngF),
    );
@endphp

<div
    {{ $attributes->merge(['class' => 'overflow-hidden rounded-[1.25rem] border border-teal-100 bg-teal-50/40 shadow-inner ring-1 ring-teal-50']) }}>
    <div class="relative aspect-video w-full min-h-[240px] max-h-[32rem] bg-neutral-200/50">
        @if ($googleEmbed !== null)
            <iframe title="{{ __('Şikâyet konumu — Google Haritalar') }}" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade" class="absolute inset-0 h-full w-full border-0"
                allowfullscreen src="{{ $googleEmbed }}"></iframe>
        @else
            <iframe title="{{ __('Şikâyet konumu — OpenStreetMap') }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                class="absolute inset-0 h-full w-full border-0" src="{{ $osmEmbed }}"></iframe>
        @endif
    </div>
    <div class="flex flex-wrap items-center justify-between gap-2 border-t border-teal-100/80 bg-white/90 px-3 py-2 text-xs font-semibold text-teal-900">
        <span>{{ __('İşaretli nokta bildirim koordinatıdır.') }}</span>
        <a href="{{ $osmFull }}" target="_blank" rel="noopener noreferrer"
            class="font-bold text-teal-800 underline underline-offset-2 hover:text-teal-950">{{ __('Haritayı tam ekran aç') }}</a>
    </div>
</div>
