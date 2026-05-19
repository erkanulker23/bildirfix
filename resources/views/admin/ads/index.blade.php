@extends('layouts.admin')

@section('title', __('Reklam alanları'))

@section('content')
    <div>
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Reklam alanları') }}</h1>
                <p class="psc-page-desc">
                    {{ __('Google AdSense veya özel medya. AdSense:') }}
                    @if ($adsenseEnabled && filled($adsenseClient))
                        <span class="font-semibold text-emerald-700">{{ __('yapılandırıldı') }}</span>
                    @else
                        <span class="font-semibold text-amber-700">{{ __('kapalı / eksik') }}</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="psc-ad-grid">
            @foreach ($placements as $p)
                <article class="psc-ad-card">
                    <h2 class="psc-ad-card__title">{{ $p->label }}</h2>
                    <p class="psc-ad-card__meta">
                        <code>{{ $p->key }}</code><br>
                        {{ __('Tür') }}: {{ $p->type }}
                    </p>
                    <div class="psc-ad-card__foot">
                        @if ($p->is_active)
                            <span class="psc-badge psc-badge--success">{{ __('Aktif') }}</span>
                        @else
                            <span class="psc-badge psc-badge--neutral">{{ __('Pasif') }}</span>
                        @endif
                        <a href="{{ route('admin.ads.edit', $p) }}" class="psc-btn psc-btn--primary">{{ __('Düzenle') }}</a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
@endsection
