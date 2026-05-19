@extends('layouts.admin')

@section('title', __('Mesaj #:id', ['id' => $message->id]))

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="psc-page-head">
            <div>
                <a href="{{ route('admin.contact-messages.index') }}" class="text-sm font-semibold text-[var(--psc-text-muted)] hover:text-[var(--psc-text-strong)]">← {{ __('İletişim listesi') }}</a>
                <h1 class="psc-page-title mt-2">{{ __('Mesaj #:id', ['id' => $message->id]) }}</h1>
                <p class="psc-page-desc">{{ $message->created_at?->translatedFormat('d F Y, H:i') }}</p>
            </div>
            @if ($message->isUnread())
                <span class="psc-badge psc-badge--warn">{{ __('Yeni') }}</span>
            @endif
        </div>

        <div class="psc-card">
            <div class="psc-card__body space-y-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="psc-field__label">{{ __('Ad') }}</p>
                        <p class="mt-1 font-semibold text-[var(--psc-text-strong)]">{{ $message->name }}</p>
                    </div>
                    <div>
                        <p class="psc-field__label">{{ __('E-posta') }}</p>
                        <p class="mt-1">
                            <a href="mailto:{{ $message->email }}" class="font-semibold text-primary hover:underline">{{ $message->email }}</a>
                        </p>
                    </div>
                    <div>
                        <p class="psc-field__label">{{ __('Konu') }}</p>
                        <p class="mt-1 font-semibold">{{ $message->topicLabel() }}</p>
                    </div>
                    <div>
                        <p class="psc-field__label">{{ __('IP') }}</p>
                        <p class="mt-1 text-sm text-[var(--psc-text-muted)]">{{ $message->ip_address ?? '—' }}</p>
                    </div>
                </div>

                <div>
                    <p class="psc-field__label">{{ __('Mesaj') }}</p>
                    <div class="mt-2 whitespace-pre-wrap rounded-xl border border-[var(--psc-border)] bg-[var(--psc-surface-muted)] p-4 text-sm leading-relaxed text-[var(--psc-text)]">{{ $message->message }}</div>
                </div>

                @if ($message->read_at)
                    <p class="text-xs text-[var(--psc-text-muted)]">{{ __('Okundu:') }} {{ $message->read_at->translatedFormat('d M Y H:i') }}</p>
                @endif
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            @unless ($message->isUnread())
                <form method="POST" action="{{ route('admin.contact-messages.mark-unread', $message) }}">
                    @csrf
                    <button type="submit" class="psc-btn psc-btn--ghost">{{ __('Okunmadı işaretle') }}</button>
                </form>
            @endunless
            <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}"
                onsubmit="return confirm(@js(__('Bu mesajı silmek istediğinize emin misiniz?')))">
                @csrf
                @method('DELETE')
                <button type="submit" class="psc-btn psc-btn--danger">{{ __('Sil') }}</button>
            </form>
        </div>
    </div>
@endsection
