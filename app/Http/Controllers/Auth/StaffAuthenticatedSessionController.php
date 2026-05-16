<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuthLookup;
use App\Support\ComplaintDraftSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StaffAuthenticatedSessionController extends Controller
{
    public function createBrand(): View
    {
        return view('auth.login-brand');
    }

    public function createSuper(): View
    {
        return view('auth.login-super');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        /** @var User|null $user */
        $user = AuthLookup::userForCredential($credentials['login']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => __('Giriş bilgileri hatalı.'),
            ]);
        }

        $portal = match (true) {
            $request->routeIs('login.brand.store') => 'brand',
            $request->routeIs('login.super.store') => 'super',
            default => (string) $request->input('auth_portal', ''),
        };

        if ($portal === 'brand') {
            if (! $user->isInstitution()) {
                throw ValidationException::withMessages([
                    'login' => __('Bu adres yalnızca doğrulanmış şirket / kurum hesapları içindir.'),
                ]);
            }
            if (! $user->hasVerifiedPhone()) {
                throw ValidationException::withMessages([
                    'login' => __('Kurum hesabında telefon doğrulaması gerekli. Destek için yöneticinize başvurun.'),
                ]);
            }
        } elseif ($portal === 'super') {
            if ($user->isInstitution()) {
                throw ValidationException::withMessages([
                    'login' => __('Kurumsal kullanıcılar için şirket giriş adresini kullanın (/brand).'),
                ]);
            }
            if (! $user->isSuperAdmin() && ! $user->isAdmin()) {
                throw ValidationException::withMessages([
                    'login' => __('Bu adres yönetim (süper admin / dahili yönetici) hesapları içindir.'),
                ]);
            }
        } elseif (! $user->isSuperAdmin() && ! $user->isAdmin() && ! $user->isInstitution()) {
            throw ValidationException::withMessages([
                'login' => __('Bu giriş ekranı yalnızca yetkilendirilmiş kurum ve yönetim hesapları içindir.'),
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $post = ComplaintDraftSession::createPostIfAny($request, $user);
        if ($post !== null) {
            return redirect()->route('home')->with(
                'status',
                __('Şikâyet kaydedildi (#:id). Onay sonrası herkese açılacak.', ['id' => $post->id]),
            );
        }

        return redirect()->intended(match (true) {
            $user->canAccessAdminPanel() => route('admin.dashboard'),
            $user->isInstitution() => route('institution.dashboard'),
            default => route('home'),
        });
    }
}
