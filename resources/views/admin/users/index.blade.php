@extends('layouts.admin')

@section('title', __('Kullanıcı izleme'))

@section('content')
    <div class="space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Kayıtlı kullanıcılar') }}</h1>
                <p class="psc-page-desc">{{ __('E-posta onayı, doğrulama durumu ve temel iletişim bilgileri.') }}</p>
            </div>
        </div>

        <form method="get" class="psc-filter">
            <div class="psc-filter__grid">
                <div>
                    <label class="psc-field__label" for="user-search">{{ __('Ara') }}</label>
                    <input id="user-search" type="search" name="q" value="{{ $search }}" class="psc-input"
                        placeholder="{{ __('E-posta, ad, telefon…') }}">
                </div>
                <div>
                    <label class="psc-field__label" for="user-mail">{{ __('E-posta onayı') }}</label>
                    <select id="user-mail" name="mail" class="psc-select" onchange="this.form.submit()">
                        <option value="all" @selected($mailFilter === 'all')>{{ __('Tümü') }}</option>
                        <option value="yes" @selected($mailFilter === 'yes')>{{ __('Onaylı') }}</option>
                        <option value="no" @selected($mailFilter === 'no')>{{ __('Onaysız') }}</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="psc-btn psc-btn--primary w-full sm:w-auto">{{ __('Filtrele') }}</button>
                </div>
            </div>
        </form>

        <div class="psc-table-wrap">
            <div class="psc-table-scroll">
                <table class="psc-table">
                    <thead>
                        <tr>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Ad') }}</th>
                            <th>{{ __('E-posta') }}</th>
                            <th>{{ __('E-posta onayı') }}</th>
                            <th>{{ __('Telefon') }}</th>
                            <th>{{ __('Rol') }}</th>
                            <th>{{ __('Şikâyet') }}</th>
                            <th class="text-right">{{ __('İşlem') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr>
                                <td class="font-mono text-xs text-[#64748b]">{{ $u->id }}</td>
                                <td class="psc-table__primary">{{ $u->name }}</td>
                                <td class="psc-table__link">{{ $u->email ?? '—' }}</td>
                                <td>
                                    @if ($u->email_verified_at)
                                        <span class="psc-badge psc-badge--success">{{ __('Onaylı') }}</span>
                                        <span class="mt-1 block text-[10px] text-[#94a3b8]">{{ $u->email_verified_at->timezone(config('app.timezone'))->format('d.m.Y') }}</span>
                                    @else
                                        <span class="psc-badge psc-badge--warn">{{ __('Onaysız') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span>{{ $u->phone ?? '—' }}</span>
                                    @if ($u->phone_verified_at)
                                        <span class="mt-1 block text-[11px] font-medium text-[#16a34a]">{{ __('Tel. onaylı') }}</span>
                                    @endif
                                </td>
                                <td><span class="psc-badge psc-badge--neutral">{{ $u->role->value }}</span></td>
                                <td>
                                    <span class="psc-badge psc-badge--neutral tabular-nums">{{ number_format((int) ($u->posts_count ?? 0)) }}</span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="psc-table__link">{{ __('Düzenle') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $users->links() }}</div>
    </div>
@endsection
