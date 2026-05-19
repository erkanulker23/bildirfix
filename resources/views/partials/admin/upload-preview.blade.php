@props([
    'label',
    'previewUrl' => null,
    'previewAlt' => '',
    'removeName' => null,
    'removeLabel' => null,
    'inputName',
    'accept' => 'image/*',
    'hint' => null,
    'previewClass' => 'h-16 max-w-[200px] object-contain',
])

<div class="psc-upload-preview">
    <label class="psc-field__label">{{ $label }}</label>
    @if ($previewUrl)
        <div class="psc-upload-preview__current mt-2">
            <img src="{{ $previewUrl }}" alt="{{ $previewAlt }}" class="{{ $previewClass }} rounded-lg border border-[var(--psc-border)] bg-white p-1">
            @if ($removeName)
                <label class="mt-2 flex cursor-pointer items-center gap-2 text-sm font-medium text-rose-700">
                    <input type="checkbox" name="{{ $removeName }}" value="1" class="rounded border-slate-300">
                    {{ $removeLabel ?? __('Kaldır') }}
                </label>
            @endif
        </div>
    @else
        <p class="mt-2 text-sm text-[var(--psc-text-muted)]">{{ __('Yüklü dosya yok') }}</p>
    @endif
    <input name="{{ $inputName }}" type="file" accept="{{ $accept }}" class="mt-3 w-full text-sm text-[var(--psc-text)]">
    @if ($hint)
        <p class="mt-1 text-xs text-[var(--psc-text-muted)]">{{ $hint }}</p>
    @endif
</div>
