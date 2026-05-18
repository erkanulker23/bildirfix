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

        @if (! empty($googleOAuthEnabled))
            <p class="mt-2 text-sm font-medium leading-relaxed text-slate-600">{{ __('Google hesabınla hızlıca giriş yapabilirsin.') }}</p>
            @include('partials.auth-google-oauth', ['mode' => 'login', 'class' => 'mt-5'])
        @else
            <p class="mt-2 text-sm font-medium leading-relaxed text-slate-600">{{ __('Kent sorun bildirimin için giriş yap. Belediye / kurum kullanıcıları /brand adresinden bağlanır.') }}</p>
        @endif

        <form method="POST" action="{{ route('login') }}" class="{{ ! empty($googleOAuthEnabled) ? 'mt-2' : 'mt-8' }} space-y-5">
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
