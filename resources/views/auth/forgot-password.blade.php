@extends('layouts.app', ['minimalChrome' => true])

@section('title', __('Şifremi unuttum'))

@section('content')
    <div class="mx-auto max-w-md px-4 py-12">
        <h1 class="text-2xl font-extrabold text-neutral-900">{{ __('Şifre sıfırlama') }}</h1>
        <p class="mt-2 text-sm text-neutral-600">{{ __('E-posta adresinize sıfırlama bağlantısı gönderilir.') }}</p>

        @if (session('status'))
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-neutral-500">{{ __('E-posta') }}</label>
                <input name="email" type="email" required value="{{ old('email') }}" autocomplete="email"
                    class="mt-2 w-full rounded-xl border border-neutral-200 px-4 py-3 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                @error('email')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white hover:opacity-95">{{ __('Bağlantı gönder') }}</button>
        </form>

        <p class="mt-6 text-center text-sm">
            <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">{{ __('Girişe dön') }}</a>
        </p>
    </div>
@endsection
