<?php

use App\Http\Controllers\Admin\AdPlacementAdminController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\CampaignAdminController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CampaignModerationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HomepageSettingsController;
use App\Http\Controllers\Admin\InstitutionAdminController;
use App\Http\Controllers\Admin\LegalPagesController;
use App\Http\Controllers\Admin\MailSettingsController;
use App\Http\Controllers\Admin\PlatformSettingController;
use App\Http\Controllers\Admin\PostModerationController;
use App\Http\Controllers\Admin\SiteIntegrationController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\UserAdminEditController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleOAuthController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\StaffAuthenticatedSessionController;
use App\Http\Controllers\Auth\VerifyPhoneController;
use App\Http\Controllers\BlogIndexController;
use App\Http\Controllers\BlogShowController;
use App\Http\Controllers\CampaignCreateController;
use App\Http\Controllers\CampaignIndexController;
use App\Http\Controllers\CampaignShowController;
use App\Http\Controllers\CampaignStoreController;
use App\Http\Controllers\CityExploreController;
use App\Http\Controllers\CityPublicController;
use App\Http\Controllers\CitySearchController;
use App\Http\Controllers\ContactPageController;
use App\Http\Controllers\CreatePostWizardController;
use App\Http\Controllers\FeedIndexController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndexNowKeyController;
use App\Http\Controllers\Institution\DashboardController as InstitutionDashboardController;
use App\Http\Controllers\InstitutionPublicController;
use App\Http\Controllers\LegalPageController;
use App\Http\Controllers\NotificationsPageController;
use App\Http\Controllers\Panel\DashboardController as PanelDashboardController;
use App\Http\Controllers\Panel\MyCampaignsController as PanelMyCampaignsController;
use App\Http\Controllers\Panel\MyPostsController as PanelMyPostsController;
use App\Http\Controllers\Panel\ProfileController as PanelProfileController;
use App\Http\Controllers\PostShowController;
use App\Http\Controllers\ProfilePageController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\Web\CampaignCommentStoreController;
use App\Http\Controllers\Web\CampaignSupportWebController;
use App\Http\Controllers\Web\GeoDistrictsController;
use App\Http\Controllers\Web\GeoInstitutionsController;
use App\Http\Controllers\Web\GeoNeighborhoodsController;
use App\Http\Controllers\Web\GeoReverseController;
use App\Http\Controllers\Web\PostFollowWebController;
use App\Http\Controllers\Web\PostSupportWebController;
use App\Http\Controllers\Web\QuickComplaintController;
use Illuminate\Support\Facades\Route;

Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');

Route::get('/', HomeController::class)->name('home');
Route::get('akis', FeedIndexController::class)->name('feed.index');

Route::middleware('throttle:180,1')->group(function (): void {
    Route::get('geo/districts', GeoDistrictsController::class)->name('geo.districts');
    Route::get('geo/institutions', GeoInstitutionsController::class)->name('geo.institutions');
    Route::get('geo/neighborhoods', GeoNeighborhoodsController::class)->name('geo.neighborhoods');
    Route::get('geo/reverse', GeoReverseController::class)->name('geo.reverse');
});

Route::redirect('kesfet', '/kampanyalar')->name('explore');
Route::redirect('harita', '/')->name('map');
Route::get('paylasim-olustur', CreatePostWizardController::class)->name('posts.create');
Route::get('bildir', CreatePostWizardController::class)->name('complaints.quick.create');
Route::get('kampanyalar', CampaignIndexController::class)->name('campaigns.index');
Route::get('kampanya/{campaign:slug}', CampaignShowController::class)->name('campaigns.show');
Route::get('sikayet/{post}', PostShowController::class)->name('posts.show');
Route::get('kurum/{institution}', InstitutionPublicController::class)->name('institutions.show');
Route::get('sehirini-kesfet', CityExploreController::class)->name('cities.explore');
Route::get('api/sehir-ara', CitySearchController::class)->name('api.cities.search')->middleware('throttle:60,1');
Route::get('il/{city:slug}', CityPublicController::class)->name('cities.show');
Route::get('blog', BlogIndexController::class)->name('blog.index');
Route::get('blog/{slug}', BlogShowController::class)->name('blog.show')->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*');

