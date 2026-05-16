@extends('layouts.app')

@section('title', __('Şirket girişi').' • '.config('app.name'))

@section('toolbar')
    <div class="flex flex-1 justify-end gap-3">
        <a href="{{ route('home') }}"
            class="rounded-full bg-neutral-100 px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-neutral-200">{{ __('← Akış') }}</a>
    </div>
@endsection

@section('content')
    <div class="mx-auto max-w-md">
        <div class="overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(0,0,0,0.08)] ring-1 ring-neutral-100">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-10 text-white">
                <p class="text-[11px] font-bold uppercase tracking-[0.35em] text-white/85">{{ __('Kurumsal kullanıcı') }}</p>
                <h1 class="mt-2 text-2xl font-black leading-tight">{{ __('Şirket / marka oturumu') }}</h1>
                <p class="mt-3 text-sm text-white/90">{{ __('Yalnızca doğrulanmış kurum hesapları.') }}</p>
            </div>
            <div class="px-8 py-8">
                <form method="POST" action="{{ route('login.brand.store') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="auth_portal" value="brand">
                    <div>
                        <label class="text-xs font-bold text-neutral-500">{{ __('Şirket e-postası') }}</label>
                        <input name="login" type="email" autocomplete="username" required value="{{ old('login') }}"
                            class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm font-medium focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-neutral-500">{{ __('Şifre') }}</label>
                        <input name="password" type="password" autocomplete="current-password" required
                            class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-neutral-700">
                        <input type="checkbox" name="remember" value="1" class="rounded border-neutral-300 text-emerald-600">
                        {{ __('Beni hatırla') }}
                    </label>
                    <x-turnstile-widget class="rounded-xl bg-neutral-50 px-3 py-2 ring-1 ring-neutral-100" theme="auto" />
                    <button type="submit"
                        class="w-full rounded-xl bg-emerald-600 py-3.5 text-sm font-bold text-white shadow-md shadow-emerald-600/30 hover:bg-emerald-700">
                        {{ __('Giriş yap') }}</button>
                </form>

                <p class="mt-6 text-center text-xs text-neutral-500">
                    <a href="{{ route('login') }}" class="font-semibold text-neutral-700 hover:underline">{{ __('Vatandaş girişi') }}</a>
                </p>
            </div>
        </div>
    </div>
@endsection
