@extends('layouts.app')

@section('title', __('Hızlı şikâyet • ').config('app.name'))

@section('toolbar')
    <div class="flex flex-1 items-center gap-3 sm:justify-end">
        <p class="text-xs font-semibold uppercase tracking-wide text-teal-800/70">{{ __('2 dakikada bildir') }}</p>
        <a href="{{ route('home') }}"
            class="rounded-2xl bg-white/70 px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-teal-100 hover:bg-white">← {{ __('Akış') }}</a>
    </div>
@endsection

@section('content')
    @php
        $d = $complaintDraft ?? [];
    @endphp
    <div class="mx-auto max-w-2xl rounded-3xl border border-teal-100/80 bg-white/90 p-6 shadow-xl shadow-teal-500/10 ring-1 ring-white sm:p-8">
        <h1 class="text-2xl font-black text-teal-900">{{ __('Yeni şikâyet oluştur') }}</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ __('Başlık ve hedef birim zorunlu değildir ancak daha hızlı yönlendirme sağlar. Yayın süper onay sürecinden geçer.') }}</p>
        @guest
            <div class="mt-4 rounded-2xl border border-teal-200 bg-teal-50/90 px-4 py-3 text-[13px] font-semibold leading-snug text-teal-950 ring-1 ring-teal-100">
                {{ __('Önce bu formu doldur; en sonda üye olup telefonunu doğrula — bildirimin tek seferde gönderilir. Zaten hesabın varsa giriş yaptığında taslak kaydedilir.') }}
            </div>
        @endguest

        <form method="POST" action="{{ route('complaints.quick.store') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label class="text-xs font-bold uppercase tracking-wide text-teal-900/70">{{ __('İl') }}</label>
                <select name="city_id" required
                    class="mt-2 w-full rounded-2xl border border-teal-100 bg-teal-50/40 px-4 py-3 text-sm font-semibold shadow-inner ring-1 ring-teal-50 focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-400/30">
                    @foreach ($cities as $c)
                        <option value="{{ $c->id }}" @selected((int) $cityId === (int) $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-bold uppercase tracking-wide text-teal-900/70">{{ __('Başlık') }}</label>
                <input name="title" value="{{ old('title', $d['title'] ?? '') }}" required maxlength="255"
                    class="mt-2 w-full rounded-2xl border border-slate-100 bg-white px-4 py-3 text-sm font-medium shadow-inner focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-400/25"
                    placeholder="{{ __('Örn: Parktaki yanmış armatür üç gündür yanmıyor') }}">
            </div>
            <div>
                <label class="text-xs font-bold uppercase tracking-wide text-teal-900/70">{{ __('Hedef kurum / birim') }}</label>
                <select name="institution_id"
                    class="mt-2 w-full rounded-2xl border border-slate-100 bg-white px-4 py-3 text-sm focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-400/25">
                    <option value="">— {{ __('Seçilmeyebilir') }} —</option>
                    @foreach ($institutions as $i)
                        <option value="{{ $i->id }}" @selected(old('institution_id', $d['institution_id'] ?? '') == $i->id)>{{ $i->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-bold uppercase tracking-wide text-teal-900/70">{{ __('Kategori') }}</label>
                <select name="category_id"
                    class="mt-2 w-full rounded-2xl border border-slate-100 bg-white px-4 py-3 text-sm focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-400/25">
                    <option value="">—</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $d['category_id'] ?? '') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-bold uppercase tracking-wide text-teal-900/70">{{ __('Açıklama') }}</label>
                <textarea name="description" rows="6" maxlength="8000"
                    class="mt-2 w-full rounded-2xl border border-slate-100 bg-white px-4 py-3 text-sm leading-relaxed shadow-inner focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-400/25"
                    placeholder="{{ __('Konum süre ve mümkünse foto/video bağlantısı…') }}">{{ old('description', $d['description'] ?? '') }}</textarea>
            </div>
            <div>
                <label class="text-xs font-bold uppercase tracking-wide text-teal-900/70">{{ __('Medya bağlantısı (isteğe bağlı)') }}</label>
                <input name="media_url" value="{{ old('media_url', $d['media_url'] ?? '') }}" type="url" maxlength="2048"
                    class="mt-2 w-full rounded-2xl border border-slate-100 bg-white px-4 py-3 text-sm focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-400/25"
                    placeholder="https://… (YouTube veya doğrudan resim bağlantısı)">
                <p class="mt-1 text-xs text-slate-500">{{ __('Dosya yüklemesi bir sonraki sürümde; şimdilik herkese açık bağlantılar.') }}</p>
            </div>
            <x-turnstile-widget class="rounded-2xl border border-teal-100 bg-teal-50/40 px-3 py-2" />
            <button type="submit"
                class="w-full rounded-2xl bg-gradient-to-r from-teal-500 via-teal-600 to-teal-700 px-4 py-3.5 text-sm font-black text-white shadow-lg shadow-teal-600/30 hover:from-teal-600 hover:to-teal-800">
                @auth
                    {{ __('Gönder • moderasyona düşür') }}
                @else
                    {{ __('Devam et • kayıt ve telefon doğrulama') }}
                @endauth
            </button>
        </form>
        @guest
            <p class="mt-5 text-center text-sm text-slate-600">
                {{ __('Zaten üye misin?') }}
                <a href="{{ route('login') }}" class="font-bold text-teal-800 underline underline-offset-4 hover:text-teal-950">{{ __('Giriş yap') }}</a>
            </p>
        @endguest
    </div>
@endsection
