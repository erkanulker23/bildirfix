@extends('layouts.app')

@section('title', __('Üye girişi • ').config('app.name'))

@section('content')
    <div class="mb-4 flex justify-end">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('login.brand') }}"
                class="rounded-full border border-emerald-100 bg-emerald-50 px-3 py-2 text-[11px] font-bold uppercase tracking-wide text-emerald-900 hover:bg-emerald-100">{{ __('Kurum') }}</a>
        </div>
    </div>
    <div class="mx-auto max-w-md rounded-[1.75rem] border border-teal-100/80 bg-white/95 p-8 shadow-xl shadow-teal-500/[0.12] ring-1 ring-teal-50 backdrop-blur-sm">
        <h1 class="text-2xl font-black text-teal-950">{{ __('Vatandaş girişi') }}</h1>
        @if (session()->has(\App\Support\ComplaintDraftSession::SESSION_KEY))
            <div class="mt-4 rounded-2xl border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-semibold text-teal-950">
                {{ __('Giriş sonrası bekleyen kent bildirimin otomatik gönderilir.') }}
            </div>
        @endif
        <p class="mt-2 text-sm font-medium leading-relaxed text-slate-600">{{ __('Kent sorun bildirimin için giriş yap. Belediye / kurum kullanıcıları /brand adresinden bağlanır.') }}</p>

        @if (! empty($googleOAuthEnabled))
            <div class="mt-6">
                <a href="{{ route('auth.google.redirect') }}"
                    class="flex w-full items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 shadow-sm ring-2 ring-transparent hover:bg-slate-50">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                    </svg>
                    {{ __('Google ile devam et') }}
                </a>
                <p class="mt-3 text-center text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">{{ __('veya hesabınla') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="{{ ! empty($googleOAuthEnabled) ? 'mt-4' : 'mt-8' }} space-y-5">
            @csrf
            <div>
                <label class="text-xs font-bold uppercase tracking-wide text-teal-900/70">{{ __('E-posta veya telefon') }}</label>
                <input name="login" type="text" autocomplete="username" value="{{ old('login') }}" required
                    class="mt-2 w-full rounded-2xl border border-slate-100 bg-teal-50/30 px-4 py-3 text-sm font-semibold placeholder:text-slate-400 focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-400/30"
                    placeholder="ornek@gmail.com · +90 53…">
            </div>
            <div>
                <label class="text-xs font-bold uppercase tracking-wide text-teal-900/70">{{ __('Şifre') }}</label>
                <input name="password" type="password" autocomplete="current-password" required
                    class="mt-2 w-full rounded-2xl border border-slate-100 bg-white px-4 py-3 text-sm focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-400/30">
            </div>
            <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                <input type="checkbox" name="remember" value="1" class="rounded border-teal-300 text-teal-700">
                {{ __('Beni hatırla') }}
            </label>
            <x-turnstile-widget class="rounded-2xl border border-teal-100 bg-teal-50/35 px-3 py-2" />
            <button type="submit"
                class="w-full rounded-2xl bg-gradient-to-r from-teal-500 via-teal-600 to-teal-800 px-4 py-3.5 text-sm font-black text-white shadow-lg shadow-teal-600/35 hover:from-teal-600 hover:to-teal-900">
                {{ __('Giriş yap') }}</button>
        </form>

        <p class="mt-8 text-center text-sm font-semibold text-slate-700">
            {{ __('Hesabın yok mu?') }}
            <a href="{{ route('register') }}" class="text-teal-800 underline decoration-2">{{ __('Üye kaydı') }}</a>
        </p>
    </div>
@endsection
