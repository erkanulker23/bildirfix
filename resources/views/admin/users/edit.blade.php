@extends('layouts.admin')

@section('admin_heading', $user->name)
@section('title', __('Kullanıcı düzenle'))

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">{{ __('Hesap bilgileri') }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ __('ID') }} #{{ $user->id }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-blue-600 hover:underline">← {{ __('Listeye dön') }}</a>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PATCH')

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Ad') }}</label>
                <input name="name" type="text" required value="{{ old('name', $user->name) }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                @error('name')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('E-posta') }}</label>
                <input name="email" type="email" required value="{{ old('email', $user->email) }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                @error('email')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Telefon') }}</label>
                <input name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                @error('phone')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Rol') }}</label>
                @if (! empty($isDesignatedSuperAdmin))
                    <input type="hidden" name="role" value="super_admin">
                    <p class="mt-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-900">{{ __('Süper yönetici (sabit)') }}</p>
                @else
                    <select name="role"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}" @selected(old('role', $user->role->value) === $role->value)>{{ $role->value }}</option>
                        @endforeach
                    </select>
                @endif
                @error('role')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Hesap doğrulama durumu') }}</label>
                <select name="verification_status"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    @foreach ($verificationStatuses as $vs)
                        <option value="{{ $vs->value }}" @selected(old('verification_status', $user->verification_status->value) === $vs->value)>{{ $vs->value }}</option>
                    @endforeach
                </select>
                @error('verification_status')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                    <input type="hidden" name="email_verified" value="0">
                    <label class="flex cursor-pointer items-start gap-3">
                        <input type="checkbox" name="email_verified" value="1" class="mt-1 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            @checked(old('email_verified', $user->email_verified_at ? '1' : '0') === '1')>
                        <span>
                            <span class="text-sm font-bold text-slate-900">{{ __('E-posta doğrulanmış say') }}</span>
                            <span class="mt-1 block text-xs text-slate-500">{{ __('`email_verified_at` alanını doldurur veya temizler.') }}</span>
                        </span>
                    </label>
                    @error('email_verified')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                    <input type="hidden" name="phone_verified" value="0">
                    <label class="flex cursor-pointer items-start gap-3">
                        <input type="checkbox" name="phone_verified" value="1" class="mt-1 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            @checked(old('phone_verified', $user->phone_verified_at ? '1' : '0') === '1')>
                        <span>
                            <span class="text-sm font-bold text-slate-900">{{ __('Telefon doğrulanmış say') }}</span>
                            <span class="mt-1 block text-xs text-slate-500">{{ __('`phone_verified_at` alanını doldurur veya temizler.') }}</span>
                        </span>
                    </label>
                    @error('phone_verified')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-blue-600 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700">{{ __('Kaydet') }}</button>
        </form>
    </div>
@endsection
