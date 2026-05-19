@php
    $integrations = $siteIntegrations ?? \App\Support\SiteIntegrations::fromPlatform();
@endphp

@if (filled($integrations->googleSiteVerificationEffective()))
    <meta name="google-site-verification" content="{{ $integrations->googleSiteVerificationEffective() }}">
@endif
@if (filled($integrations->yandexVerificationEffective()))
    <meta name="yandex-verification" content="{{ $integrations->yandexVerificationEffective() }}">
@endif
@if (filled($integrations->bingSiteVerificationEffective()))
    <meta name="msvalidate.01" content="{{ $integrations->bingSiteVerificationEffective() }}">
@endif

@if ($integrations->googleAnalyticsConfigured())
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $integrations->googleAnalyticsMeasurementId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', '{{ $integrations->googleAnalyticsMeasurementId }}', { anonymize_ip: true });
    </script>
@endif

@if (filled($integrations->customHeadCss))
    <style id="site-custom-head-css">{!! $integrations->customHeadCss !!}</style>
@endif

@if (filled($integrations->customHeadHtml))
    {!! $integrations->customHeadHtml !!}
@endif
