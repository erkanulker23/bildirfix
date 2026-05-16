@extends('layouts.app')

@section('title', __('Platform ayarları').' • Admin')

@section('content')
    <section class="mx-auto max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div>
                <h1 class="text-xl font-black text-slate-900">{{ __('Platform ve Google kayıt') }}</h1>
                <p class="mt-1 text-sm font-medium text-slate-600">
                    {{ __('Sadece süper yönetici görür. Google konsolundan aldığınız OAuth istemci bilgileri buraya girilir; site ön yüzünde süper girişi linklenmez.') }}</p>
            </div>
            <a href="{{ route('admin.dashboard') }}"
                class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-800 hover:bg-slate-50">{{ __('Özet') }}</a>
        </div>

        <form action="{{ route('admin.platform-settings.update') }}" method="post" class="mt-8 space-y-6">
            @csrf
            @method('PATCH')

            <div>
                <label for="oauth-en" class="block text-[11px] font-black uppercase tracking-wider text-slate-500">{{ __('Google ile üye olunabilsin') }}</label>
                <select id="oauth-en" name="google_oauth_enabled"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-900 outline-none ring-4 ring-transparent focus:border-violet-500 focus:ring-violet-500/15">
                    <option value="0" @selected(! $settings->google_oauth_enabled)>{{ __('Kapalı') }}</option>
                    <option value="1" @selected($settings->google_oauth_enabled)>{{ __('Açık (kimlik doğrulanmış ise)') }}</option>
                </select>
            </div>

            <div>
                <label for="gid" class="block text-[11px] font-black uppercase tracking-wider text-slate-500">{{ __('OAuth istemci kimliği (Client ID)') }}</label>
                <input id="gid" name="google_client_id" type="text" value="{{ old('google_client_id', $settings->google_client_id) }}" autocomplete="off"
                    placeholder="xxxxxxxx.apps.googleusercontent.com"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 outline-none ring-4 ring-transparent focus:border-violet-500 focus:ring-violet-500/15">
            </div>

            <div>
                <label for="gs" class="block text-[11px] font-black uppercase tracking-wider text-slate-500">{{ __('OAuth istemci sırrı (Client secret)') }}</label>
                <input id="gs" name="google_client_secret" type="password" value="" autocomplete="new-password"
                    placeholder="{{ __('Güncellemek için yeni sırrı yapıştırın; boş bırakırsanız mevcut sırrı koruyunuz.') }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none ring-4 ring-transparent focus:border-violet-500 focus:ring-violet-500/15">
            </div>

            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-[13px] font-semibold leading-relaxed text-amber-950">
                <p class="font-black">{{ __('Google Cloud Console’da sabit geri adres (Authorized redirect URIs)') }}</p>
                <p class="mt-2"><code dir="ltr" class="block break-all rounded-lg bg-white/70 px-2 py-1 text-xs text-slate-900">{{ $redirectUri }}</code></p>
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-violet-600 py-3.5 text-sm font-black text-white shadow-lg shadow-violet-600/30 hover:bg-violet-700">{{ __('Kaydet') }}</button>
        </form>
    </section>
@endsection
