<?php

namespace App\Models;

use App\Enums\PostModerationStatus;
use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'media_url',
        'media',
        'type',
        'city_id',
        'district_id',
        'neighborhood_id',
        'latitude',
        'longitude',
        'category_id',
        'institution_id',
        'status',
        'moderation_status',
        'moderated_at',
        'moderated_by_user_id',
        'moderation_note',
        'support_count',
        'comments_count',
        'reports_count',
        'share_count',
        'follow_count',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'media' => 'array',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'status' => PostStatus::class,
            'moderation_status' => PostModerationStatus::class,
            'moderated_at' => 'datetime',
        ];
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopePublicApproved($query)
    {
        return $query->where('moderation_status', PostModerationStatus::Approved);
    }

    public function isPubliclyApproved(): bool
    {
        return $this->moderation_status === PostModerationStatus::Approved;
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

    public function moderatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by_user_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /** @return BelongsToMany<Institution, $this> */
    public function institutions(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class);
    }

    /**
     * Çoklu hedef kurumları pivot ile kaydeder; geriye dönük uyumluluk için `institution_id` ilk seçeneğe ayarlanır.
     *
     * @param  array<int, mixed>  $institutionIds
     */
    public function syncTargetInstitutions(array $institutionIds): void
    {
        $ids = array_values(array_unique(array_filter(array_map(static fn ($id) => (int) $id, $institutionIds), static fn (int $id) => $id > 0)));
        $this->institutions()->sync($ids);
        $this->forceFill(['institution_id' => $ids[0] ?? null])->saveQuietly();
    }

    public function follows(): HasMany
    {
        return $this->hasMany(PostFollow::class, 'post_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function supports(): HasMany
    {
        return $this->hasMany(Support::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(PostStatusLog::class);
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(ContentReport::class, 'reportable');
    }
}
