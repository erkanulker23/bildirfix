@extends('layouts.admin')

@section('title', $source->exists ? __('Kaynağı düzenle') : __('Yeni dış kaynak'))

@section('content')
    @php
        $isEdit = $source->exists;
    @endphp
    <div class="max-w-2xl space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ $isEdit ? __('Kaynağı düzenle') : __('Yeni içe aktarım kaynağı') }}</h1>
                <p class="psc-page-desc">{{ __('Şikayetvar marka sayfası; kurum türünde şikâyetler ilgili kuruma da bağlanır.') }}</p>
            </div>
        </div>

        <form method="POST"
            action="{{ $isEdit ? route('admin.external-imports.update', $source) : route('admin.external-imports.store') }}"
            class="space-y-6">
            @csrf
            @if ($isEdit)
                @method('PATCH')
            @endif

            <div class="psc-card">
                <div class="psc-card__body space-y-5">
                    <div>
                        <label class="psc-field__label" for="name">{{ __('Kaynak adı') }}</label>
                        <input id="name" name="name" type="text" required class="psc-input mt-2"
                            value="{{ old('name', $source->name) }}" placeholder="{{ __('Örn. Ataşehir Belediyesi — Şikayetvar') }}">
                        @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="psc-field__label" for="type">{{ __('Kaynak türü') }}</label>
                        <select id="type" name="type" class="psc-select mt-2" required>
                            @foreach (\App\Enums\ExternalImportSourceType::cases() as $case)
                                <option value="{{ $case->value }}" @selected(old('type', $source->type?->value) === $case->value)>
                                    {{ $case->label() }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-[var(--psc-text-muted)]">
                            {{ __('“Kurum bağlantılı” seçeneğinde fotoğraf ve videolar kurum profiline de hedeflenir.') }}
                        </p>
                        @error('type')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="psc-field__label" for="source_url">{{ __('Şikayetvar sayfa URL’si') }}</label>
                        <input id="source_url" name="source_url" type="url" required class="psc-input mt-2 font-mono text-sm"
                            value="{{ old('source_url', $source->source_url) }}"
                            placeholder="https://www.sikayetvar.com/atasehir-belediyesi">
                        @error('source_url')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div id="institution-field">
                        <label class="psc-field__label" for="institution_id">{{ __('Sistemdeki kurum') }}</label>
                        <select id="institution_id" name="institution_id" class="psc-select mt-2">
                            <option value="">{{ __('— Seçilmedi —') }}</option>
                            @foreach ($institutions as $inst)
                                <option value="{{ $inst['id'] }}" @selected((int) old('institution_id', $source->institution_id) === $inst['id'])>
                                    {{ $inst['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('institution_id')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="psc-field__label" for="max_pages">{{ __('Maks. sayfa') }}</label>
                            <input id="max_pages" name="max_pages" type="number" min="1" max="200" class="psc-input mt-2"
                                value="{{ old('max_pages', $source->max_pages ?? 50) }}">
                        </div>
                        <div>
                            <label class="psc-field__label" for="default_moderation">{{ __('Varsayılan moderasyon') }}</label>
                            <select id="default_moderation" name="default_moderation" class="psc-select mt-2">
                                <option value="pending" @selected(old('default_moderation', $source->default_moderation) === 'pending')>{{ __('Onay bekliyor') }}</option>
                                <option value="approved" @selected(old('default_moderation', $source->default_moderation) === 'approved')>{{ __('Doğrudan yayınla') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-6">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="enabled" value="1" class="rounded" @checked(old('enabled', $source->enabled))>
                            {{ __('Kaynak etkin') }}
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="auto_sync" value="1" class="rounded" @checked(old('auto_sync', $source->auto_sync))>
                            {{ __('Zamanlanmış otomatik çekim') }}
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="fetch_media" value="1" class="rounded" @checked(old('fetch_media', $source->fetch_media ?? true))>
                            {{ __('Fotoğraf ve video indir') }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="psc-btn psc-btn--primary">{{ $isEdit ? __('Kaydet') : __('Oluştur') }}</button>
                <a href="{{ route('admin.external-imports.index') }}" class="psc-btn">{{ __('İptal') }}</a>
            </div>
        </form>

        @if ($isEdit)
            <form method="POST" action="{{ route('admin.external-imports.destroy', $source) }}" class="mt-4"
                onsubmit="return confirm(@js(__('Bu kaynak silinsin mi?')))">
                @csrf
                @method('DELETE')
                <button type="submit" class="psc-btn psc-btn--danger">{{ __('Kaynağı sil') }}</button>
            </form>
        @endif
    </div>

    @push('head')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var typeEl = document.getElementById('type');
                var instField = document.getElementById('institution-field');
                function toggle() {
                    var isInst = typeEl && typeEl.value === 'sikayetvar_institution';
                    if (instField) instField.style.display = isInst ? '' : 'none';
                }
                if (typeEl) typeEl.addEventListener('change', toggle);
                toggle();
            });
        </script>
    @endpush
@endsection
