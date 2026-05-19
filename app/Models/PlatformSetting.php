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
        'mail_use_custom_smtp',
        'mail_from_address',
        'mail_from_name',
        'mail_host',
        'mail_port',
        'mail_encryption',
        'mail_username',
        'mail_password',
        'google_site_verification',
        'google_analytics_measurement_id',
        'yandex_verification',
        'bing_site_verification',
        'indexnow_key',
        'custom_head_css',
        'custom_head_html',
        'custom_body_html',
        'homepage_seo_title',
        'homepage_seo_description',
        'site_logo_path',
        'favicon_path',
        'homepage_og_image_path',
        'legal_privacy_html',
        'legal_kvkk_html',
        'legal_terms_html',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'google_oauth_enabled' => 'boolean',
            'google_client_secret' => 'encrypted',
            'mail_use_custom_smtp' => 'boolean',
            'mail_port' => 'integer',
            'mail_password' => 'encrypted',
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

    public function customSmtpConfigured(): bool
    {
        if (! $this->mail_use_custom_smtp) {
            return false;
        }

        $host = is_string($this->mail_host) ? trim($this->mail_host) : '';
        $from = is_string($this->mail_from_address) ? trim($this->mail_from_address) : '';

        return $host !== '' && $from !== '';
    }
}
