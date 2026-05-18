@extends('layouts.admin')

@section('admin_heading', __('Kurumlar'))
@section('title', __('Kurum kayıtları'))

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">{{ __('Kurumlar ve bağlantılı hesaplar') }}</h1>
                <p class="mt-2 text-sm text-slate-500">{{ __('Hızlı arama: kurum adı, tür, şehir, e-posta veya ID.') }}</p>
            </div>
            <form method="get" class="flex items-end gap-2">
                <div class="min-w-[14rem]">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Ara') }}</label>
                    <input name="q" type="search" value="{{ $q ?? '' }}" placeholder="{{ __('Kurum, şehir, e-posta…') }}"
                        class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                </div>
                <button type="submit"
                    class="rounded-xl bg-slate-800 px-4 py-2 text-xs font-bold text-white hover:bg-slate-900">{{ __('Filtrele') }}</button>
                @if (! empty($q))
                    <a href="{{ route('admin.institutions.index') }}"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50">{{ __('Temizle') }}</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="w-14 px-4 py-3">{{ __('Logo') }}</th>
                        <th class="px-4 py-3">{{ __('Kurum') }}</th>
                        <th class="px-4 py-3">{{ __('Tür') }}</th>
                        <th class="px-4 py-3">{{ __('Şehir') }}</th>
                        <th class="px-4 py-3">{{ __('Hesap') }}</th>
                        <th class="px-4 py-3">{{ __('Doğrulandı') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('İşlem') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($institutions as $ins)
                        <tr class="text-slate-700">
                            <td class="px-4 py-3">
                                <img src="{{ $ins->displayLogoUrl() }}" alt="" width="40" height="40"
                                    class="h-10 w-10 rounded-xl border border-slate-200 bg-white object-cover shadow-sm" loading="lazy">
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $ins->name }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $types[$ins->type] ?? $ins->type ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $ins->city?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs text-blue-700">{{ $ins->accountUser?->email ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if ($ins->verified)
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-900">{{ __('Evet') }}</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-600">{{ __('Hayır') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.institutions.edit', $ins) }}"
                                    class="text-xs font-bold text-blue-600 hover:underline">{{ __('Düzenle') }}</a>
                                <a href="{{ route('institutions.show', $ins) }}" target="_blank" rel="noopener"
                                    class="ml-3 text-xs font-semibold text-slate-500 hover:text-slate-800">{{ __('Ön yüz') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">{{ __('Sonuç bulunamadı.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $institutions->links() }}</div>
    </div>
@endsection
