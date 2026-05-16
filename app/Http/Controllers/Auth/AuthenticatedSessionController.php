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

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
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

        $staffSkipsPhone = $user->isSuperAdmin() || $user->isAdmin();

        if (! $staffSkipsPhone && ! $user->hasVerifiedPhone()) {
            $request->session()->put('otp_phone', $user->phone);

            return redirect()
                ->route('verify.phone.form')
                ->with('status', __('Önce telefon doğrulamasını tamamlayın.'));
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
            default => route('panel.dashboard'),
        });
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
