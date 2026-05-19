<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CampaignModerationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Campaign extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'description',
        'hero_image_url',
        'city_id',
        'campaign_topic_id',
        'goal_supporters',
        'supporter_count',
        'view_count',
        'moderation_status',
        'moderated_at',
        'moderated_by_user_id',
        'moderation_note',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'moderation_status' => CampaignModerationStatus::class,
            'moderated_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function slugFromTitle(string $title): string
    {
        $base = Str::slug($title, '-', app()->getLocale());

        return $base !== '' ? $base : 'kampanya';
    }

    public static function uniqueSlug(string $base, mixed $ignoreId = null): string
    {
        $slug = $base;
        $n = 0;
        while (true) {
            $q = Campaign::query()->where('slug', $slug);
            if ($ignoreId !== null) {
                $q->where('id', '!=', $ignoreId);
            }
            if (! $q->exists()) {
                return $slug;
            }
            $n++;
            $slug = $base.'-'.$n;
        }
    }

    /**
     * @param  Builder<Campaign>  $query
     * @return Builder<Campaign>
     */
    public function scopePublicApproved($query)
    {
        return $query->where('moderation_status', CampaignModerationStatus::Approved);
    }

    public function isPubliclyApproved(): bool
    {
        return $this->moderation_status === CampaignModerationStatus::Approved;
    }

    public function isVisibleTo(?User $user): bool
    {
        if ($this->isPubliclyApproved()) {
            return true;
        }

        if ($user === null) {
            return false;
        }

        if ((int) $user->id === (int) $this->user_id) {
            return true;
        }

        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(CampaignTopic::class, 'campaign_topic_id');
    }

    public function moderatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by_user_id');
    }

    /** @return HasMany<CampaignSupporter, Campaign> */
    public function supporters(): HasMany
    {
        return $this->hasMany(CampaignSupporter::class);
    }

    /** @return HasMany<CampaignComment, Campaign> */
    public function comments(): HasMany
    {
        return $this->hasMany(CampaignComment::class);
    }
}
