@extends('layouts.admin')

@section('title', __('Dashboard'))

@php
    $iconTone = static fn (string $tone): string => match ($tone) {
        'blue' => 'psc-metric__icon--blue',
        'orange' => 'psc-metric__icon--orange',
        'green' => 'psc-metric__icon--green',
        'red' => 'psc-metric__icon--red',
        'violet' => 'psc-metric__icon--violet',
        'cyan' => 'psc-metric__icon--cyan',
        default => 'psc-metric__icon--blue',
    };
@endphp

@section('content')
    <div class="space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Dashboard') }}</h1>
                <p class="psc-page-desc">{{ __('Operasyon ve onay özeti — yayın akışı süper yönetici onayına bağlıdır.') }}</p>
            </div>
            <div class="psc-page-actions">
                @if ($viewerIsSuperAdmin)
                    <a href="{{ route('admin.moderation.index') }}" class="psc-btn psc-btn--primary">
                        @include('partials.psc.icons', ['name' => 'complaint'])
                        {{ __('Şikâyet moderasyonu') }}
                    </a>
                @endif
                <a href="{{ route('admin.blog.create') }}" class="psc-btn psc-btn--secondary">{{ __('Yeni blog') }}</a>
            </div>
        </div>

        <div class="psc-metrics">
            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Toplam şikâyet') }}</p>
                    <p class="psc-metric__value">{{ number_format($postsCount) }}</p>
                </div>
                <div class="psc-metric__icon {{ $iconTone('blue') }}">
                    @include('partials.psc.icons', ['name' => 'complaint'])
                </div>
            </div>
            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Açık şikâyet') }}</p>
                    <p class="psc-metric__value">{{ number_format($openPosts) }}</p>
                </div>
                <div class="psc-metric__icon {{ $iconTone('orange') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Yayındaki şikâyet') }}</p>
                    <p class="psc-metric__value">{{ number_format($approvedPostsCount) }}</p>
                </div>
                <div class="psc-metric__icon {{ $iconTone('green') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Bekleyen şikâyet') }}</p>
                    <p class="psc-metric__value">{{ number_format($pendingModeration) }}</p>
                    @if ($viewerIsSuperAdmin)
                        <a href="{{ route('admin.moderation.index') }}" class="psc-metric__meta">{{ __('Moderasyon') }}</a>
                    @endif
                </div>
                <div class="psc-metric__icon {{ $iconTone('red') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Toplam kampanya') }}</p>
                    <p class="psc-metric__value">{{ number_format($campaignsCount) }}</p>
                </div>
                <div class="psc-metric__icon {{ $iconTone('violet') }}">
                    @include('partials.psc.icons', ['name' => 'campaign'])
                </div>
            </div>
            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Yayındaki kampanya') }}</p>
                    <p class="psc-metric__value">{{ number_format($approvedCampaignsCount) }}</p>
                </div>
                <div class="psc-metric__icon {{ $iconTone('green') }}">
                    @include('partials.psc.icons', ['name' => 'campaign'])
                </div>
            </div>
            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Bekleyen kampanya') }}</p>
                    <p class="psc-metric__value">{{ number_format($pendingCampaignModeration) }}</p>
                    @if ($viewerIsSuperAdmin)
                        <a href="{{ route('admin.campaign-moderation.index') }}" class="psc-metric__meta">{{ __('Moderasyon') }}</a>
                    @endif
                </div>
                <div class="psc-metric__icon {{ $iconTone('orange') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Kullanıcılar') }}</p>
                    <p class="psc-metric__value">{{ number_format($usersCount) }}</p>
                    @if ($viewerIsSuperAdmin)
                        <a href="{{ route('admin.users.index') }}" class="psc-metric__meta">{{ __('Liste') }}</a>
                    @endif
                </div>
                <div class="psc-metric__icon {{ $iconTone('cyan') }}">
                    @include('partials.psc.icons', ['name' => 'users'])
                </div>
            </div>

            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Kurumlar') }}</p>
                    <p class="psc-metric__value">{{ number_format($institutionsCount) }}</p>
                    @if ($viewerIsSuperAdmin)
                        <a href="{{ route('admin.institutions.index') }}" class="psc-metric__meta">{{ __('Liste') }}</a>
                    @endif
                </div>
                <div class="psc-metric__icon {{ $iconTone('blue') }}">
                    @include('partials.psc.icons', ['name' => 'building'])
                </div>
            </div>
            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Blog yazısı') }}</p>
                    <p class="psc-metric__value">{{ number_format($blogTotalCount) }}</p>
                </div>
                <div class="psc-metric__icon {{ $iconTone('violet') }}">
                    @include('partials.psc.icons', ['name' => 'blog'])
                </div>
            </div>
            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Sitede blog') }}</p>
                    <p class="psc-metric__value">{{ number_format($blogLiveCount) }}</p>
                    <p class="mt-1 text-xs text-[#64748b]">{{ __('Onaylı + yayın tarihi geçmiş') }}</p>
                </div>
                <div class="psc-metric__icon {{ $iconTone('green') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Bekleyen blog') }}</p>
                    <p class="psc-metric__value">{{ number_format($pendingBlogModeration) }}</p>
                    @if ($viewerIsSuperAdmin)
                        <a href="{{ route('admin.blog.index') }}" class="psc-metric__meta">{{ __('Blog yönetimi') }}</a>
                    @endif
                </div>
                <div class="psc-metric__icon {{ $iconTone('red') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="psc-split">
            <section class="psc-card">
                <div class="psc-card__body">
                    <h2 class="psc-card__title">{{ __('Şikâyet & kampanya — son 6 ay') }}</h2>
                    <p class="psc-card__sub">{{ __('Canlı grafik entegrasyonu için veri kaynağı eklendiğinde güncellenebilir.') }}</p>
                    <div class="mt-6 flex h-48 items-end justify-between gap-2">
                        @foreach ([35, 52, 28, 64, 45, 72] as $h)
                            <div class="flex flex-1 flex-col items-center gap-2">
                                <div class="w-full rounded-t-lg bg-gradient-to-t from-[#2563eb] to-[#60a5fa]" style="height: {{ $h }}%"></div>
                                <span class="text-[10px] font-semibold text-[#94a3b8]">{{ $loop->iteration }}. {{ __('ay') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            <section class="psc-card">
                <div class="psc-card__body">
                    <h2 class="psc-card__title">{{ __('Onay akışı') }}</h2>
                    <p class="psc-card__sub">{{ __('Bekleyen içerik türleri') }}</p>
                    <ul class="mt-4 divide-y divide-[#f1f5f9]">
                        <li class="flex items-center justify-between py-3 text-sm">
                            <span class="font-semibold text-[#334155]">{{ __('Şikâyet') }}</span>
                            <span class="psc-badge psc-badge--warn">{{ number_format($pendingModeration) }}</span>
                        </li>
                        <li class="flex items-center justify-between py-3 text-sm">
                            <span class="font-semibold text-[#334155]">{{ __('Kampanya') }}</span>
                            <span class="psc-badge psc-badge--info">{{ number_format($pendingCampaignModeration) }}</span>
                        </li>
                        <li class="flex items-center justify-between py-3 text-sm">
                            <span class="font-semibold text-[#334155]">{{ __('Blog') }}</span>
                            <span class="psc-badge psc-badge--success">{{ number_format($pendingBlogModeration) }}</span>
                        </li>
                    </ul>
                    @if ($viewerIsSuperAdmin)
                        <div class="mt-4 flex flex-wrap gap-3 border-t border-[#f1f5f9] pt-4 text-xs font-semibold">
                            <a href="{{ route('admin.platform-settings.edit') }}" class="text-[#2563eb] hover:underline">{{ __('Platform ayarları') }}</a>
                            <a href="{{ route('admin.mail-settings.edit') }}" class="text-[#2563eb] hover:underline">{{ __('E-posta') }}</a>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        @if (! $viewerIsSuperAdmin)
            <p class="psc-card psc-card__body text-sm text-[#64748b]">
                {{ __('Moderasyon ve sistem ayarları yalnızca süper yönetici içindir. Blog yönetimine sol menüden erişebilirsiniz.') }}
            </p>
        @endif
    </div>
@endsection
