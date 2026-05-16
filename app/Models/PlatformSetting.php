<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $fillable = [
        'google_oauth_enabled',
        'google_client_id',
        'google_client_secret',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'google_oauth_enabled' => 'boolean',
            'google_client_secret' => 'encrypted',
        ];
    }

    /**
     * Platform-wide settings singleton (first row).
     */
    public static function current(): self
    {
        /** @var self $row */
        $row = static::query()->orderBy('id')->firstOrFail();

        return $row;
    }

    public function googleOAuthConfigured(): bool
    {
        if (! $this->google_oauth_enabled) {
            return false;
        }

        return is_string($this->google_client_id) && trim($this->google_client_id) !== ''
            && is_string($this->google_client_secret) && trim($this->google_client_secret) !== '';
    }
}
