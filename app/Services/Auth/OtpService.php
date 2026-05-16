<?php

namespace App\Services\Auth;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\OtpCode;
use App\Models\User;
use App\Support\Phone;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OtpService
{
    private const TTL_MINUTES = 10;

    public function issue(?User $user, string $rawPhone): void
    {
        $phone = Phone::normalize($rawPhone);

        OtpCode::query()
            ->where('phone', $phone)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->delete();

        $plain = sprintf('%06d', random_int(0, 999_999));

        OtpCode::query()->create([
            'phone' => $phone,
            'code_hash' => Hash::make($plain),
            'expires_at' => Carbon::now()->addMinutes(self::TTL_MINUTES),
            'attempts' => 0,
        ]);

        Log::channel('otp')->debug('OTP issued', ['phone' => $phone, 'code' => $plain]);
    }

    public function verifyAndFinalizeUser(?User $user, string $rawPhone, string $code): User
    {
        $phone = Phone::normalize($rawPhone);

        $record = OtpCode::query()
            ->where('phone', $phone)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $record) {
            throw ValidationException::withMessages([
                'code' => [__('OTP bulunamadı veya süresi dolmuş.')],
            ]);
        }

        if ($record->attempts >= 5) {
            throw ValidationException::withMessages([
                'code' => [__('Çok fazla deneme. Bir süre sonra tekrar deneyin.')],
            ]);
        }

        $record->increment('attempts');

        if (! Hash::check($code, $record->code_hash)) {
            throw ValidationException::withMessages([
                'code' => [__('Kod doğrulanamadı.')],
            ]);
        }

        $record->verified_at = now();
        $record->save();

        $user ??= User::query()->where('phone', $phone)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'phone' => [__('Kullanıcı bulunamadı.')],
            ]);
        }

        $user->forceFill([
            'phone_verified_at' => now(),
            'verification_status' => VerificationStatus::Verified,
        ]);

        if ($user->role === UserRole::User->value || $user->role === UserRole::VerifiedUser->value) {
            $user->role = UserRole::VerifiedUser->value;
        }

        $user->save();

        return $user;
    }
}
