@extends('layouts.admin')

@section('title', __('Tüm kampanyalar'))

@section('content')
    <div class="space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Kampanya kayıtları') }}</h1>
                <p class="psc-page-desc">{{ __('Arama, düzenleme ve toplu silme.') }}</p>
            </div>
            <a href="{{ route('admin.campaigns.create') }}" class="psc-btn psc-btn--primary">{{ __('Kampanya oluştur') }}</a>
        </div>

        <form method="get" class="psc-filter">
            <div class="psc-filter__grid">
                <div>
                    <label class="psc-field__label" for="cmp-q">{{ __('Ara') }}</label>
                    <input id="cmp-q" name="q" type="search" value="{{ $searchQuery ?? '' }}"
                        placeholder="{{ __('Başlık, kullanıcı…') }}" class="psc-input">
                </div>
                <div>
                    <label class="psc-field__label" for="cmp-durum">{{ __('Durum') }}</label>
                    <select id="cmp-durum" name="durum" class="psc-select">
                        <option value="all" @selected($statusFilter === 'all')>{{ __('Tümü') }}</option>
                        <option value="pending" @selected($statusFilter === 'pending')>{{ __('Onay bekliyor') }}</option>
                        <option value="approved" @selected($statusFilter === 'approved')>{{ __('Yayında') }}</option>
                        <option value="rejected" @selected($statusFilter === 'rejected')>{{ __('Red') }}</option>
                        <option value="unpublished" @selected($statusFilter === 'unpublished')>{{ __('Yayından kaldırıldı') }}</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="psc-btn psc-btn--primary w-full sm:w-auto">{{ __('Filtrele') }}</button>
                </div>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.campaigns.bulk-destroy') }}" id="campaign-bulk-form"
            onsubmit="return confirm(@js(__('Seçili kampanyalar kalıcı olarak silinsin mi?')))">
            @csrf
            @method('DELETE')
            @foreach (request()->only(['durum', 'q', 'page']) as $k => $v)
                @if ($v !== null && $v !== '')
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endif
            @endforeach

            <div class="mb-3">
                <button type="submit" class="psc-btn psc-btn--danger psc-btn--sm">{{ __('Seçilenleri sil') }}</button>
            </div>

            <div class="psc-table-wrap">
                <div class="psc-table-scroll">
                    <table class="psc-table">
                        <thead>
                            <tr>
                                <th class="w-10">
                                    <input type="checkbox" class="rounded" onclick="document.querySelectorAll('.campaign-row-cb').forEach(c => c.checked = this.checked)">
                                </th>
                                <th>{{ __('Başlık') }}</th>
                                <th>{{ __('Kullanıcı') }}</th>
                                <th>{{ __('Şehir') }}</th>
                                <th>{{ __('Durum') }}</th>
                                <th class="text-right">{{ __('İşlem') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($campaigns as $campaign)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $campaign->id }}" class="campaign-row-cb rounded">
                                    </td>
                                    <td class="psc-table__primary">{{ $campaign->title }}</td>
                                    <td>{{ $campaign->user?->name ?? '—' }}</td>
                                    <td>{{ $campaign->city?->name ?? '—' }}</td>
                                    <td>
                                        <span class="psc-badge psc-badge--neutral">{{ $campaign->moderation_status->label() }}</span>
                                    </td>
                                    <td class="text-right whitespace-nowrap">
                                        <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="psc-table__link">{{ __('Düzenle') }}</a>
                                        @if ($campaign->isPubliclyApproved())
                                            <a href="{{ route('campaigns.show', $campaign) }}" target="_blank" rel="noopener"
                                                class="ml-2 text-xs font-semibold text-[var(--psc-text-muted)]">{{ __('Ön yüz') }}</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

        <div>{{ $campaigns->links() }}</div>
    </div>
@endsection
