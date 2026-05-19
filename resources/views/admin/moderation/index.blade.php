@extends('layouts.admin')

@section('title', __('Şikâyet moderasyonu'))

@section('content')
    <div class="space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Kent bildirimleri — moderasyon') }}</h1>
                <p class="psc-page-desc">{{ __('Onaylı, red veya yayından kaldırılan kayıtları süzgeçle listeleyin.') }}</p>
            </div>
        </div>

        @include('partials.admin.moderation-filters', ['statusFilter' => $statusFilter, 'routeName' => 'admin.moderation.index'])

        @if ($posts->isEmpty())
            <div class="psc-card">
                <div class="psc-card__body py-10 text-center text-sm text-[var(--psc-text-muted)]">
                    {{ __('Bu süzgeçte kayıt yok.') }}
                </div>
            </div>
        @else
            <div class="psc-card overflow-hidden">
                <ul class="divide-y divide-[var(--psc-border-light)]">
                    @foreach ($posts as $post)
                        <li class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0 flex-1">
                                @php
                                    $st = $post->moderation_status;
                                    $pill = match ($st->value) {
                                        'pending' => 'psc-badge--warn',
                                        'approved' => 'psc-badge--success',
                                        'rejected' => 'psc-badge--danger',
                                        default => 'psc-badge--neutral',
                                    };
                                @endphp
                                <span class="psc-badge {{ $pill }}">{{ $st->label() }}</span>
                                <p class="mt-2 text-base font-bold text-[var(--psc-text-strong)]">{{ $post->title }}</p>
                                <p class="mt-1 text-xs text-[var(--psc-text-muted)]">
                                    {{ $post->user?->name }} · {{ $post->city?->name }}
                                    @if ($post->category)
                                        · {{ $post->category->name }}
                                    @endif
                                </p>
                                @php
                                    $targetInstitutions = $post->relationLoaded('institutions') && $post->institutions->isNotEmpty()
                                        ? $post->institutions
                                        : ($post->institution ? collect([$post->institution]) : collect());
                                @endphp
                                @if ($targetInstitutions->isNotEmpty())
                                    <p class="mt-2 text-sm text-[var(--psc-text)]">
                                        <span class="font-semibold text-[var(--psc-text-strong)]">{{ __('Şikâyet edilen kurum') }}:</span>
                                        {{ $targetInstitutions->pluck('name')->join(', ') }}
                                    </p>
                                @endif
                                @if ($post->moderated_at)
                                    <p class="mt-1 text-[11px] text-[var(--psc-text-faint)]">
                                        {{ __('Son işlem') }}: {{ $post->moderated_at->timezone(config('app.timezone'))->format('d.m.Y H:i') }}
                                        @if ($post->moderatedBy) · {{ $post->moderatedBy->name }} @endif
                                    </p>
                                @endif
                                @if ($post->description)
                                    <p class="mt-2 line-clamp-2 text-sm text-[var(--psc-text)]">{{ $post->description }}</p>
                                @endif
                                @if ($post->moderation_note)
                                    <p class="mt-2 text-xs font-semibold text-[var(--psc-text-muted)]">{{ __('Not') }}: {{ $post->moderation_note }}</p>
                                @endif
                                <p class="mt-2">
                                    <a href="{{ route('posts.show', $post) }}" target="_blank" rel="noopener" class="psc-table__link text-xs">{{ __('Ön yüzde aç') }}</a>
                                </p>
                            </div>
                            @include('partials.admin.moderation-item-actions', [
                                'item' => $post,
                                'approveRoute' => route('admin.moderation.approve', $post),
                                'rejectRoute' => route('admin.moderation.reject', $post),
                                'unpublishRoute' => route('admin.moderation.unpublish', $post),
                                'statusFilter' => $statusFilter,
                            ])
                        </li>
                    @endforeach
                </ul>
                <div class="border-t border-[var(--psc-border-light)] px-4 py-3">{{ $posts->links() }}</div>
            </div>
        @endif
    </div>
@endsection