Route::redirect('/admin', '/admin/dashboard');
Route::redirect('/yonetici', '/superlogin', 302);
Route::redirect('/login/yonetici', '/superlogin', 302);
Route::redirect('/admin-giris', '/superlogin', 302);
Route::redirect('/giris/yonetici', '/superlogin', 302);

Route::view('nasil-calisir', 'pages.nasil-calisir')->name('how-it-works');

Route::get('iletisim', [ContactPageController::class, 'show'])->name('contact');
Route::post('iletisim', [ContactPageController::class, 'store'])
    ->middleware(['turnstile', 'throttle:8,1'])
    ->name('contact.store');

Route::get('yasal/gizlilik', [LegalPageController::class, 'privacy'])->name('legal.privacy');
Route::get('yasal/kvkk', [LegalPageController::class, 'kvkk'])->name('legal.kvkk');
Route::get('yasal/kullanim-kosullari', [LegalPageController::class, 'terms'])->name('legal.terms');

Route::middleware('guest')->group(function (): void {
    Route::get('auth/google', [GoogleOAuthController::class, 'redirect'])->name('auth.google.redirect')->middleware('throttle:login');
    Route::get('auth/google/callback', [GoogleOAuthController::class, 'callback'])->middleware('throttle:login')->name('auth.google.callback');
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware(['throttle:login', 'turnstile']);
    Route::get('brand', [StaffAuthenticatedSessionController::class, 'createBrand'])->name('login.brand');
    Route::post('brand', [StaffAuthenticatedSessionController::class, 'store'])->middleware(['throttle:login', 'turnstile'])->name('login.brand.store');
    Route::get('superlogin', [StaffAuthenticatedSessionController::class, 'createSuper'])->name('login.super');
    Route::post('superlogin', [StaffAuthenticatedSessionController::class, 'store'])->middleware(['throttle:login', 'turnstile'])->name('login.super.store');
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])->middleware(['throttle:otp-send', 'turnstile']);

    Route::get('sifremi-unuttum', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('sifremi-unuttum', [PasswordResetLinkController::class, 'store'])->middleware('throttle:6,1')->name('password.email');
    Route::get('sifre-sifirla/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('sifre-sifirla', [NewPasswordController::class, 'store'])->middleware('throttle:6,1')->name('password.update');
});

Route::get('verify-phone', [VerifyPhoneController::class, 'create'])->name('verify.phone.form');
Route::post('verify-phone', [VerifyPhoneController::class, 'store'])
    ->middleware('throttle:otp-verify')
    ->name('verify.phone');
Route::post('verify-phone/resend', [VerifyPhoneController::class, 'resend'])
    ->middleware('throttle:otp-send')
    ->name('verify.phone.resend');

