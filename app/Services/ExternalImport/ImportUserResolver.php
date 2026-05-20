<?php

declare(strict_types=1);

namespace App\Services\ExternalImport;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class ImportUserResolver
{
    public function resolve(string $source, ?string $authorName): User
    {
        $displayName = $this->normalizeAuthorName($authorName);
        $importKey = 'author:'.Str::slug($displayName);

        $existing = User::query()
            ->where('external_source', $source)
            ->where('external_import_key', $importKey)
            ->first();

        if ($existing !== null) {
            return $existing;
        }

        $domain = (string) config('external_import.user_email_domain', 'import.local');
        $email = $importKey.'@'.$domain;
        $email = Str::limit($email, 190, '');

        $byEmail = User::query()->whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
        if ($byEmail !== null) {
            $byEmail->forceFill([
                'external_source' => $source,
                'external_import_key' => $importKey,
            ])->save();

            return $byEmail;
        }

        return User::query()->create([
            'name' => $displayName,
            'email' => $email,
            'phone' => null,
            'password' => Hash::make(Str::password(48)),
            'role' => UserRole::User,
            'verification_status' => VerificationStatus::Verified,
            'email_verified_at' => now(),
            'phone_verified_at' => null,
            'external_source' => $source,
            'external_import_key' => $importKey,
        ]);
    }

    private function normalizeAuthorName(?string $authorName): string
    {
        $name = trim((string) $authorName);
        if ($name === '') {
            return __('Şikayetvar kullanıcısı');
        }

        return Str::limit($name, 120, '');
    }
}
