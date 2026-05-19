<?php

namespace App\Providers;

use App\Models\CampaignSupporter;
use App\Models\Comment;
use App\Models\PlatformSetting;
use App\Models\Post;
use App\Models\PostFollow;
use App\Models\Story;
use App\Models\Support;
use App\Observers\CampaignSupporterObserver;
use App\Observers\CommentObserver;
use App\Observers\PostFollowObserver;
use App\Observers\PostObserver;
use App\Observers\StoryObserver;
use App\Observers\SupportObserver;
use App\Support\AuthLookup;
use App\Support\Phone;
use App\Support\PageHero;
use App\Support\SiteBranding;
use App\Support\SiteIntegrations;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.force_https') || str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Post::observe(PostObserver::class);
        Story::observe(StoryObserver::class);
        Support::observe(SupportObserver::class);
        CampaignSupporter::observe(CampaignSupporterObserver::class);
        PostFollow::observe(PostFollowObserver::class);
        Comment::observe(CommentObserver::class);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(180)->by($request->user()?->getAuthIdentifier() ?: $request->ip());
        });

        RateLimiter::for('otp-send', function (Request $request) {
            $key = Phone::normalize((string) $request->input('phone', ''));

            return Limit::perMinute(4)->by($key !== '' ? $key : $request->ip());
        });

        RateLimiter::for('otp-verify', fn (Request $request) => Limit::perMinute(20)->by($request->ip()));

        RateLimiter::for('login', function (Request $request) {
            $raw = trim((string) $request->input('login', ''));
            if ($raw === '') {
                $raw = trim((string) $request->input('phone', ''));
            }
            $key = AuthLookup::credentialKey($raw);

            return Limit::perMinute(30)->by($key !== '' ? $key : $request->ip());
        });

        RateLimiter::for('create-content', fn (Request $request) => Limit::perMinute(45)->by(
            $request->user()?->getAuthIdentifier() ?: $request->ip()
        ));

        View::composer(['auth.login', 'auth.register'], function ($view): void {
            try {
                $enabled = PlatformSetting::current()->googleOAuthConfigured();
            } catch (\Throwable) {
                $enabled = false;
            }
            $view->with('googleOAuthEnabled', $enabled);
        });

        View::composer('layouts.admin', function ($view): void {
            $view->with('viewerIsSuperAdmin', request()->user()?->isSuperAdmin() ?? false);
        });

        View::composer('layouts.app', function ($view): void {
            $view->with('siteIntegrations', SiteIntegrations::fromPlatform());
            $view->with('siteBranding', SiteBranding::fromPlatform());

            if (! $view->offsetExists('pageHero')) {
                $view->with('pageHero', PageHero::forRoute(request()->route()?->getName()));
            }
        });

        try {
            /** @var PlatformSetting|null $platform */
            $platform = PlatformSetting::query()->orderBy('id')->first();
            if ($platform !== null && $platform->customSmtpConfigured()) {
                $port = $platform->mail_port;
                Config::set('mail.default', 'smtp');
                Config::set('mail.mailers.smtp.host', $platform->mail_host);
                Config::set('mail.mailers.smtp.port', $port ?: 587);
                Config::set('mail.mailers.smtp.encryption', $platform->mail_encryption);
                Config::set('mail.mailers.smtp.username', $platform->mail_username);
                Config::set('mail.mailers.smtp.password', $platform->mail_password);
                Config::set('mail.mailers.smtp.timeout', null);
                Config::set('mail.from.address', $platform->mail_from_address);
                $fromName = $platform->mail_from_name;
                Config::set('mail.from.name', ($fromName !== null && trim($fromName) !== '') ? $fromName : config('app.name'));
            }
        } catch (\Throwable) {
            //
        }
    }
}
