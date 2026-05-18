@extends('layouts.panel', ['panelKind' => 'institution'])

@section('title', __('Dashboard'))

@section('content')
    <div class="space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Kurum hesabı') }}</h1>
                <p class="psc-page-desc">
                    {{ $institution ? $institution->name : __('Kurum profilin henüz atanmadı.') }}
                </p>
            </div>
        </div>

        @if ($institution)
            <div class="psc-metrics sm:grid-cols-2">
                <div class="psc-metric">
                    <div>
                        <p class="psc-metric__label">{{ __('Şehir') }}</p>
                        <p class="psc-metric__value text-xl">{{ $institution->city?->name ?? '—' }}</p>
                    </div>
                    <div class="psc-metric__icon psc-metric__icon--blue">
                        @include('partials.psc.icons', ['name' => 'building'])
                    </div>
                </div>
                <div class="psc-metric">
                    <div>
                        <p class="psc-metric__label">{{ __('Durum') }}</p>
                        <p class="mt-2">
                            @if ($institution->verified)
                                <span class="psc-badge psc-badge--success">{{ __('Doğrulanmış') }}</span>
                            @else
                                <span class="psc-badge psc-badge--warn">{{ __('Taslak') }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <section class="psc-card">
            <div class="psc-card__body">
                <p class="text-sm leading-relaxed text-[#64748b]">
                    {{ __('İlgili şikâyetleri yanıtlama ve durum güncelleme işlevleri sıradaki iterasyonda eklenecek.') }}
                </p>
            </div>
        </section>
    </div>
@endsection