Route::middleware('auth')->group(function (): void {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::post('paylasim-taslak', [QuickComplaintController::class, 'storeDraft'])
    ->middleware(['throttle:create-content', 'turnstile'])
    ->name('complaints.draft.store');
Route::post('bildir-taslak', [QuickComplaintController::class, 'storeDraft'])
    ->middleware(['throttle:create-content', 'turnstile'])
    ->name('complaints.quick.draft.store');

Route::middleware(['auth', 'verified.phone'])->group(function (): void {
    Route::post('paylasim-olustur', [QuickComplaintController::class, 'store'])
        ->middleware(['throttle:create-content', 'turnstile'])
        ->name('posts.store');
    Route::post('bildir', [QuickComplaintController::class, 'store'])
        ->middleware(['throttle:create-content', 'turnstile'])
        ->name('complaints.quick.store');

    Route::get('kampanya-baslat', CampaignCreateController::class)->name('campaigns.create');
    Route::post('kampanya-baslat', CampaignStoreController::class)
        ->middleware(['throttle:create-content'])
        ->name('campaigns.store');

    Route::post('kampanya/{campaign:slug}/destek', CampaignSupportWebController::class)
        ->name('campaigns.support.web')
        ->middleware('throttle:create-content');

    Route::post('kampanya/{campaign:slug}/yorum', CampaignCommentStoreController::class)
        ->name('campaigns.comments.store')
        ->middleware('throttle:create-content');

    Route::post('sikayet/{post}/destek', PostSupportWebController::class)
        ->name('posts.support.web')
        ->middleware('throttle:create-content');
    Route::post('sikayet/{post}/takip', PostFollowWebController::class)
        ->name('posts.follow.web')
        ->middleware('throttle:create-content');

    Route::get('profil', ProfilePageController::class)->name('profile');
    Route::get('bildirimler', NotificationsPageController::class)->name('notifications.index');
});

Route::middleware(['auth', 'verified.phone'])
    ->prefix('panel')
    ->name('panel.')
    ->group(function (): void {
        Route::get('dashboard', PanelDashboardController::class)->name('dashboard');
        Route::get('bildirimlerim', [PanelMyPostsController::class, 'index'])->name('posts.index');
        Route::get('bildirimlerim/{post}/duzenle', [PanelMyPostsController::class, 'edit'])->name('posts.edit');
        Route::patch('bildirimlerim/{post}', [PanelMyPostsController::class, 'update'])->name('posts.update');
        Route::get('kampanyalarim', [PanelMyCampaignsController::class, 'index'])->name('campaigns.index');
        Route::get('kampanyalarim/{campaign:id}/duzenle', [PanelMyCampaignsController::class, 'edit'])->name('campaigns.edit');
        Route::patch('kampanyalarim/{campaign:id}', [PanelMyCampaignsController::class, 'update'])->name('campaigns.update');
        Route::get('profil', [PanelProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profil', [PanelProfileController::class, 'update'])->name('profile.update');
    });

Route::middleware(['auth', 'verified.phone', 'role:admin,super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('dashboard', AdminDashboardController::class)->name('dashboard');

        Route::middleware('role:super_admin')->group(function (): void {
            Route::get('moderasyon', [PostModerationController::class, 'index'])->name('moderation.index');
            Route::post('moderasyon/{post}/onayla', [PostModerationController::class, 'approve'])->name('moderation.approve');
            Route::post('moderasyon/{post}/reddet', [PostModerationController::class, 'reject'])->name('moderation.reject');
            Route::post('moderasyon/{post}/yayindan-kaldir', [PostModerationController::class, 'unpublish'])->name('moderation.unpublish');

            Route::get('kampanya-moderasyon', [CampaignModerationController::class, 'index'])->name('campaign-moderation.index');
            Route::post('kampanya-moderasyon/{campaign:id}/onayla', [CampaignModerationController::class, 'approve'])->name('campaign-moderation.approve');
            Route::post('kampanya-moderasyon/{campaign:id}/reddet', [CampaignModerationController::class, 'reject'])->name('campaign-moderation.reject');
            Route::post('kampanya-moderasyon/{campaign:id}/yayindan-kaldir', [CampaignModerationController::class, 'unpublish'])->name('campaign-moderation.unpublish');

            Route::get('kampanyalar', [CampaignAdminController::class, 'index'])->name('campaigns.registry');
            Route::get('kampanyalar/olustur', [CampaignAdminController::class, 'create'])->name('campaigns.create');
            Route::post('kampanyalar', [CampaignAdminController::class, 'store'])->name('campaigns.store');
            Route::get('kampanyalar/{campaign:id}/duzenle', [CampaignAdminController::class, 'edit'])->name('campaigns.edit');
            Route::patch('kampanyalar/{campaign:id}', [CampaignAdminController::class, 'update'])->name('campaigns.update');
            Route::delete('kampanyalar/toplu', [CampaignAdminController::class, 'bulkDestroy'])->name('campaigns.bulk-destroy');

            Route::get('reklamlar', [AdPlacementAdminController::class, 'index'])->name('ads.index');
            Route::get('reklamlar/{ad}/duzenle', [AdPlacementAdminController::class, 'edit'])->name('ads.edit');
            Route::patch('reklamlar/{ad}', [AdPlacementAdminController::class, 'update'])->name('ads.update');

            Route::get('kullanicilar', UserAdminController::class)->name('users.index');
            Route::get('kullanicilar/{user}/duzenle', [UserAdminEditController::class, 'edit'])->name('users.edit');
            Route::patch('kullanicilar/{user}', [UserAdminEditController::class, 'update'])->name('users.update');
            Route::post('kullanicilar/{user}/sifre-sifirla', [UserAdminEditController::class, 'sendPasswordReset'])->name('users.send-password-reset');
            Route::get('iletisim-mesajlari', [ContactMessageController::class, 'index'])->name('contact-messages.index');
            Route::get('iletisim-mesajlari/{contactMessage}', [ContactMessageController::class, 'show'])->name('contact-messages.show');
            Route::post('iletisim-mesajlari/{contactMessage}/okunmadi', [ContactMessageController::class, 'markUnread'])->name('contact-messages.mark-unread');
            Route::delete('iletisim-mesajlari/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');

            Route::get('kurumlar', [InstitutionAdminController::class, 'index'])->name('institutions.index');
            Route::get('kurumlar/{institution}/duzenle', [InstitutionAdminController::class, 'edit'])->name('institutions.edit');
            Route::patch('kurumlar/{institution}', [InstitutionAdminController::class, 'update'])->name('institutions.update');
            Route::post('kurumlar/{institution}/sifre-sifirla', [InstitutionAdminController::class, 'sendAccountPasswordReset'])->name('institutions.send-password-reset');

            Route::get('eposta', [MailSettingsController::class, 'edit'])->name('mail-settings.edit');
            Route::patch('eposta', [MailSettingsController::class, 'update'])->name('mail-settings.update');

            Route::get('platform', [PlatformSettingController::class, 'edit'])->name('platform-settings.edit');
            Route::patch('platform', [PlatformSettingController::class, 'update'])->name('platform-settings.update');

            Route::get('site-entegrasyonlari', [SiteIntegrationController::class, 'edit'])->name('site-integrations.edit');
            Route::patch('site-entegrasyonlari', [SiteIntegrationController::class, 'update'])->name('site-integrations.update');
            Route::post('site-entegrasyonlari/indexnow-yenile', [SiteIntegrationController::class, 'regenerateIndexNowKey'])->name('site-integrations.regenerate-indexnow');

            Route::get('anasayfa', [HomepageSettingsController::class, 'edit'])->name('homepage-settings.edit');
            Route::patch('anasayfa', [HomepageSettingsController::class, 'update'])->name('homepage-settings.update');

            Route::get('yasal-sayfalar', [LegalPagesController::class, 'edit'])->name('legal-pages.edit');
            Route::patch('yasal-sayfalar', [LegalPagesController::class, 'update'])->name('legal-pages.update');
        });

        Route::prefix('blog-yonetim')->name('blog.')->group(function (): void {
            Route::get('/', [BlogPostController::class, 'index'])->name('index');
            Route::get('yeni', [BlogPostController::class, 'create'])->name('create');
            Route::post('/', [BlogPostController::class, 'store'])->name('store')->middleware('throttle:create-content');
            Route::get('{blog}/duzenle', [BlogPostController::class, 'edit'])->name('edit');
            Route::patch('{blog}', [BlogPostController::class, 'update'])->name('update')->middleware('throttle:create-content');
            Route::delete('{blog}', [BlogPostController::class, 'destroy'])->name('destroy')->middleware('throttle:create-content');
        });
    });

Route::middleware(['auth', 'verified.phone', 'role:institution'])
    ->prefix('institution')
    ->name('institution.')
    ->group(function (): void {
        Route::get('dashboard', InstitutionDashboardController::class)->name('dashboard');
    });

Route::get('{indexnowKey}.txt', IndexNowKeyController::class)
    ->where('indexnowKey', '[a-f0-9]{32}')
    ->name('indexnow.key');
