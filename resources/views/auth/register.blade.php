@extends('layouts.app')

@section('title', __('Kayıt ol'))

@section('content')
    <div class="mx-auto max-w-md rounded-[1.75rem] border border-teal-100/80 bg-white/95 p-8 shadow-xl shadow-teal-500/[0.12] ring-1 ring-teal-50">
        <h1 class="text-2xl font-black text-teal-950">{{ __('Üye ol') }}</h1>

        @if (session()->has(\App\Support\ComplaintDraftSession::SESSION_KEY))
            <div class="mt-4 rounded-2xl border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-semibold text-teal-950">
                {{ __('Kent bildir formunu doldurdun. Kayıt ve telefon doğrulamasından sonra gönderin otomatik tamamlanır.') }}
            </div>
        @endif

        @if (! empty($googleOAuthEnabled))
            <p class="mt-2 text-sm font-medium leading-relaxed text-slate-600">{{ __('Google hesabınla tek adımda üye olabilirsin.') }}</p>
            @include('partials.auth-google-oauth', ['mode' => 'register', 'class' => 'mt-5'])
        @else
            <p class="mt-2 text-sm font-medium leading-relaxed text-slate-600">{{ __('Kent bildir ve kampanya için telefon numaran doğrulanana kadar içerik yayınlanmaz.') }}</p>
        @endif

        <form method="POST" action="{{ route('register') }}" class="{{ ! empty($googleOAuthEnabled) ? 'mt-2' : 'mt-6' }} space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wide text-teal-900/70">{{ __('Ad Soyad') }}</label>
                <input name="name" type="text" value="{{ old('name') }}" required
                    class="mt-2 w-full rounded-2xl border border-slate-100 bg-teal-50/30 px-4 py-3 text-sm font-semibold focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-400/30">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wide text-teal-900/70">{{ __('Telefon') }}</label>
                <input name="phone" type="tel" value="{{ old('phone') }}" required
                    class="mt-2 w-full rounded-2xl border border-slate-100 bg-teal-50/30 px-4 py-3 text-sm font-semibold focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-400/30">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wide text-teal-900/70">{{ __('E-posta') }} <span class="font-normal lowercase">({{ __('isteğe bağlı') }})</span></label>
                <input name="email" type="email" value="{{ old('email') }}"
                    class="mt-2 w-full rounded-2xl border border-slate-100 bg-teal-50/30 px-4 py-3 text-sm font-semibold focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-400/30">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wide text-teal-900/70">{{ __('Şifre') }}</label>
                <input name="password" type="password" autocomplete="new-password" required
                    class="mt-2 w-full rounded-2xl border border-slate-100 bg-white px-4 py-3 text-sm focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-400/30">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wide text-teal-900/70">{{ __('Şifre tekrar') }}</label>
                <input name="password_confirmation" type="password" autocomplete="new-password" required
                    class="mt-2 w-full rounded-2xl border border-slate-100 bg-white px-4 py-3 text-sm focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-400/30">
            </div>
            <x-turnstile-widget class="rounded-2xl border border-teal-100 bg-teal-50/35 px-3 py-2" />
            <button type="submit"
                class="w-full rounded-2xl bg-gradient-to-r from-teal-500 via-teal-600 to-teal-800 px-4 py-3.5 text-sm font-black text-white shadow-lg shadow-teal-600/35 hover:from-teal-600 hover:to-teal-900">
                {{ __('Üye ol') }}</button>
        </form>

        <p class="mt-8 text-center text-sm font-semibold text-slate-700">
            {{ __('Zaten hesabın var mı?') }}
            <a href="{{ route('login') }}" class="text-teal-800 underline decoration-2">{{ __('Giriş yap') }}</a>
        </p>
    </div>
@endsection
