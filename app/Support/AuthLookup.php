<?php

namespace App\Support;

use App\Models\User;

final class AuthLookup
{
    /** Normalized key for throttling (email lowercased or E.164 phone). */
    public static function credentialKey(string $credential): string
    {
        $trimmed = trim($credential);

        if ($trimmed === '') {
            return '';
        }

        if (str_contains($trimmed, '@')) {
            return mb_strtolower($trimmed, 'UTF-8');
        }

        return Phone::normalize($trimmed);
    }

    public static function userForCredential(string $credential): ?User
    {
        $trimmed = trim($credential);

        if ($trimmed === '') {
            return null;
        }

        if (str_contains($trimmed, '@')) {
            return User::query()->whereRaw('LOWER(email) = ?', [mb_strtolower($trimmed, 'UTF-8')])->first();
        }

        return User::query()->where('phone', Phone::normalize($trimmed))->first();
    }
}
