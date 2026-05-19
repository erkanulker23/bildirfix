@props([
    'name',
    'label' => null,
    'value' => '',
    'editorId' => null,
    'hint' => null,
    'required' => false,
])

@php
    $editorId = $editorId ?? 'quill-'.\Illuminate\Support\Str::slug($name, '-');
    $inputId = $editorId.'-input';
@endphp

<div {{ $attributes->merge(['class' => 'psc-quill-field']) }}>
    @if ($label)
        <label class="psc-field__label" for="{{ $inputId }}">{{ $label }}</label>
    @endif
    <div class="psc-editor-wrap mt-2">
        <div id="{{ $editorId }}" class="psc-quill-mount" data-quill-input="{{ $inputId }}"></div>
    </div>
    <textarea name="{{ $name }}" id="{{ $inputId }}" class="psc-quill-hidden" @if($required) required @endif>{{ $value }}</textarea>
    @if ($hint)
        <p class="mt-1 text-xs text-[var(--psc-text-muted)]">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
    @enderror
</div>
