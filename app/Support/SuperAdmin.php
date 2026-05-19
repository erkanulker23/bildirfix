<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class SuperAdmin
{
    public static function email(): string
    {
        return strtolower(trim((string) config('auth.super_admin_email', 'erkanulker0@gmail.com')));
    }

    public static function is(User $user): bool
    {
        $needle = self::email();

        return $needle !== '' && strtolower(trim((string) $user->email)) === $needle;
    }

    public static function canAssignRole(UserRole $role): bool
    {
        return $role !== UserRole::SuperAdmin;
    }

    public static function syncAccount(?string $password = null): User
    {
        $email = self::email();

        if ($email === '') {
            throw new \InvalidArgumentException('SUPER_ADMIN_EMAIL is not configured.');
        }

        $resolvedPassword = $password ?? (string) env('SUPER_ADMIN_PASSWORD', '');

        if ($resolvedPassword === '') {
            throw new \InvalidArgumentException('SUPER_ADMIN_PASSWORD or an explicit password is required.');
        }

        /** @var User $user */
        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Süper Yönetici',
                'phone' => Phone::normalize('5530000099'),
                'password' => Hash::make($resolvedPassword),
                'role' => UserRole::SuperAdmin,
                'verification_status' => VerificationStatus::Verified,
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
            ],
        );

        return $user;
    }
}
