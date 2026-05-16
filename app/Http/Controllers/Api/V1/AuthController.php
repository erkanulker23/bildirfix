<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\OtpService;
use App\Support\AuthLookup;
use App\Support\Phone;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request, OtpService $otpService): JsonResponse
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
                'phone' => [__('Bu telefon numarası zaten kayıtlı.')],
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

        return response()->json([
            'message' => __('Kayıt alındı. OTP doğrulamasına geçebilirsiniz.'),
            'data' => [
                'user_id' => $user->id,
                'phone' => $user->phone,
            ],
        ], 201);
    }

    public function resendOtp(Request $request, OtpService $otpService): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string'],
        ]);

        $phone = Phone::normalize($data['phone']);
        $user = User::query()->where('phone', $phone)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'phone' => [__('Kayıtlı kullanıcı bulunamadı.')],
            ]);
        }

        $otpService->issue($user, $phone);

        return response()->json([
            'message' => __('Yeni doğrulama kodu üretildi (yerelde otp.log).'),
        ]);
    }

    public function verifyOtp(Request $request, OtpService $otpService): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string'],
            'code' => ['required', 'string', 'size:6'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $phone = Phone::normalize($data['phone']);
        $user = $otpService->verifyAndFinalizeUser(null, $phone, $data['code']);
        $deviceName = $data['device_name'] ?? 'mobile';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->userPayload($user),
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'login' => ['required_without:phone', 'string'],
            'phone' => ['required_without:login', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $credential = isset($data['login']) ? (string) $data['login'] : (string) $data['phone'];
        $user = AuthLookup::userForCredential((string) $credential);

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => [__('Giriş bilgileri hatalı.')],
            ]);
        }

        $staffSkipsPhone = $user->isSuperAdmin() || $user->isAdmin();

        if (! $staffSkipsPhone && ! $user->hasVerifiedPhone()) {
            return response()->json([
                'message' => __('Telefon doğrulaması gerekli.'),
                'requires_otp' => true,
                'phone' => $user->phone,
            ], 403);
        }

        $deviceName = $data['device_name'] ?? 'mobile';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->userPayload($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json(['message' => __('Oturum kapatıldı.')]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->userPayload($request->user()),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function userPayload(?User $user): array
    {
        if (! $user) {
            return [];
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role->value,
            'verification_status' => $user->verification_status->value,
            'phone_verified_at' => $user->phone_verified_at?->toIso8601String(),
            'score' => $user->score,
            'trust_score' => $user->trust_score,
        ];
    }
}
