@extends('layouts.app')

@section('title', __('Kayıt ol'))

@section('content')
    <div class="mx-auto max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold">{{ __('Kayıt ol') }}</h1>
        @if (session()->has(\App\Support\ComplaintDraftSession::SESSION_KEY))
            <div class="mt-4 rounded-xl border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-semibold text-teal-950">
                {{ __('Kent bildir formunu doldurdun. Kayıt ve telefon doğrulamasından sonra gönderin otomatik tamamlanır.') }}
            </div>
        @endif
        <p class="mt-2 text-sm text-slate-600">{{ __('Kent bildir için telefon numaran doğrulanana kadar paylaşım yapılmaz. Kurum ayarlarıyla Google ile de üye olunabiliyorsa aşağıdaki seçenek görünür.') }}</p>

        @if (! empty($googleOAuthEnabled))
            <div class="mt-6">
                <a href="{{ route('auth.google.redirect') }}"
                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 hover:bg-slate-50">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                    </svg>
                    {{ __('Google ile tek adımda üye ol') }}
                </a>
                <p class="mt-3 text-center text-[11px] font-bold uppercase tracking-widest text-slate-500">{{ __('veya klasik kayıt') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500">{{ __('Ad Soyad') }}</label>
                <input name="name" type="text" value="{{ old('name') }}" required
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500">{{ __('Telefon') }}</label>
                <input name="phone" type="tel" value="{{ old('phone') }}" required
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500">{{ __('E-posta') }} <span class="font-normal lowercase">({{ __('isteğe bağlı') }})</span></label>
                <input name="email" type="email" value="{{ old('email') }}"
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500">{{ __('Şifre') }}</label>
                <input name="password" type="password" autocomplete="new-password" required
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500">{{ __('Şifre tekrar') }}</label>
                <input name="password_confirmation" type="password" autocomplete="new-password" required
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <x-turnstile-widget class="rounded-lg border border-teal-100 bg-teal-50/35 px-2 py-2" />
            <button type="submit"
                class="w-full rounded-xl bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-500">{{ __('Devam et') }}</button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            {{ __('Zaten hesabın var mı?') }}
            <a href="{{ route('login') }}" class="font-semibold text-teal-700 hover:underline">{{ __('Giriş') }}</a>
        </p>
    </div>
@endsection
