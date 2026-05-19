@extends('layouts.admin')

@section('title', __('Kurum kayıtları'))

@section('content')
    <div class="space-y-6">
        <motion.div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Kurumlar ve bağlantılı hesaplar') }}</h1>
                <p class="psc-page-desc">{{ __('Kurum adı, tür, şehir, e-posta veya ID ile arayın.') }}</p>
            </div>
        </motion.div>

        <form method="get" class="psc-filter">
            <div class="psc-filter__grid">
                <div class="sm:col-span-2">
                    <label class="psc-field__label" for="inst-search">{{ __('Ara') }}</label>
                    <input id="inst-search" name="q" type="search" value="{{ $q ?? '' }}"
                        placeholder="{{ __('Kurum, şehir, e-posta…') }}" class="psc-input">
                </div>
                <div>
                    <button type="submit" class="psc-btn psc-btn--primary w-full sm:w-auto">{{ __('Filtrele') }}</button>
                    @if (! empty($q))
                        <a href="{{ route('admin.institutions.index') }}" class="psc-btn psc-btn--ghost mt-2 w-full sm:mt-0 sm:ml-2">{{ __('Temizle') }}</a>
                    @endif
                </div>
            </div>
        </form>

        <div class="psc-table-wrap">
            <div class="psc-table-scroll">
                <table class="psc-table">
                    <thead>
                        <tr>
                            <th class="w-14">{{ __('Logo') }}</th>
                            <th>{{ __('Kurum') }}</th>
                            <th>{{ __('Tür') }}</th>
                            <th>{{ __('Şehir') }}</th>
                            <th>{{ __('Şikâyet') }}</th>
                            <th>{{ __('Hesap') }}</th>
                            <th>{{ __('Doğrulandı') }}</th>
                            <th class="text-right">{{ __('İşlem') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($institutions as $ins)
                            <tr>
                                <td>
                                    <img src="{{ $ins->displayLogoUrl() }}" alt="" width="40" height="40"
                                        class="h-10 w-10 rounded-lg border border-[var(--psc-border)] object-cover bg-white" loading="lazy">
                                </td>
                                <td class="psc-table__primary">{{ $ins->name }}</td>
                                <td>{{ $types[$ins->type] ?? $ins->type ?? '—' }}</td>
                                <td>{{ $ins->city?->name ?? '—' }}</td>
                                <td>
                                    <span class="psc-badge psc-badge--neutral tabular-nums">{{ number_format((int) ($ins->complaints_count ?? 0)) }}</span>
                                </td>
                                <td class="psc-table__link text-xs">{{ $ins->accountUser?->email ?? '—' }}</td>
                                <td>
                                    @if ($ins->verified)
                                        <span class="psc-badge psc-badge--success">{{ __('Evet') }}</span>
                                    @else
                                        <span class="psc-badge psc-badge--warn">{{ __('Hayır') }}</span>
                                    @endif
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <a href="{{ route('admin.institutions.edit', $ins) }}" class="psc-table__link">{{ __('Düzenle') }}</a>
                                    <a href="{{ route('institutions.show', $ins) }}" target="_blank" rel="noopener"
                                        class="ml-2 text-xs font-semibold text-[var(--psc-text-muted)] hover:text-[var(--psc-text-strong)]">{{ __('Ön yüz') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-sm text-[var(--psc-text-muted)]">{{ __('Sonuç bulunamadı.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $institutions->links() }}</motion.div>
    </div>
@endsection
