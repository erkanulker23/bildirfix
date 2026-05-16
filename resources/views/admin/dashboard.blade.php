@extends('layouts.app')

@section('title', __('Admin'))

@section('content')
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold">{{ __('Yönetim özeti') }}</h1>
        <p class="mt-2 text-sm text-slate-600">{{ __('Özel Filament kullanılmıyor — bu yapı tamamen özelleştirilebilir.') }}</p>

        <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="rounded-xl border border-violet-200 bg-violet-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-violet-700">{{ __('Kullanıcı') }}</p>
                <p class="mt-1 text-2xl font-semibold">{{ $usersCount }}</p>
            </div>
            <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">{{ __('Gönderi') }}</p>
                <p class="mt-1 text-2xl font-semibold">{{ $postsCount }}</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-800">{{ __('Açık') }}</p>
                <p class="mt-1 text-2xl font-semibold">{{ $openPosts }}</p>
            </div>
        </div>

        @if ($viewerIsSuperAdmin && (($pendingModeration ?? 0) > 0 || ($pendingCampaignModeration ?? 0) > 0))
            <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm">
                @if (($pendingModeration ?? 0) > 0)
                    <p class="font-semibold text-amber-950">{{ __('Onay bekleyen şikâyetler') }}: {{ $pendingModeration }}</p>
                    <a href="{{ route('admin.moderation.index') }}"
                        class="mt-2 inline-block font-semibold text-amber-900 underline">{{ __('Şikâyet moderasyonu') }}</a>
                @endif
                @if (($pendingCampaignModeration ?? 0) > 0)
                    <p class="mt-3 font-semibold text-violet-950">{{ __('Onay bekleyen kampanyalar') }}: {{ $pendingCampaignModeration }}</p>
                    <a href="{{ route('admin.campaign-moderation.index') }}"
                        class="mt-2 inline-block font-semibold text-violet-900 underline">{{ __('Kampanya moderasyonu') }}</a>
                @endif
            </div>
        @elseif ($viewerIsSuperAdmin)
            <p class="mt-6 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                <a href="{{ route('admin.moderation.index') }}" class="font-semibold text-violet-800 underline">{{ __('Şikâyet moderasyonu') }}</a>
                —
                {{ __('bekleyen yok.') }}
                <span class="mx-1 text-slate-400">|</span>
                <a href="{{ route('admin.campaign-moderation.index') }}" class="font-semibold text-violet-800 underline">{{ __('Kampanya moderasyonu') }}</a>
                —
                {{ (($pendingCampaignModeration ?? 0) > 0) ? $pendingCampaignModeration : __('bekleyen yok.') }}
            </p>
        @endif


        @if ($viewerIsSuperAdmin)
            <div class="mt-6 rounded-xl border border-violet-200 bg-violet-50/90 px-4 py-3 text-[13px] font-semibold text-violet-950">
                <a href="{{ route('admin.platform-settings.edit') }}" class="underline decoration-2">{{ __('Google ile oturum — platform ayarları') }}</a>
            </div>
        @endif

        <p class="mt-6 text-sm text-slate-600">{{ __('Kent sorunu moderasyon süreci süper yöneticidedir; yöneticiler özeti görür.') }}</p>
    </section>
@endsection
