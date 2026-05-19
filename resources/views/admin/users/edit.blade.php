@extends('layouts.admin')

@section('title', __('Kullanıcı düzenle'))

@section('content')
    <div class="max-w-2xl">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Hesap bilgileri') }}</h1>
                <p class="psc-page-desc">{{ __('ID') }} #{{ $user->id }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="psc-btn psc-btn--ghost">← {{ __('Listeye dön') }}</a>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="psc-card">
            <div class="psc-card__body space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label class="psc-field__label">{{ __('Ad') }}</label>
                    <input name="name" type="text" required value="{{ old('name', $user->name) }}" class="psc-input mt-2">
                    @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="psc-field__label">{{ __('E-posta') }}</label>
                    <input name="email" type="email" required value="{{ old('email', $user->email) }}" class="psc-input mt-2">
                    @error('email')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="psc-field__label">{{ __('Telefon') }}</label>
                    <input name="phone" type="text" value="{{ old('phone', $user->phone) }}" class="psc-input mt-2">
                    @error('phone')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="psc-field__label">{{ __('Rol') }}</label>
                    @if (! empty($isDesignatedSuperAdmin))
                        <input type="hidden" name="role" value="super_admin">
                        <p class="mt-2 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900">{{ __('Süper yönetici (sabit)') }}</p>
                    @else
                        <select name="role" class="psc-select mt-2">
                            @foreach ($roles as $role)
                                <option value="{{ $role->value }}" @selected(old('role', $user->role->value) === $role->value)>{{ $role->value }}</option>
                            @endforeach
                        </select>
                    @endif
                    @error('role')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="psc-field__label">{{ __('Hesap doğrulama durumu') }}</label>
                    <select name="verification_status" class="psc-select mt-2">
                        @foreach ($verificationStatuses as $vs)
                            <option value="{{ $vs->value }}" @selected(old('verification_status', $user->verification_status->value) === $vs->value)>{{ $vs->value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <input type="hidden" name="email_verified" value="0">
                        <input type="checkbox" name="email_verified" value="1" class="mt-1" @checked(old('email_verified', $user->email_verified_at ? '1' : '0') === '1')>
                        <span class="text-sm font-semibold">{{ __('E-posta doğrulanmış') }}</span>
                    </label>
                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <input type="hidden" name="phone_verified" value="0">
                        <input type="checkbox" name="phone_verified" value="1" class="mt-1" @checked(old('phone_verified', $user->phone_verified_at ? '1' : '0') === '1')>
                        <span class="text-sm font-semibold">{{ __('Telefon doğrulanmış') }}</span>
                    </label>
                </div>

                <div class="border-t border-slate-100 pt-5">
                    <h2 class="text-sm font-bold text-slate-900">{{ __('Şifre (isteğe bağlı)') }}</h2>
                    <p class="mt-1 text-xs text-slate-500">{{ __('Doldurursanız kullanıcının şifresi doğrudan güncellenir.') }}</p>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="psc-field__label">{{ __('Yeni şifre') }}</label>
                            <input name="password" type="password" autocomplete="new-password" class="psc-input mt-2">
                            @error('password')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="psc-field__label">{{ __('Tekrar') }}</label>
                            <input name="password_confirmation" type="password" autocomplete="new-password" class="psc-input mt-2">
                        </div>
                    </div>
                </div>

                <button type="submit" class="psc-btn psc-btn--primary w-full">{{ __('Kaydet') }}</button>
            </div>
        </form>

        @if (filled($user->email))
            <form method="POST" action="{{ route('admin.users.send-password-reset', $user) }}" class="psc-card mt-6">
                @csrf
                <div class="psc-card__body">
                    <h2 class="psc-card__title">{{ __('E-posta ile şifre sıfırlama') }}</h2>
                    <p class="psc-card__sub mt-1">{{ __('Kullanıcıya :email adresine sıfırlama bağlantısı gönderilir.', ['email' => $user->email]) }}</p>
                    @error('password_reset')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    <button type="submit" class="psc-btn psc-btn--secondary mt-4">{{ __('Sıfırlama e-postası gönder') }}</button>
                </div>
            </form>
        @endif
    </div>
@endsection
