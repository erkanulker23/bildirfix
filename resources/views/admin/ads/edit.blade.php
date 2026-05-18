@extends('layouts.admin')

@section('admin_heading', $placement->label)
@section('title', __('Reklam düzenle'))

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-extrabold text-slate-900">{{ $placement->label }}</h1>
            <a href="{{ route('admin.ads.index') }}" class="text-xs font-bold text-blue-600 hover:underline">← {{ __('Listeye dön') }}</a>
        </div>

        <form method="POST" action="{{ route('admin.ads.update', $placement) }}" enctype="multipart/form-data"
            class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PATCH')

            <p class="text-xs text-slate-500">{{ __('Alan anahtarı') }}: <code>{{ $placement->key }}</code></p>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Başlık') }}</label>
                <input name="label" type="text" required value="{{ old('label', $placement->label) }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Tür') }}</label>
                    <select name="type" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold">
                        <option value="adsense" @selected(old('type', $placement->type) === 'adsense')>{{ __('Google AdSense') }}</option>
                        <option value="image" @selected(old('type', $placement->type) === 'image')>{{ __('Görsel reklam') }}</option>
                        <option value="video" @selected(old('type', $placement->type) === 'video')>{{ __('Video reklam') }}</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Durum') }}</label>
                    <select name="is_active" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold">
                        <option value="1" @selected(old('is_active', $placement->is_active ? '1' : '0') === '1')>{{ __('Aktif') }}</option>
                        <option value="0" @selected(old('is_active', $placement->is_active ? '1' : '0') === '0')>{{ __('Pasif') }}</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('AdSense slot ID') }}</label>
                <input name="adsense_slot" type="text" value="{{ old('adsense_slot', $placement->adsense_slot) }}"
                    placeholder="1234567890"
                    class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                <p class="mt-1 text-xs text-slate-500">{{ __('AdSense türünde kullanılır. İstemci kimliği .env ADSENSE_CLIENT.') }}</p>
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Medya dosyası') }}</label>
                <input name="media" type="file" accept="image/*,video/mp4,video/webm"
                    class="mt-2 w-full text-sm text-slate-700">
                @if ($placement->media_url)
                    <p class="mt-2 text-xs text-slate-500">{{ __('Mevcut') }}: <a href="{{ $placement->media_url }}" target="_blank" rel="noopener" class="text-blue-600 underline">{{ __('Görüntüle') }}</a></p>
                @endif
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Medya URL (alternatif)') }}</label>
                <input name="media_url" type="text" value="{{ old('media_url', $placement->media_url) }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Tıklama linki') }}</label>
                <input name="link_url" type="url" value="{{ old('link_url', $placement->link_url) }}"
                    placeholder="https://"
                    class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-blue-600 py-3 text-sm font-bold text-white hover:bg-blue-700">{{ __('Kaydet') }}</button>
        </form>
    </div>
@endsection
