<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\OtpService;
use App\Support\Phone;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request, OtpService $otpService): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255'],
            'phone' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $phone = Phone::normalize($data['phone']);

        if (User::query()->where('phone', $phone)->exists()) {
            throw ValidationException::withMessages([
                'phone' => __('Bu telefon numarası zaten kayıtlı.'),
            ]);
        }

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $phone,
            'password' => $data['password'],
            'role' => UserRole::User,
            'verification_status' => VerificationStatus::PendingOtp,
        ]);

        event(new Registered($user));

        $otpService->issue($user, $phone);

        $request->session()->put('otp_phone', $phone);

        return redirect()
            ->route('verify.phone.form')
            ->with('status', __('Doğrulama kodunuz otp.log dosyasına yazıldı (yerel ortam).'));
    }
}
