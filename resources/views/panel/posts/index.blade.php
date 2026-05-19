@extends('layouts.panel', ['panelKind' => 'user'])

@section('title', __('Bildirimlerim'))

@section('content')
    <div class="space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Bildirimlerim') }}</h1>
                <p class="psc-page-desc">{{ __('Oluşturduğun şikâyet ve bildirimleri görüntüle, düzenle.') }}</p>
            </div>
            <a href="{{ route('posts.create') }}" class="psc-btn psc-btn--primary">{{ __('Yeni bildir') }}</a>
        </div>

        @if ($posts->isEmpty())
            <section class="psc-card">
                <div class="psc-card__body text-center">
                    <p class="text-sm text-[#64748b]">{{ __('Henüz bildirim oluşturmadın.') }}</p>
                    <a href="{{ route('posts.create') }}" class="psc-btn psc-btn--primary mt-4">{{ __('İlk bildirimi oluştur') }}</a>
                </div>
            </section>
        @else
            <div class="psc-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="psc-table w-full">
                        <thead>
                            <tr>
                                <th>{{ __('Başlık') }}</th>
                                <th>{{ __('Durum') }}</th>
                                <th class="hidden sm:table-cell">{{ __('Görüntülenme') }}</th>
                                <th class="hidden md:table-cell">{{ __('Tarih') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($posts as $post)
                                <tr>
                                    <td class="max-w-[14rem] sm:max-w-xs">
                                        <p class="truncate font-semibold text-[#0f172a]">{{ $post->title }}</p>
                                        <p class="mt-0.5 truncate text-xs text-[#64748b]">{{ $post->city?->name ?? '—' }} · {{ $post->category?->name ?? '—' }}</p>
                                    </td>
                                    <td>
                                        @include('partials.panel-moderation-badge', ['status' => $post->moderation_status])
                                        <span class="mt-1 block text-xs text-[#64748b]">{{ $post->status->label() }}</span>
                                    </td>
                                    <td class="hidden tabular-nums sm:table-cell">{{ number_format((int) $post->view_count) }}</td>
                                    <td class="hidden text-xs text-[#64748b] md:table-cell">{{ $post->created_at->translatedFormat('d M Y') }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('panel.posts.edit', $post) }}" class="psc-btn psc-btn--ghost">{{ __('Düzenle') }}</a>
                                        @if ($post->isPubliclyApproved())
                                            <a href="{{ route('posts.show', $post) }}" target="_blank" rel="noopener" class="psc-btn psc-btn--ghost">{{ __('Gör') }}</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">{{ $posts->links() }}</div>
        @endif
    </div>
@endsection
