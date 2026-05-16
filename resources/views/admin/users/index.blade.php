@extends('layouts.admin')

@section('admin_heading', __('Kullanıcılar'))
@section('title', __('Kullanıcı izleme'))

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">{{ __('Kayıtlı kullanıcılar') }}</h1>
                <p class="mt-2 text-sm text-slate-500">{{ __('E-posta onayı (`email_verified_at`), doğrulama durumu ve temel iletişim bilgileri.') }}</p>
            </div>
            <form method="get" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Ara') }}</label>
                    <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('E-posta, ad, telefon…') }}"
                        class="mt-1 w-48 min-w-[12rem] rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm placeholder:text-slate-400 sm:w-64 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('E-posta onayı') }}</label>
                    <select name="mail" onchange="this.form.submit()"
                        class="mt-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-900 shadow-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                        <option value="all" @selected($mailFilter === 'all')>{{ __('Tümü') }}</option>
                        <option value="yes" @selected($mailFilter === 'yes')>{{ __('Onaylı') }}</option>
                        <option value="no" @selected($mailFilter === 'no')>{{ __('Onaysız') }}</option>
                    </select>
                </div>
                <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">{{ __('Uygula') }}</button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full min-w-[840px] text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('ID') }}</th>
                        <th class="px-4 py-3">{{ __('Ad') }}</th>
                        <th class="px-4 py-3">{{ __('E-posta') }}</th>
                        <th class="px-4 py-3">{{ __('E-posta onayı') }}</th>
                        <th class="px-4 py-3">{{ __('Telefon / onay') }}</th>
                        <th class="px-4 py-3">{{ __('Rol') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('İşlem') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $u)
                        <tr class="text-slate-700">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $u->id }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $u->name }}</td>
                            <td class="px-4 py-3 text-blue-700">{{ $u->email ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if ($u->email_verified_at)
                                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-900 ring-1 ring-emerald-200">{{ __('Onaylı') }}</span>
                                    <span class="mt-1 block text-[10px] text-slate-400">{{ $u->email_verified_at->timezone(config('app.timezone'))->format('d.m.Y') }}</span>
                                @else
                                    <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-bold text-amber-950 ring-1 ring-amber-200">{{ __('Onaysız') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <span>{{ $u->phone ?? '—' }}</span>
                                @if ($u->phone_verified_at)
                                    <span class="mt-1 block text-[11px] font-semibold text-emerald-700">{{ __('Tel. onaylı') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-800">{{ $u->role->value }}</span></td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.users.edit', $u) }}" class="text-xs font-bold text-blue-600 hover:underline">{{ __('Düzenle') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>{{ $users->links() }}</div>
    </div>
@endsection
