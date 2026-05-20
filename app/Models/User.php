<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Support\SuperAdmin;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'name',
    'avatar_path',
    'email',
    'phone',
    'google_id',
    'external_source',
    'external_import_key',
    'password',
    'role',
    'verification_status',
    'score',
    'trust_score',
    'phone_verified_at',
    'email_verified_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::saving(function (User $user): void {
            if (SuperAdmin::is($user)) {
                $user->role = UserRole::SuperAdmin;

                return;
            }

            if ($user->role === UserRole::SuperAdmin) {
                $user->role = UserRole::Admin;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'verification_status' => VerificationStatus::class,
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function supports(): HasMany
    {
        return $this->hasMany(Support::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function campaignComments(): HasMany
    {
        return $this->hasMany(CampaignComment::class);
    }

    public function managedInstitution(): HasOne
    {
        return $this->hasOne(Institution::class, 'account_user_id');
    }

    public function pushDevices(): HasMany
    {
        return $this->hasMany(PushDevice::class);
    }

    public function isSuperAdmin(): bool
    {
        return \App\Support\SuperAdmin::is($this);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isInstitution(): bool
    {
        return $this->role === UserRole::Institution;
    }

    public function canAccessAdminPanel(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function hasVerifiedPhone(): bool
    {
        if ($this->google_id !== null && $this->email_verified_at !== null) {
            return $this->verification_status === VerificationStatus::Verified;
        }

        return $this->phone_verified_at !== null
            && $this->verification_status === VerificationStatus::Verified;
    }

    public function avatarUrl(): ?string
    {
        $path = trim((string) ($this->avatar_path ?? ''));

        return $path !== '' ? asset('storage/'.$path) : null;
    }

    public function avatarInitials(): string
    {
        $name = (string) $this->name;
        preg_match_all('/\p{L}/u', $name, $lettersMatch);
        $letters = $lettersMatch[0] ?? [];
        $initials = '';
        if (($letters[0] ?? '') !== '') {
            $initials .= mb_strtoupper(mb_substr((string) $letters[0], 0, 1));
        }
        if (($letters[1] ?? '') !== '') {
            $initials .= mb_strtoupper(mb_substr((string) $letters[1], 0, 1));
        }
        if ($initials === '') {
            $initials = mb_strtoupper(mb_substr($name, 0, 2));
        }

        return $initials;
    }
}
