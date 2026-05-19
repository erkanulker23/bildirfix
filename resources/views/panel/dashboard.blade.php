@extends('layouts.panel', ['panelKind' => 'user'])

@section('title', __('Dashboard'))

@section('content')
    <div class="space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Hoş geldin, :name', ['name' => $user->name]) }}</h1>
                <p class="psc-page-desc">{{ __('Bildirimlerini ve kampanyalarını buradan yönet; yeni içerik oluştur.') }}</p>
            </div>
            <div class="psc-page-actions">
                <a href="{{ route('posts.create') }}" class="psc-btn psc-btn--primary">{{ __('Yeni bildir') }}</a>
                <a href="{{ route('campaigns.create') }}" class="psc-btn psc-btn--secondary">{{ __('Kampanya başlat') }}</a>
            </div>
        </div>

        <div class="psc-metrics sm:grid-cols-2 lg:grid-cols-4">
            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Bildirimlerim') }}</p>
                    <p class="psc-metric__value">{{ number_format($postsCount) }}</p>
                    <a href="{{ route('panel.posts.index') }}" class="psc-metric__meta">{{ __('Tümünü gör') }}</a>
                </div>
            </div>
            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Kampanyalarım') }}</p>
                    <p class="psc-metric__value">{{ number_format($campaignsCount) }}</p>
                    <a href="{{ route('panel.campaigns.index') }}" class="psc-metric__meta">{{ __('Tümünü gör') }}</a>
                </div>
            </div>
            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Bildirim görüntülenme') }}</p>
                    <p class="psc-metric__value">{{ number_format($postsViewsSum) }}</p>
                </div>
            </div>
            <div class="psc-metric">
                <div>
                    <p class="psc-metric__label">{{ __('Kampanya görüntülenme') }}</p>
                    <p class="psc-metric__value">{{ number_format($campaignsViewsSum) }}</p>
                </div>
            </div>
        </div>

        <div class="psc-split">
            <section class="psc-card">
                <div class="psc-card__body">
                    <h2 class="psc-card__title">{{ __('Son bildirimler') }}</h2>
                    @if ($recentPosts->isEmpty())
                        <p class="mt-3 text-sm text-[#64748b]">{{ __('Henüz bildirim yok.') }}
                            <a href="{{ route('posts.create') }}" class="font-semibold text-[#ea580c] hover:underline">{{ __('İlk bildirimi oluştur') }}</a>
                        </p>
                    @else
                        <ul class="mt-4 divide-y divide-[#eef2f7]">
                            @foreach ($recentPosts as $post)
                                <li class="flex flex-wrap items-center justify-between gap-2 py-3">
                                    <div class="min-w-0">
                                        <a href="{{ route('panel.posts.edit', $post) }}" class="font-semibold text-[#0f172a] hover:text-[#ea580c]">{{ \Illuminate\Support\Str::limit($post->title, 48) }}</a>
                                        <p class="mt-0.5 text-xs text-[#64748b]">{{ $post->city?->name ?? '—' }} · {{ number_format((int) $post->view_count) }} {{ __('görüntülenme') }}</p>
                                    </div>
                                    @include('partials.panel-moderation-badge', ['status' => $post->moderation_status])
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>

            <section class="psc-card">
                <div class="psc-card__body">
                    <h2 class="psc-card__title">{{ __('Son kampanyalar') }}</h2>
                    @if ($recentCampaigns->isEmpty())
                        <p class="mt-3 text-sm text-[#64748b]">{{ __('Henüz kampanya yok.') }}
                            <a href="{{ route('campaigns.create') }}" class="font-semibold text-[#ea580c] hover:underline">{{ __('Kampanya başlat') }}</a>
                        </p>
                    @else
                        <ul class="mt-4 divide-y divide-[#eef2f7]">
                            @foreach ($recentCampaigns as $campaign)
                                <li class="flex flex-wrap items-center justify-between gap-2 py-3">
                                    <div class="min-w-0">
                                        <a href="{{ route('panel.campaigns.edit', $campaign) }}" class="font-semibold text-[#0f172a] hover:text-[#ea580c]">{{ \Illuminate\Support\Str::limit($campaign->title, 48) }}</a>
                                        <p class="mt-0.5 text-xs text-[#64748b]">{{ number_format((int) $campaign->supporter_count) }} {{ __('destek') }} · {{ number_format((int) $campaign->view_count) }} {{ __('görüntülenme') }}</p>
                                    </div>
                                    @include('partials.panel-moderation-badge', ['status' => $campaign->moderation_status])
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection
