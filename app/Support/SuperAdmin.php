<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;

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
}
