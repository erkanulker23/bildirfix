<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Institution extends Model
{
    protected $fillable = [
        'city_id',
        'account_user_id',
        'name',
        'type',
        'verified',
        'logo_url',
        'website',
        'public_email',
        'phone',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'verified' => 'boolean',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function accountUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_user_id');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    /** Logo veya kurum adına göre üretilmiş yedek görsel (harici SVG). */
    public function displayLogoUrl(): string
    {
        $u = trim((string) ($this->logo_url ?? ''));
        if ($u !== '') {
            if (str_starts_with($u, 'http://') || str_starts_with($u, 'https://')) {
                return $u;
            }

            return url(ltrim($u, '/'));
        }

        return 'https://api.dicebear.com/7.x/shapes/svg?seed='.rawurlencode((string) $this->name)
            .'&backgroundColor=c7d2fe,bbf7d0,a5b4fc,fecaca';
    }
}
