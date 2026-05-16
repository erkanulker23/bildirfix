@extends('layouts.app')

@section('title', __('Telefon doğrulama'))

@section('content')
    <div class="mx-auto max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold">{{ __('OTP doğrula') }}</h1>
        <p class="mt-2 text-sm text-slate-600">{{ __('Kod') }} {{ $phone }} {{ __('için geçerlidir.') }}</p>

        <form method="POST" action="{{ route('verify.phone') }}" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="phone" value="{{ $phone }}">
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500">{{ __('6 haneli kod') }}</label>
                <input name="code" type="text" inputmode="numeric" maxlength="6" required autofocus
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-center text-xl tracking-[0.4em]">
            </div>
            <button type="submit"
                class="w-full rounded-xl bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-500">{{ __('Doğrula') }}</button>
        </form>

        <form method="POST" action="{{ route('verify.phone.resend') }}" class="mt-4">
            @csrf
            <button type="submit" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                {{ __('Kodu tekrar gönder') }}
            </button>
        </form>

        <p class="mt-4 text-xs text-slate-500">{{ __('Yerelde kod storage/logs/otp.log dosyasına düşer.') }}</p>
    </div>
@endsection
