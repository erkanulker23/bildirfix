@extends('layouts.admin')

@section('title', __('Tüm şikâyetler'))

@section('content')
    <div class="space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Tüm şikâyetler') }}</h1>
                <p class="psc-page-desc">{{ __('Siteye düşen tüm bildirimler — kullanıcı, dış aktarım ve moderasyon durumu.') }}</p>
            </div>
            <a href="{{ route('admin.moderation.index') }}" class="psc-btn">{{ __('Moderasyon kuyruğu') }}</a>
        </div>

        <form method="get" class="psc-filter">
            <div class="psc-filter__grid">
                <div>
                    <label class="psc-field__label" for="post-q">{{ __('Ara') }}</label>
                    <input id="post-q" name="q" type="search" value="{{ $searchQuery ?? '' }}"
                        placeholder="{{ __('Başlık, kullanıcı, kaynak URL…') }}" class="psc-input">
                </div>
                <div>
                    <label class="psc-field__label" for="post-durum">{{ __('Durum') }}</label>
                    <select id="post-durum" name="durum" class="psc-select">
                        <option value="all" @selected($statusFilter === 'all')>{{ __('Tümü') }}</option>
                        <option value="pending" @selected($statusFilter === 'pending')>{{ __('Onay bekliyor') }}</option>
                        <option value="approved" @selected($statusFilter === 'approved')>{{ __('Yayında') }}</option>
                        <option value="rejected" @selected($statusFilter === 'rejected')>{{ __('Red') }}</option>
                        <option value="unpublished" @selected($statusFilter === 'unpublished')>{{ __('Yayından kaldırıldı') }}</option>
                        <option value="imported" @selected($statusFilter === 'imported')>{{ __('Dış kaynak (Şikayetvar)') }}</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="psc-btn psc-btn--primary w-full sm:w-auto">{{ __('Filtrele') }}</button>
                </div>
            </div>
        </form>

        <div class="psc-table-wrap">
            <div class="psc-table-scroll">
                <table class="psc-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Başlık') }}</th>
                            <th>{{ __('Kullanıcı') }}</th>
                            <th>{{ __('Kurum') }}</th>
                            <th>{{ __('Kaynak') }}</th>
                            <th>{{ __('Durum') }}</th>
                            <th>{{ __('Tarih') }}</th>
                            <th class="text-right">{{ __('İşlem') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($posts as $post)
                            <tr>
                                <td class="text-xs text-[var(--psc-text-muted)]">{{ $post->id }}</td>
                                <td class="psc-table__primary max-w-[220px] truncate">{{ $post->title }}</td>
                                <td>{{ $post->user?->name ?? '—' }}</td>
                                <td>{{ $post->institution?->name ?? '—' }}</td>
                                <td class="text-xs">
                                    @if ($post->isImported())
                                        <span class="psc-badge psc-badge--neutral">{{ __('Dış') }}</span>
                                        @if ($post->externalImportSource)
                                            <span class="block text-[var(--psc-text-muted)]">{{ $post->externalImportSource->name }}</span>
                                        @endif
                                    @else
                                        {{ __('Site') }}
                                    @endif
                                </td>
                                <td>
                                    <span class="psc-badge psc-badge--neutral">{{ $post->moderation_status->label() }}</span>
                                </td>
                                <td class="text-xs whitespace-nowrap">{{ $post->created_at?->format('d.m.Y H:i') }}</td>
                                <td class="text-right whitespace-nowrap">
                                    @if ($post->isPubliclyApproved())
                                        <a href="{{ route('posts.show', $post) }}" target="_blank" rel="noopener"
                                            class="psc-table__link">{{ __('Ön yüz') }}</a>
                                    @endif
                                    @if ($post->source_url)
                                        <a href="{{ $post->source_url }}" target="_blank" rel="noopener noreferrer"
                                            class="ml-2 text-xs font-semibold text-[var(--psc-text-muted)]">SV</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $posts->links() }}</div>
    </div>
@endsection
