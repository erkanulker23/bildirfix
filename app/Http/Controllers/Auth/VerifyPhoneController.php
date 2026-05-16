<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\OtpService;
use App\Support\ComplaintDraftSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VerifyPhoneController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()?->hasVerifiedPhone()) {
            return redirect()->route('panel.dashboard');
        }

        if ($request->user() && ! $request->session()->get('otp_phone')) {
            $request->session()->put('otp_phone', $request->user()->phone);
        }

        if (! $request->session()->get('otp_phone')) {
            return redirect()->route('register')->withErrors([
                'phone' => __('Önce kayıt işlemini tamamlayın.'),
            ]);
        }

        return view('auth.verify-phone', [
            'phone' => (string) $request->session()->get('otp_phone'),
        ]);
    }

    public function store(Request $request, OtpService $otpService): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'size:6'],
            'phone' => ['sometimes', 'string'],
        ]);

        $phone = (string) ($data['phone'] ?? $request->session()->get('otp_phone'));

        $user = $otpService->verifyAndFinalizeUser(null, $phone, $data['code']);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget('otp_phone');

        $post = ComplaintDraftSession::createPostIfAny($request, $user);
        if ($post !== null) {
            return redirect()->route('home')->with(
                'status',
                __('Hoş geldin. Şikâyetin kaydedildi (#:id); onay sonrası herkese açılacak.', ['id' => $post->id]),
            );
        }

        return redirect()->intended(route('panel.dashboard'))
            ->with('status', __('Telefon doğrulandı, hoş geldiniz.'));
    }

    public function resend(Request $request, OtpService $otpService): RedirectResponse
    {
        $phone = (string) $request->session()->get('otp_phone', '');

        if ($phone === '') {
            return redirect()
                ->route('register')
                ->withErrors(['phone' => __('Önce kayıt işlemini tamamlayın.')]);
        }

        $user = User::query()->where('phone', $phone)->first();

        if (! $user) {
            return redirect()
                ->route('register')
                ->withErrors(['phone' => __('Kullanıcı bulunamadı.')]);
        }

        $otpService->issue($user, $phone);

        return back()->with('status', __('Yeni doğrulama kodu üretildi (yerelde otp.log).'));
    }
}
