@extends('layouts.admin')

@section('title', __('Kurum düzenle'))

@section('content')
    <div class="max-w-2xl">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ $institution->name }}</h1>
                <p class="psc-page-desc">{{ __('Kurum bilgileri ve bağlı giriş hesabı') }}</p>
            </div>
            <a href="{{ route('admin.institutions.index') }}" class="psc-btn psc-btn--ghost">← {{ __('Listeye dön') }}</a>
        </div>

        <form method="POST" action="{{ route('admin.institutions.update', $institution) }}" enctype="multipart/form-data" class="psc-card">
            <div class="psc-card__body space-y-5">
                @csrf
                @method('PATCH')

                <div class="flex items-center gap-4 rounded-lg border border-slate-100 bg-slate-50 p-4">
                    <img src="{{ $institution->displayLogoUrl() }}" alt="" width="64" height="64" class="h-16 w-16 rounded-xl border object-cover">
                    <div>
                        <p class="psc-field__label mb-0">{{ __('Logo') }}</p>
                        <p class="text-xs text-slate-500">{{ __('Dosya veya URL') }}</p>
                    </div>
                </div>

                <div>
                    <label class="psc-field__label">{{ __('Logo dosyası') }}</label>
                    <input name="logo" type="file" accept="image/png,image/jpeg,image/webp" class="mt-2 w-full text-sm">
                </div>
                <div>
                    <label class="psc-field__label">{{ __('Logo URL') }}</label>
                    <input name="logo_url" type="text" value="{{ old('logo_url', $institution->logo_url) }}" class="psc-input mt-2">
                </div>
                <div>
                    <label class="psc-field__label">{{ __('Kurum adı') }}</label>
                    <input name="name" type="text" required value="{{ old('name', $institution->name) }}" class="psc-input mt-2">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="psc-field__label">{{ __('Tür') }}</label>
                        <select name="type" class="psc-select mt-2">
                            <option value="">{{ __('Seçin') }}</option>
                            @foreach ($types as $key => $label)
                                <option value="{{ $key }}" @selected(old('type', $institution->type) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="psc-field__label">{{ __('Şehir') }}</label>
                        <select name="city_id" class="psc-select mt-2">
                            <option value="">{{ __('Seçin') }}</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" @selected((string) old('city_id', $institution->city_id) === (string) $city->id)>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="psc-field__label">{{ __('Doğrulanmış') }}</label>
                    <select name="verified" class="psc-select mt-2">
                        <option value="1" @selected(old('verified', $institution->verified ? '1' : '0') === '1')>{{ __('Evet') }}</option>
                        <option value="0" @selected(old('verified', $institution->verified ? '1' : '0') === '0')>{{ __('Hayır') }}</option>
                    </select>
                </div>
                <div>
                    <label class="psc-field__label">{{ __('Web sitesi') }}</label>
                    <input name="website" type="url" value="{{ old('website', $institution->website) }}" class="psc-input mt-2">
                </div>
                <div>
                    <label class="psc-field__label">{{ __('Kamu e-posta') }}</label>
                    <input name="public_email" type="email" value="{{ old('public_email', $institution->public_email) }}" class="psc-input mt-2">
                </div>
                <div>
                    <label class="psc-field__label">{{ __('Telefon') }}</label>
                    <input name="phone" type="text" value="{{ old('phone', $institution->phone) }}" class="psc-input mt-2">
                </div>
                <div>
                    <label class="psc-field__label">{{ __('Adres') }}</label>
                    <textarea name="address" rows="2" class="psc-input mt-2 !h-auto py-3">{{ old('address', $institution->address) }}</textarea>
                </div>

                @if ($accountUser !== null)
                    <div class="border-t border-slate-100 pt-5 space-y-4">
                        <h2 class="text-sm font-bold text-slate-900">{{ __('Kurum giriş hesabı') }}</h2>
                        <div>
                            <label class="psc-field__label">{{ __('Hesap adı') }}</label>
                            <input name="account_name" type="text" value="{{ old('account_name', $accountUser->name) }}" class="psc-input mt-2">
                        </div>
                        <div>
                            <label class="psc-field__label">{{ __('Hesap e-posta') }}</label>
                            <input name="account_email" type="email" value="{{ old('account_email', $accountUser->email) }}" class="psc-input mt-2">
                            @error('account_email')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="psc-field__label">{{ __('Hesap telefon') }}</label>
                            <input name="account_phone" type="text" value="{{ old('account_phone', $accountUser->phone) }}" class="psc-input mt-2">
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="psc-field__label">{{ __('Yeni şifre') }}</label>
                                <input name="account_password" type="password" autocomplete="new-password" class="psc-input mt-2">
                                @error('account_password')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="psc-field__label">{{ __('Şifre tekrar') }}</label>
                                <input name="account_password_confirmation" type="password" class="psc-input mt-2">
                            </div>
                        </div>
                    </div>
                @else
                    <p class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">{{ __('Bu kuruma bağlı giriş hesabı yok.') }}</p>
                @endif

                <button type="submit" class="psc-btn psc-btn--primary w-full">{{ __('Kaydet') }}</button>
            </div>
        </form>

        @if ($accountUser !== null && filled($accountUser->email))
            <form method="POST" action="{{ route('admin.institutions.send-password-reset', $institution) }}" class="psc-card mt-6">
                @csrf
                <div class="psc-card__body">
                    <h2 class="psc-card__title">{{ __('Hesaba sıfırlama e-postası') }}</h2>
                    @error('account_password_reset')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    <button type="submit" class="psc-btn psc-btn--secondary mt-3">{{ __('Şifre sıfırlama e-postası gönder') }}</button>
                </div>
            </form>
        @endif
    </div>
@endsection
