@extends('layouts.panel', ['panelKind' => 'user'])

@section('title', __('Dashboard'))

@section('content')
    <div class="space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Hoş geldin, :name', ['name' => $user->name]) }}</h1>
                <p class="psc-page-desc">{{ __('Kampanyalarını yönet, yeni imza kampanyası başlat.') }}</p>
            </div>
            <div class="psc-page-actions">
                <a href="{{ route('campaigns.create') }}" class="psc-btn psc-btn--primary">
                    @include('partials.psc.icons', ['name' => 'plus'])
                    {{ __('Kampanya başlat') }}
                </a>
                <a href="{{ route('campaigns.index') }}" class="psc-btn psc-btn--secondary">{{ __('Kampanyalarım') }}</a>
            </div>
        </div>

        <div class="psc-metrics sm:grid-cols-3">
            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Puan') }}</p>
                    <p class="psc-metric__value">{{ number_format($user->score) }}</p>
                </div>
                <div class="psc-metric__icon psc-metric__icon--blue">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
            </div>
            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Güven') }}</p>
                    <p class="psc-metric__value">{{ number_format($user->trust_score) }}</p>
                </div>
                <div class="psc-metric__icon psc-metric__icon--green">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
            </div>
            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Rol') }}</p>
                    <p class="mt-2"><span class="psc-badge psc-badge--neutral">{{ $user->role->value }}</span></p>
                </div>
            </div>
        </div>

        <section class="psc-card">
            <div class="psc-card__body">
                <p class="text-sm leading-relaxed text-[#64748b]">
                    {{ __('Buradan yakında içerik oluşturma, bildirimler ve profilin yönetilecek.') }}
                </p>
                @if (!$user->managedInstitution)
                    <p class="mt-4 text-xs text-[#94a3b8]">
                        REST: {{ __('POST') }} /api/v1/posts — Authorization: Bearer &lt;sanctum&gt;
                    </p>
                @endif
            </div>
        </section>
    </div>
@endsection
