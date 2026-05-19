@props(['status'])

@php
    $statusValue = $status instanceof \BackedEnum ? $status->value : (string) $status;
    $class = match ($statusValue) {
        'approved' => 'psc-badge--success',
        'pending' => 'psc-badge--warn',
        'rejected' => 'psc-badge--neutral',
        'unpublished' => 'psc-badge--neutral',
        default => 'psc-badge--neutral',
    };
@endphp

<span class="psc-badge {{ $class }}">{{ $status->label() }}</span>
