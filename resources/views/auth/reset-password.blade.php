@extends('layouts.app', ['minimalChrome' => true])

@section('title', __('Yeni şifre'))

@section('content')
    <div class="mx-auto max-w-md px-4 py-12">
        <h1 class="text-2xl font-extrabold text-neutral-900">{{ __('Yeni şifre belirle') }}</h1>

        <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-neutral-500">{{ __('E-posta') }}</label>
                <input name="email" type="email" required value="{{ old('email', $email) }}" autocomplete="email"
                    class="mt-2 w-full rounded-xl border border-neutral-200 px-4 py-3 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                @error('email')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-neutral-500">{{ __('Yeni şifre') }}</label>
                <input name="password" type="password" required autocomplete="new-password"
                    class="mt-2 w-full rounded-xl border border-neutral-200 px-4 py-3 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                @error('password')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-neutral-500">{{ __('Şifre tekrar') }}</label>
                <input name="password_confirmation" type="password" required autocomplete="new-password"
                    class="mt-2 w-full rounded-xl border border-neutral-200 px-4 py-3 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white hover:opacity-95">{{ __('Şifreyi kaydet') }}</button>
        </form>
    </div>
@endsection
