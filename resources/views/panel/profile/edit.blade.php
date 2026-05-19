@extends('layouts.panel', ['panelKind' => 'user'])

@section('title', __('Profil'))

@section('content')
    <div class="max-w-xl space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Profil ayarları') }}</h1>
                <p class="psc-page-desc">{{ __('Ad, e-posta, şifre ve profil fotoğrafını güncelle.') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('panel.profile.update') }}" enctype="multipart/form-data" class="psc-card">
            <div class="psc-card__body space-y-5">
                @csrf
                @method('PATCH')

                <div class="flex flex-wrap items-center gap-4">
                    @if ($user->avatarUrl())
                        <img src="{{ $user->avatarUrl() }}" alt="" class="h-16 w-16 rounded-full object-cover ring-2 ring-[#eef2f7]">
                    @else
                        <div class="psc-avatar h-16 w-16 text-lg">{{ $user->avatarInitials() }}</div>
                    @endif
                    <div class="min-w-0 flex-1 space-y-2">
                        <label class="psc-field__label" for="avatar">{{ __('Profil fotoğrafı') }}</label>
                        <input id="avatar" name="avatar" type="file" accept="image/*" class="block w-full text-sm text-[#64748b] file:mr-3 file:rounded-lg file:border-0 file:bg-[#fff7ed] file:px-3 file:py-2 file:text-sm file:font-semibold file:text-[#ea580c]">
                        @error('avatar')<p class="text-sm text-rose-600">{{ $message }}</p>@enderror
                        @if ($user->avatar_path)
                            <label class="flex items-center gap-2 text-sm text-[#64748b]">
                                <input type="checkbox" name="remove_avatar" value="1" class="rounded border-[#cbd5e1]">
                                {{ __('Fotoğrafı kaldır') }}
                            </label>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="psc-field__label" for="name">{{ __('Ad soyad') }}</label>
                    <input id="name" name="name" type="text" required maxlength="120" value="{{ old('name', $user->name) }}" class="psc-input mt-2">
                    @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="psc-field__label" for="email">{{ __('E-posta') }}</label>
                    <input id="email" name="email" type="email" maxlength="255" value="{{ old('email', $user->email) }}" class="psc-input mt-2" autocomplete="email">
                    @error('email')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="border-t border-[#eef2f7] pt-5">
                    <p class="psc-field__label">{{ __('Şifre değiştir') }}</p>
                    <p class="mt-1 text-xs text-[#64748b]">{{ __('Boş bırakırsan şifren değişmez.') }}</p>
                    <div class="mt-3 space-y-3">
                        <div>
                            <label class="text-sm font-medium text-[#334155]" for="password">{{ __('Yeni şifre') }}</label>
                            <input id="password" name="password" type="password" class="psc-input mt-1.5" autocomplete="new-password">
                            @error('password')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-[#334155]" for="password_confirmation">{{ __('Yeni şifre (tekrar)') }}</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="psc-input mt-1.5" autocomplete="new-password">
                        </div>
                    </div>
                </div>

                <button type="submit" class="psc-btn psc-btn--primary">{{ __('Kaydet') }}</button>
            </div>
        </form>
    </div>
@endsection
