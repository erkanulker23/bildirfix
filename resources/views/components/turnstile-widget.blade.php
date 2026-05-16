@props([
    'theme' => 'auto',
])

@if (filled(config('services.turnstile.site_key')))
    @once
        @push('scripts')
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer referrerpolicy="no-referrer-when-downgrade"></script>
        @endpush
    @endonce
    <div
        {{ $attributes->merge([
            'class' => 'cf-turnstile min-h-[65px]',
        ]) }}
        data-sitekey="{{ config('services.turnstile.site_key') }}"
        data-theme="{{ $theme ?? 'auto' }}"
    ></div>
@endif
