@php
    /** @var string $statusFilter */
    /** @var string $routeName */
    $tabs = [
        'all' => __('Tümü'),
        'pending' => __('Onay bekleyen'),
        'approved' => __('Onaylı / yayında'),
        'rejected' => __('Reddedilen'),
        'unpublished' => __('Yayından kaldırılan'),
    ];
@endphp

<nav class="psc-filter-tabs" aria-label="{{ __('Durum filtresi') }}">
    @foreach ($tabs as $key => $label)
        <a href="{{ route($routeName, ['durum' => $key]) }}"
            class="psc-filter-tab {{ $statusFilter === $key ? 'psc-filter-tab--active' : '' }}">
            {{ $label }}
        </a>
    @endforeach
</nav>
