@extends('layouts.admin')

@section('title', __('Dashboard'))
@section('admin_heading', '')

@php
    $metricIcon = static function (string $tone): string {
        return match ($tone) {
            'blue' => 'bg-blue-100 text-blue-600',
            'orange' => 'bg-orange-100 text-orange-600',
            'violet' => 'bg-violet-100 text-violet-600',
            'green' => 'bg-emerald-100 text-emerald-600',
            'red' => 'bg-rose-100 text-rose-600',
            'cyan' => 'bg-cyan-100 text-cyan-700',
            default => 'bg-slate-100 text-slate-600',
        };
    };
@endphp

@section('content')
    <div class="mx-auto max-w-[1400px] space-y-6">
        <div class="shell-page-head">
            <div>
                <h1 class="shell-page-title">{{ __('Dashboard') }}</h1>
                <p class="shell-page-desc">{{ __('Operasyon ve onay özeti — yayın akışı süper yönetici onayına bağlıdır.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($viewerIsSuperAdmin)
                    <a href="{{ route('admin.blog-moderation.index') }}" class="shell-btn shell-btn--primary">
                        <span class="text-lg leading-none">+</span> {{ __('Onay kuyrukları') }}
                    </a>
                @endif
                <a href="{{ route('admin.blog.create') }}" class="shell-btn shell-btn--secondary">{{ __('Yeni blog') }}</a>
            </div>
        </div>

        <div class="shell-stat-grid">
            <div class="shell-metric">
                <div>
                    <p class="shell-metric__label">{{ __('Toplam şikâyet') }}</p>
                    <p class="shell-metric__value">{{ number_format($postsCount) }}</p>
                </div>
                <div class="shell-metric__icon {{ $metricIcon('blue') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <div class="shell-metric">
                <div>
                    <p class="shell-metric__label">{{ __('Açık şikâyet') }}</p>
                    <p class="shell-metric__value">{{ number_format($openPosts) }}</p>
                </div>
                <div class="shell-metric__icon {{ $metricIcon('orange') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="shell-metric">
                <div>
                    <p class="shell-metric__label">{{ __('Yayındaki şikâyet') }}</p>
                    <p class="shell-metric__value">{{ number_format($approvedPostsCount) }}</p>
                </div>
                <div class="shell-metric__icon {{ $metricIcon('green') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="shell-metric">
                <div>
                    <p class="shell-metric__label">{{ __('Bekleyen şikâyet') }}</p>
                    <p class="shell-metric__value">{{ number_format($pendingModeration) }}</p>
                    @if ($viewerIsSuperAdmin)
                        <a href="{{ route('admin.moderation.index') }}" class="mt-1 inline-block text-xs font-semibold text-blue-600 hover:underline">{{ __('Moderasyon') }}</a>
                    @endif
                </div>
                <div class="shell-metric__icon {{ $metricIcon('red') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="shell-metric">
                <div>
                    <p class="shell-metric__label">{{ __('Toplam kampanya') }}</p>
                    <p class="shell-metric__value">{{ number_format($campaignsCount) }}</p>
                </div>
                <div class="shell-metric__icon {{ $metricIcon('violet') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                </div>
            </div>
            <div class="shell-metric">
                <div>
                    <p class="shell-metric__label">{{ __('Yayındaki kampanya') }}</p>
                    <p class="shell-metric__value">{{ number_format($approvedCampaignsCount) }}</p>
                </div>
                <div class="shell-metric__icon {{ $metricIcon('green') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 10h2m-8 0h2m-4 4h8m-9 4h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="shell-metric">
                <div>
                    <p class="shell-metric__label">{{ __('Bekleyen kampanya') }}</p>
                    <p class="shell-metric__value">{{ number_format($pendingCampaignModeration) }}</p>
                    @if ($viewerIsSuperAdmin)
                        <a href="{{ route('admin.campaign-moderation.index') }}" class="mt-1 inline-block text-xs font-semibold text-blue-600 hover:underline">{{ __('Moderasyon') }}</a>
                    @endif
                </div>
                <div class="shell-metric__icon {{ $metricIcon('orange') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="shell-metric">
                <div>
                    <p class="shell-metric__label">{{ __('Kullanıcılar') }}</p>
                    <p class="shell-metric__value">{{ number_format($usersCount) }}</p>
                    @if ($viewerIsSuperAdmin)
                        <a href="{{ route('admin.users.index') }}" class="mt-1 inline-block text-xs font-semibold text-blue-600 hover:underline">{{ __('Liste') }}</a>
                    @endif
                </div>
                <div class="shell-metric__icon {{ $metricIcon('cyan') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>

            <div class="shell-metric">
                <div>
                    <p class="shell-metric__label">{{ __('Kurumlar') }}</p>
                    <p class="shell-metric__value">{{ number_format($institutionsCount) }}</p>
                    @if ($viewerIsSuperAdmin)
                        <a href="{{ route('admin.institutions.index') }}" class="mt-1 inline-block text-xs font-semibold text-blue-600 hover:underline">{{ __('Liste') }}</a>
                    @endif
                </div>
                <div class="shell-metric__icon {{ $metricIcon('blue') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <div class="shell-metric">
                <div>
                    <p class="shell-metric__label">{{ __('Blog yazısı') }}</p>
                    <p class="shell-metric__value">{{ number_format($blogTotalCount) }}</p>
                </div>
                <div class="shell-metric__icon {{ $metricIcon('violet') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </div>
            </div>
            <div class="shell-metric">
                <div>
                    <p class="shell-metric__label">{{ __('Sitede blog') }}</p>
                    <p class="shell-metric__value">{{ number_format($blogLiveCount) }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ __('Onaylı + yayın tarihi geçmiş') }}</p>
                </div>
                <div class="shell-metric__icon {{ $metricIcon('green') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="shell-metric">
                <div>
                    <p class="shell-metric__label">{{ __('Bekleyen blog') }}</p>
                    <p class="shell-metric__value">{{ number_format($pendingBlogModeration) }}</p>
                    @if ($viewerIsSuperAdmin)
                        <a href="{{ route('admin.blog-moderation.index') }}" class="mt-1 inline-block text-xs font-semibold text-blue-600 hover:underline">{{ __('Moderasyon') }}</a>
                    @endif
                </div>
                <div class="shell-metric__icon {{ $metricIcon('red') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="shell-card shell-card-pad">
                <h2 class="text-sm font-extrabold text-slate-900">{{ __('Şikâyet & kampanya — son 6 ay (örnek grafik)') }}</h2>
                <p class="text-xs text-slate-500">{{ __('Canlı grafik entegrasyonu için veri kaynağı eklendiğinde güncellenebilir.') }}</p>
                <div class="mt-6 flex h-48 items-end justify-between gap-2 px-1">
                    @foreach ([35, 52, 28, 64, 45, 72] as $h)
                        <div class="flex flex-1 flex-col items-center gap-2">
                            <div class="w-full rounded-t-md bg-gradient-to-t from-blue-600 to-blue-400" style="height: {{ $h }}%"></div>
                            <span class="text-[10px] font-semibold text-slate-400">{{ $loop->iteration }}. {{ __('ay') }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
            <section class="shell-card shell-card-pad">
                <h2 class="text-sm font-extrabold text-slate-900">{{ __('Onay akışı') }}</h2>
                <p class="text-xs text-slate-500">{{ __('Bekleyen içerik türleri') }}</p>
                <ul class="mt-4 divide-y divide-slate-100">
                    <li class="flex items-center justify-between py-3 text-sm">
                        <span class="font-semibold text-slate-700">{{ __('Şikâyet') }}</span>
                        <span class="shell-badge shell-badge--warn">{{ number_format($pendingModeration) }}</span>
                    </li>
                    <li class="flex items-center justify-between py-3 text-sm">
                        <span class="font-semibold text-slate-700">{{ __('Kampanya') }}</span>
                        <span class="shell-badge shell-badge--info">{{ number_format($pendingCampaignModeration) }}</span>
                    </li>
                    <li class="flex items-center justify-between py-3 text-sm">
                        <span class="font-semibold text-slate-700">{{ __('Blog') }}</span>
                        <span class="shell-badge shell-badge--success">{{ number_format($pendingBlogModeration) }}</span>
                    </li>
                </ul>
                @if ($viewerIsSuperAdmin)
                    <div class="mt-4 flex flex-wrap gap-2 border-t border-slate-100 pt-4">
                        <a href="{{ route('admin.platform-settings.edit') }}" class="text-xs font-bold text-blue-600 hover:underline">{{ __('Platform ayarları') }}</a>
                        <span class="text-slate-300">|</span>
                        <a href="{{ route('admin.mail-settings.edit') }}" class="text-xs font-bold text-blue-600 hover:underline">{{ __('E-posta') }}</a>
                    </div>
                @endif
            </section>
        </div>

        @if (! $viewerIsSuperAdmin)
            <p class="shell-card shell-card-pad text-sm text-gray-600">
                {{ __('Moderasyon ve sistem ayarları yalnızca süper yönetici içindir. Blog yönetimine sol menüden erişebilirsiniz.') }}
            </p>
        @endif
    </div>
@endsection
