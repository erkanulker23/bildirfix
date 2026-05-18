@extends('layouts.admin')

@section('admin_heading', __('Kampanya izleme'))
@section('title', __('Tüm kampanyalar'))

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">{{ __('Kampanya kayıtları') }}</h1>
                <p class="mt-2 text-sm text-slate-500">{{ __('Arama, düzenleme ve toplu silme.') }}</p>
            </div>
            <a href="{{ route('admin.campaigns.create') }}"
                class="shrink-0 self-end rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-blue-700">{{ __('Kampanya oluştur') }}</a>
            <form method="get" class="flex flex-wrap items-end gap-2">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Ara') }}</label>
                    <input name="q" type="search" value="{{ $searchQuery ?? '' }}" placeholder="{{ __('Başlık, kullanıcı…') }}"
                        class="mt-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Durum') }}</label>
                    <select name="durum"
                        class="mt-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-900 shadow-sm">
                        <option value="all" @selected($statusFilter === 'all')>{{ __('Tümü') }}</option>
                        <option value="pending" @selected($statusFilter === 'pending')>{{ __('Onay bekliyor') }}</option>
                        <option value="approved" @selected($statusFilter === 'approved')>{{ __('Yayında') }}</option>
                        <option value="rejected" @selected($statusFilter === 'rejected')>{{ __('Red') }}</option>
                        <option value="unpublished" @selected($statusFilter === 'unpublished')>{{ __('Yayından kaldırıldı') }}</option>
                    </select>
                </div>
                <button type="submit"
                    class="rounded-xl bg-slate-800 px-4 py-2 text-xs font-bold text-white hover:bg-slate-900">{{ __('Filtrele') }}</button>
            </form>
        </div>

        <form method="POST" action="{{ route('admin.campaigns.bulk-destroy') }}" id="campaign-bulk-form"
            onsubmit="return confirm(@js(__('Seçili kampanyalar kalıcı olarak silinsin mi?')))">
            @csrf
            @method('DELETE')
            @foreach (request()->only(['durum', 'q', 'page']) as $k => $v)
                @if ($v !== null && $v !== '')
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endif
            @endforeach

            <div class="mb-3 flex flex-wrap items-center gap-3">
                <button type="submit"
                    class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-800 hover:bg-rose-100">
                    {{ __('Seçilenleri sil') }}
                </button>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
                <table class="w-full min-w-[800px] text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3 w-10">
                                <input type="checkbox" class="rounded" onclick="document.querySelectorAll('.campaign-row-cb').forEach(c => c.checked = this.checked)">
                            </th>
                            <th class="px-4 py-3">{{ __('Başlık') }}</th>
                            <th class="px-4 py-3">{{ __('Kullanıcı') }}</th>
                            <th class="px-4 py-3">{{ __('Şehir') }}</th>
                            <th class="px-4 py-3">{{ __('Durum') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('İşlem') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($campaigns as $campaign)
                            <tr class="text-slate-700">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="ids[]" value="{{ $campaign->id }}" class="campaign-row-cb rounded">
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-900">{{ $campaign->title }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $campaign->user?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $campaign->city?->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-800">{{ $campaign->moderation_status->label() }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.campaigns.edit', $campaign) }}"
                                        class="text-xs font-bold text-blue-600 hover:underline">{{ __('Düzenle') }}</a>
                                    @if ($campaign->isPubliclyApproved())
                                        <a href="{{ route('campaigns.show', $campaign) }}" target="_blank" rel="noopener"
                                            class="ml-3 text-xs font-semibold text-slate-500 hover:text-slate-800">{{ __('Ön yüz') }}</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>

        <div>{{ $campaigns->links() }}</div>
    </div>
@endsection
