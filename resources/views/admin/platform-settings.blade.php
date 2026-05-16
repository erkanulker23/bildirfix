@extends('layouts.admin')

@section('admin_heading', __('Platform'))
@section('title', __('Platform ayarları'))

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">{{ __('Platform ve Google kayıt') }}</h1>
            <p class="mt-2 text-sm text-slate-500">{{ __('OAuth istemci bilgileri.') }}</p>
        </div>

        <form action="{{ route('admin.platform-settings.update') }}" method="post"
            class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PATCH')

            <div>
                <label for="oauth-en" class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Google ile üye olunabilsin') }}</label>
                <select id="oauth-en" name="google_oauth_enabled"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    <option value="0" @selected(! $settings->google_oauth_enabled)>{{ __('Kapalı') }}</option>
                    <option value="1" @selected($settings->google_oauth_enabled)>{{ __('Açık') }}</option>
                </select>
            </div>

            <div>
                <label for="gid" class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('OAuth istemci kimliği') }}</label>
                <input id="gid" name="google_client_id" type="text" value="{{ old('google_client_id', $settings->google_client_id) }}" autocomplete="off"
                    placeholder="xxxxxxxx.apps.googleusercontent.com"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div>
                <label for="gs" class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('OAuth istemci sırrı') }}</label>
                <input id="gs" name="google_client_secret" type="password" value="" autocomplete="new-password"
                    placeholder="{{ __('Yeni sırrı yapıştırın; boş bırakırsanız korunur.') }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs font-semibold text-amber-950">
                <p class="font-bold">{{ __('Authorized redirect URIs') }}</p>
                <p class="mt-2 break-all rounded-lg bg-white px-2 py-1 font-mono text-[11px] text-slate-800">{{ $redirectUri }}</p>
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-blue-600 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700">{{ __('Kaydet') }}</button>
        </form>
    </div>
@endsection
