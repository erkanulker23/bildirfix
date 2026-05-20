@extends('layouts.admin')

@section('title', __('Dış kaynak içe aktarım'))

@section('content')
    <div class="space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Şikayetvar ve dış kaynaklar') }}</h1>
                <p class="psc-page-desc">
                    {{ __('Marka veya kurum sayfası URL’si ekleyin; “Şimdi çek” ile şikâyetler moderasyon kuyruğuna ve “Tüm şikâyetler” listesine düşer. Medya ve kullanıcılar otomatik oluşturulur.') }}
                </p>
            </div>
            <a href="{{ route('admin.external-imports.create') }}" class="psc-btn psc-btn--primary">{{ __('Yeni kaynak') }}</a>
        </div>

        @if (session('status'))
            <p class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</p>
        @endif

        <div class="psc-table-wrap">
            <div class="psc-table-scroll">
                <table class="psc-table">
                    <thead>
                        <tr>
                            <th>{{ __('Ad') }}</th>
                            <th>{{ __('Tür') }}</th>
                            <th>{{ __('URL') }}</th>
                            <th>{{ __('Kurum') }}</th>
                            <th>{{ __('Durum') }}</th>
                            <th>{{ __('Son çekim') }}</th>
                            <th class="text-right">{{ __('İşlem') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sources as $source)
                            <tr>
                                <td class="psc-table__primary">{{ $source->name }}</td>
                                <td><span class="psc-badge psc-badge--neutral">{{ $source->type->label() }}</span></td>
                                <td class="max-w-[200px] truncate text-xs">
                                    <a href="{{ $source->source_url }}" target="_blank" rel="noopener noreferrer"
                                        class="psc-table__link">{{ $source->source_url }}</a>
                                </td>
                                <td>{{ $source->institution?->name ?? '—' }}</td>
                                <td>
                                    @if ($source->enabled)
                                        <span class="psc-badge psc-badge--success">{{ __('Açık') }}</span>
                                    @else
                                        <span class="psc-badge psc-badge--neutral">{{ __('Kapalı') }}</span>
                                    @endif
                                    @if ($source->auto_sync)
                                        <span class="psc-badge psc-badge--neutral ml-1">{{ __('Otomatik') }}</span>
                                    @endif
                                </td>
                                <td class="text-xs text-[var(--psc-text-muted)]">
                                    @if ($source->last_synced_at)
                                        {{ $source->last_synced_at->format('d.m.Y H:i') }}
                                        <span class="block">+{{ $source->last_imported_count }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-right whitespace-nowrap space-x-2">
                                    <form method="POST" action="{{ route('admin.external-imports.run', $source) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="psc-btn psc-btn--primary psc-btn--sm">{{ __('Şimdi çek') }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.external-imports.run', $source) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="queue" value="1">
                                        <button type="submit" class="psc-btn psc-btn--sm">{{ __('Kuyruk') }}</button>
                                    </form>
                                    <a href="{{ route('admin.external-imports.edit', $source) }}" class="psc-table__link">{{ __('Düzenle') }}</a>
                                </td>
                            </tr>
                            @if ($source->last_sync_error)
                                <tr>
                                    <td colspan="7" class="text-xs text-rose-600 bg-rose-50/50">{{ Str::limit($source->last_sync_error, 300) }}</td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-[var(--psc-text-muted)]">
                                    {{ __('Henüz kaynak yok. Örnek: https://www.sikayetvar.com/atasehir-belediyesi') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <p class="text-sm text-[var(--psc-text-muted)]">
            {{ __('Komut satırı:') }} <code class="font-mono text-xs">php artisan bildir:import-external --sync --source=ID</code>
        </p>
    </div>
@endsection
