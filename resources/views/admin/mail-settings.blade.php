@extends('layouts.admin')

@section('admin_heading', __('E-posta'))
@section('title', __('E-posta (SMTP)'))

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">{{ __('Giden e-posta (SMTP)') }}</h1>
            <p class="mt-2 text-sm text-slate-500">
                {{ __('Panel SMTP açıksa gönderim bu ayarlarla yapılır; kapalıysa .env kullanılır.') }}</p>
        </div>

        <form method="POST" action="{{ route('admin.mail-settings.update') }}" class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PATCH')

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Panel SMTP kullan') }}</label>
                <select name="mail_use_custom_smtp"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    <option value="0" @selected(! $settings->mail_use_custom_smtp)>{{ __('Kapalı (.env)') }}</option>
                    <option value="1" @selected($settings->mail_use_custom_smtp)>{{ __('Açık') }}</option>
                </select>
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Gönderen e-posta') }}</label>
                <input name="mail_from_address" type="email" value="{{ old('mail_from_address', $settings->mail_from_address) }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Gönderen adı') }}</label>
                <input name="mail_from_name" type="text" value="{{ old('mail_from_name', $settings->mail_from_name) }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Sunucu') }}</label>
                    <input name="mail_host" type="text" value="{{ old('mail_host', $settings->mail_host) }}"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" autocomplete="off">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Port') }}</label>
                    <input name="mail_port" type="number" value="{{ old('mail_port', $settings->mail_port) }}" placeholder="587"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Şifreleme') }}</label>
                <select name="mail_encryption"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    @php $enc = old('mail_encryption', $settings->mail_encryption); @endphp
                    <option value="" @selected($enc === null || $enc === '')>{{ __('Yok') }}</option>
                    <option value="tls" @selected($enc === 'tls')>TLS</option>
                    <option value="ssl" @selected($enc === 'ssl')>SSL</option>
                </select>
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Kullanıcı adı') }}</label>
                <input name="mail_username" type="text" value="{{ old('mail_username', $settings->mail_username) }}" autocomplete="off"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Parola') }}</label>
                <input name="mail_password" type="password" value="" autocomplete="new-password"
                    placeholder="{{ __('Boş bırakırsanız mevcut parola korunur.') }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-blue-600 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700">{{ __('Kaydet') }}</button>
        </form>
    </div>
@endsection
