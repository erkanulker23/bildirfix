<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignTopic extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'group_key',
        'sort_order',
    ];

    /** @return HasMany<Campaign, CampaignTopic> */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }
}
