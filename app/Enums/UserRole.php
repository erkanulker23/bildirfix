<?php

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case VerifiedUser = 'verified_user';
    case Admin = 'admin';
    case SuperAdmin = 'super_admin';
    case Institution = 'institution';

    public function panelHome(): string
    {
        return match ($this) {
            self::SuperAdmin, self::Admin => '/admin/dashboard',
            self::Institution => '/institution/dashboard',
            default => '/panel/dashboard',
        };
    }
}
