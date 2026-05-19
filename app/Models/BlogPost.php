<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PostModerationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'moderation_status' => 'approved',
    ];

    /** @var list<string> */
    protected $fillable = [
        'author_user_id',
        'blog_category_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'hero_image_url',
        'meta_title',
        'meta_description',
        'is_published',
        'published_at',
        'moderation_status',
        'moderated_at',
        'moderated_by_user_id',
        'moderation_note',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'moderation_status' => PostModerationStatus::class,
            'moderated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (BlogPost $post): void {
            $slug = trim((string) $post->slug);
            if ($slug === '') {
                $post->slug = static::uniqueSlugFromTitle((string) $post->title);
            }
        });
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeVisibleOnPublicSite($query)
    {
        return $query->published()
            ->where('moderation_status', PostModerationStatus::Approved);
    }

    public function isVisibleOnPublicSite(): bool
    {
        if ($this->moderation_status !== PostModerationStatus::Approved) {
            return false;
        }

        if (! $this->is_published || $this->published_at === null) {
            return false;
        }

        return $this->published_at->lte(now());
    }

    public static function uniqueSlugFromTitle(string $title): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'yazi';
        }

        $slug = $base;
        $i = 1;
        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    /** @return BelongsTo<BlogCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    /** @return BelongsTo<User, $this> */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }

    public function renderedBody(): string
    {
        $body = trim((string) $this->body);
        if ($body === '') {
            return '';
        }

        if (preg_match('/<[a-z][\s\S]*>/i', $body) === 1) {
            return strip_tags($body, [
                'p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li',
                'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a', 'img', 'blockquote',
                'code', 'pre', 'span', 'div', 'figure', 'figcaption',
            ]);
        }

        return Str::markdown($body);
    }

    /** @return BelongsTo<User, $this> */
    public function moderatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by_user_id');
    }
}
