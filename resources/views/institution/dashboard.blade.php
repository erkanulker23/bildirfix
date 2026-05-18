@extends('layouts.panel', ['panelKind' => 'institution'])

@section('title', __('Dashboard'))

@section('panel_heading')
    <p class="text-sm font-bold text-gray-900">{{ __('Kurum paneli') }}</p>
@endsection

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="shell-page-head">
            <div>
                <h1 class="shell-page-title">{{ __('Kurum hesabı') }}</h1>
                <p class="shell-page-desc">
                    {{ $institution ? $institution->name : __('Kurum profilin henüz atanmadı.') }}
                </p>
            </div>
        </div>

        @if ($institution)
            <div class="shell-stat-grid sm:grid-cols-2">
                <div class="shell-metric">
                    <div>
                        <p class="shell-metric__label">{{ __('Şehir') }}</p>
                        <p class="shell-metric__value text-lg">{{ $institution->city?->name ?? '—' }}</p>
                    </div>
                </div>
                <div class="shell-metric">
                    <div>
                        <p class="shell-metric__label">{{ __('Durum') }}</p>
                        <p class="mt-2">
                            @if ($institution->verified)
                                <span class="shell-badge shell-badge--success">{{ __('Doğrulanmış') }}</span>
                            @else
                                <span class="shell-badge shell-badge--warn">{{ __('Taslak') }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="shell-card">
            <div class="shell-card-pad">
                <p class="text-sm text-gray-600">
                    {{ __('İlgili şikâyetleri yanıtlama ve durum güncelleme işlevleri sıradaki iterasyonda eklenecek.') }}
                </p>
            </div>
        </div>
    </div>
@endsection
