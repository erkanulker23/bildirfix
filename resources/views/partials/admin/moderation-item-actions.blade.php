@php
    /** @var \App\Models\Post|\App\Models\Campaign $item */
    $st = $item->moderation_status;
    $approveRoute = $approveRoute ?? null;
    $rejectRoute = $rejectRoute ?? null;
    $unpublishRoute = $unpublishRoute ?? null;
    $statusFilter = $statusFilter ?? 'all';
@endphp

<div class="psc-mod-actions">
    @if ($st->value === 'pending')
        <form method="POST" action="{{ $approveRoute }}">
            @csrf
            <input type="hidden" name="durum" value="{{ $statusFilter }}">
            <button type="submit" class="psc-btn psc-btn--success psc-btn--sm">{{ __('Yayına al') }}</button>
        </form>
        <form method="POST" action="{{ $rejectRoute }}" class="psc-mod-actions__stack">
            @csrf
            <input type="hidden" name="durum" value="{{ $statusFilter }}">
            <input type="text" name="moderation_note" maxlength="2000"
                placeholder="{{ __('Red gerekçesi (isteğe bağlı)') }}"
                class="psc-input psc-input--sm mb-1 min-w-[12rem]">
            <button type="submit" class="psc-btn psc-btn--danger psc-btn--sm">{{ __('Reddet') }}</button>
        </form>
    @elseif ($st->value === 'approved')
        <form method="POST" action="{{ $unpublishRoute }}" class="psc-mod-actions__stack">
            @csrf
            <input type="hidden" name="durum" value="{{ $statusFilter }}">
            <input type="text" name="moderation_note" maxlength="2000"
                placeholder="{{ __('Not (isteğe bağlı)') }}"
                class="psc-input psc-input--sm mb-1 min-w-[12rem]">
            <button type="submit" class="psc-btn psc-btn--neutral psc-btn--sm">{{ __('Yayından kaldır') }}</button>
        </form>
    @elseif (in_array($st->value, ['rejected', 'unpublished'], true))
        <form method="POST" action="{{ $approveRoute }}">
            @csrf
            <input type="hidden" name="durum" value="{{ $statusFilter }}">
            <button type="submit" class="psc-btn psc-btn--success psc-btn--sm">{{ __('Tekrar yayına al') }}</button>
        </form>
    @endif
</div>
