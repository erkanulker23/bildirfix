@extends('layouts.admin')

@section('admin_heading', __('Kampanya izleme'))
@section('title', __('Tüm kampanyalar'))

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">{{ __('Kampanya kayıtları') }}</h1>
                <p class="mt-2 text-sm text-slate-500">{{ __('Durum süzgeci; ön yüzde yalnızca onaylı kampanyalar listelenir.') }}</p>
            </div>
            <form method="get" class="flex flex-wrap items-center gap-2">
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Durum') }}</label>
                <select name="durum" onchange="this.form.submit()"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-900 shadow-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    <option value="all" @selected($statusFilter === 'all')>{{ __('Tümü') }}</option>
                    <option value="pending" @selected($statusFilter === 'pending')>{{ __('Onay bekliyor') }}</option>
                    <option value="approved" @selected($statusFilter === 'approved')>{{ __('Yayında') }}</option>
                    <option value="rejected" @selected($statusFilter === 'rejected')>{{ __('Red') }}</option>
                    <option value="unpublished" @selected($statusFilter === 'unpublished')>{{ __('Yayından kaldırıldı') }}</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('Başlık') }}</th>
                        <th class="px-4 py-3">{{ __('Kullanıcı') }}</th>
                        <th class="px-4 py-3">{{ __('Şehir') }}</th>
                        <th class="px-4 py-3">{{ __('Durum') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Ön yüz') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($campaigns as $campaign)
                        <tr class="text-slate-700">
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $campaign->title }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $campaign->user?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $campaign->city?->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-800">{{ $campaign->moderation_status->label() }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if ($campaign->isPubliclyApproved())
                                    <a href="{{ route('campaigns.show', $campaign) }}" target="_blank" rel="noopener"
                                        class="text-xs font-bold text-blue-600 hover:underline">{{ __('Aç') }}</a>
                                @else
                                    <span class="text-xs text-slate-400">{{ __('—') }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>{{ $campaigns->links() }}</div>
    </div>
@endsection
