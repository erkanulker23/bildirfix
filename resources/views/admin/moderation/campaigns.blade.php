@extends('layouts.admin')

@section('title', __('Kampanya moderasyonu'))

@section('content')
    <div class="space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Kampanya moderasyonu') }}</h1>
                <p class="psc-page-desc">{{ __('Onaylı kampanyaları buradan yayından kaldırabilir veya bekleyenleri onaylayabilirsiniz.') }}</p>
            </div>
        </div>

        @include('partials.admin.moderation-filters', ['statusFilter' => $statusFilter, 'routeName' => 'admin.campaign-moderation.index'])

        @if ($campaigns->isEmpty())
            <div class="psc-card">
                <div class="psc-card__body py-10 text-center text-sm text-[var(--psc-text-muted)]">
                    {{ __('Bu süzgeçte kayıt yok.') }}
                </div>
            </div>
        @else
            <div class="psc-card overflow-hidden">
                <ul class="divide-y divide-[var(--psc-border-light)]">
                    @foreach ($campaigns as $campaign)
                        <li class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0 flex-1">
                                @php
                                    $st = $campaign->moderation_status;
                                    $pill = match ($st->value) {
                                        'pending' => 'psc-badge--warn',
                                        'approved' => 'psc-badge--success',
                                        'rejected' => 'psc-badge--danger',
                                        default => 'psc-badge--neutral',
                                    };
                                @endphp
                                <span class="psc-badge {{ $pill }}">{{ $st->label() }}</span>
                                <p class="mt-2 text-base font-bold text-[var(--psc-text-strong)]">{{ $campaign->title }}</p>
                                <p class="mt-1 text-xs text-[var(--psc-text-muted)]">
                                    {{ $campaign->user?->name }} · {{ $campaign->city?->name ?? __('Genel') }}
                                </p>
                                @if ($campaign->moderated_at)
                                    <p class="mt-1 text-[11px] text-[var(--psc-text-faint)]">
                                        {{ __('Son işlem') }}: {{ $campaign->moderated_at->timezone(config('app.timezone'))->format('d.m.Y H:i') }}
                                        @if ($campaign->moderatedBy) · {{ $campaign->moderatedBy->name }} @endif
                                    </p>
                                @endif
                                @if ($campaign->excerpt)
                                    <p class="mt-2 line-clamp-3 text-sm text-[var(--psc-text)]">{{ $campaign->excerpt }}</p>
                                @endif
                                @if ($campaign->moderation_note)
                                    <p class="mt-2 text-xs font-semibold text-[var(--psc-text-muted)]">{{ __('Not') }}: {{ $campaign->moderation_note }}</p>
                                @endif
                                <p class="mt-2">
                                    <a href="{{ route('campaigns.show', $campaign) }}" target="_blank" rel="noopener" class="psc-table__link text-xs">{{ __('Kampanya sayfası') }}</a>
                                    @if ($campaign->isPubliclyApproved())
                                        <span class="text-[var(--psc-text-faint)]"> · </span>
                                        <span class="text-xs font-bold text-emerald-700">{{ __('Herkese açık') }}</span>
                                    @endif
                                </p>
                            </div>
                            @include('partials.admin.moderation-item-actions', [
                                'item' => $campaign,
                                'approveRoute' => route('admin.campaign-moderation.approve', $campaign),
                                'rejectRoute' => route('admin.campaign-moderation.reject', $campaign),
                                'unpublishRoute' => route('admin.campaign-moderation.unpublish', $campaign),
                                'statusFilter' => $statusFilter,
                            ])
                        </li>
                    @endforeach
                </ul>
                <div class="border-t border-[var(--psc-border-light)] px-4 py-3">{{ $campaigns->links() }}</div>
            </div>
        @endif
    </div>
@endsection
