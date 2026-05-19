<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Models\User;
use App\Support\ComplaintDraftSession;
use App\Support\SuperAdmin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleOAuthController extends Controller
{
    protected function oauthReady(): bool
    {
        return PlatformSetting::current()->googleOAuthConfigured();
    }

    protected function applyGoogleConfig(): void
    {
        $platform = PlatformSetting::current();
        config([
            'services.google' => [
                'client_id' => $platform->google_client_id,
                'client_secret' => $platform->google_client_secret,
                'redirect' => route('auth.google.callback', absolute: true),
            ],
        ]);
    }

    public function redirect(): RedirectResponse
    {
        if (! $this->oauthReady()) {
            return redirect()->route('login')->withErrors([
                'login' => __('Google ile oturum şu an kapalı. Yönetici aktivasyonundan sonra yeniden deneyin.'),
            ]);
        }

        $this->applyGoogleConfig();

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        if (! $this->oauthReady()) {
            return redirect()->route('login')->withErrors([
                'login' => __('Google ile oturum şu an kapalı.'),
            ]);
        }

        $this->applyGoogleConfig();

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable) {
            return redirect()->route('login')->withErrors([
                'login' => __('Google ile giriş tamamlanamadı. Tekrar deneyin.'),
            ]);
        }

        $email = $googleUser->getEmail();
        if (! is_string($email) || trim($email) === '') {
            return redirect()->route('login')->withErrors([
                'login' => __('Google hesabınızdan e‑posta alınamadı.'),
            ]);
        }

        $emailNormalized = strtolower(trim($email));
        $googleId = (string) $googleUser->getId();

        $name = trim((string) ($googleUser->getName() ?: $googleUser->getNickname() ?: Str::before($emailNormalized, '@')));
        if ($name === '') {
            $name = __('Kullanıcı');
        }

        $existingByGoogle = User::query()->where('google_id', $googleId)->first();
        if ($existingByGoogle !== null) {
            Auth::login($existingByGoogle, true);

            $post = ComplaintDraftSession::createPostIfAny($request, $existingByGoogle);
            if ($post !== null) {
                return redirect()->route('home')->with(
                    'status',
                    __('Şikâyet kaydedildi (#:id). Onay sonrası herkese açılacak.', ['id' => $post->id]),
                );
            }

            return redirect()->intended(route('panel.dashboard'));
        }

        $existingByEmail = User::query()->whereRaw('LOWER(email) = ?', [$emailNormalized])->first();
        if ($existingByEmail !== null && $existingByEmail->google_id === null) {
            if (SuperAdmin::is($existingByEmail)) {
                $existingByEmail->google_id = $googleId;
                $existingByEmail->email_verified_at = $existingByEmail->email_verified_at ?? now();
                $existingByEmail->verification_status = VerificationStatus::Verified;
                $existingByEmail->save();

                Auth::login($existingByEmail, true);

                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->route('login')->withErrors([
                'login' => __('Bu e‑posta adresiyle zaten hesabınız var. Şifrenizle giriş yapabilir ya da ilk kayıtta kullandığınız hesabınız kalır.'),
            ]);
        }

        $user = User::query()->create([
            'name' => $name,
            'email' => $emailNormalized,
            'google_id' => $googleId,
            'phone' => null,
            'password' => Hash::make(Str::password(52)),
            'role' => UserRole::User,
            'verification_status' => VerificationStatus::Verified,
            'email_verified_at' => now(),
            'phone_verified_at' => null,
        ]);

        Auth::login($user, true);

        $post = ComplaintDraftSession::createPostIfAny($request, $user);
        if ($post !== null) {
            return redirect()->route('home')->with(
                'status',
                __('Şikâyet kaydedildi (#:id). Onay sonrası herkese açılacak.', ['id' => $post->id]),
            );
        }

        return redirect()->intended(route('panel.dashboard'));
    }
}
