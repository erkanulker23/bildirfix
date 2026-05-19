@extends('layouts.admin')

@section('title', __('İletişim mesajları'))

@section('content')
    <div class="space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('İletişim formu') }}</h1>
                <p class="psc-page-desc">{{ __('/iletisim sayfasından gelen mesajlar.') }}</p>
            </div>
            @if ($unreadCount > 0)
                <span class="psc-badge psc-badge--warn tabular-nums">{{ __(':n okunmamış', ['n' => number_format($unreadCount)]) }}</span>
            @endif
        </div>

        <form method="get" class="psc-filter">
            <div class="psc-filter__grid">
                <div class="sm:col-span-2">
                    <label class="psc-field__label" for="contact-search">{{ __('Ara') }}</label>
                    <input id="contact-search" name="q" type="search" value="{{ $q }}"
                        placeholder="{{ __('Ad, e-posta, konu, mesaj…') }}" class="psc-input">
                </div>
                <div>
                    <label class="psc-field__label" for="contact-filter">{{ __('Durum') }}</label>
                    <select id="contact-filter" name="filter" class="psc-select">
                        <option value="" @selected($filter === '')>{{ __('Tümü') }}</option>
                        <option value="unread" @selected($filter === 'unread')>{{ __('Okunmamış') }}</option>
                        <option value="read" @selected($filter === 'read')>{{ __('Okunmuş') }}</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="psc-btn psc-btn--primary w-full sm:w-auto">{{ __('Filtrele') }}</button>
                    @if ($q !== '' || $filter !== '')
                        <a href="{{ route('admin.contact-messages.index') }}"
                            class="psc-btn psc-btn--ghost mt-2 w-full sm:mt-0 sm:ml-2">{{ __('Temizle') }}</a>
                    @endif
                </div>
            </div>
        </form>

        <div class="psc-table-wrap">
            <div class="psc-table-scroll">
                <table class="psc-table">
                    <thead>
                        <tr>
                            <th class="w-12">#</th>
                            <th>{{ __('Gönderen') }}</th>
                            <th>{{ __('Konu') }}</th>
                            <th>{{ __('Mesaj') }}</th>
                            <th>{{ __('Tarih') }}</th>
                            <th>{{ __('Durum') }}</th>
                            <th class="text-right">{{ __('İşlem') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($messages as $msg)
                            <tr class="{{ $msg->isUnread() ? 'bg-amber-50/40' : '' }}">
                                <td class="tabular-nums text-[var(--psc-text-muted)]">{{ $msg->id }}</td>
                                <td>
                                    <p class="psc-table__primary">{{ $msg->name }}</p>
                                    <p class="text-xs text-[var(--psc-text-muted)]">{{ $msg->email }}</p>
                                </td>
                                <td class="text-sm">{{ $msg->topicLabel() }}</td>
                                <td class="max-w-xs truncate text-sm text-[var(--psc-text-muted)]">
                                    {{ \Illuminate\Support\Str::limit($msg->message, 80) }}
                                </td>
                                <td class="whitespace-nowrap text-xs text-[var(--psc-text-muted)]">
                                    {{ $msg->created_at?->translatedFormat('d M Y H:i') }}
                                </td>
                                <td>
                                    @if ($msg->isUnread())
                                        <span class="psc-badge psc-badge--warn">{{ __('Yeni') }}</span>
                                    @else
                                        <span class="psc-badge psc-badge--neutral">{{ __('Okundu') }}</span>
                                    @endif
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <a href="{{ route('admin.contact-messages.show', $msg) }}" class="psc-table__link">{{ __('Görüntüle') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-sm text-[var(--psc-text-muted)]">
                                    {{ __('Henüz iletişim mesajı yok.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $messages->links() }}</div>
    </div>
@endsection
