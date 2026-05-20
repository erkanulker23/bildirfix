<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ExternalImportSourceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExternalImportSource extends Model
{
    protected $fillable = [
        'name',
        'type',
        'source_url',
        'source_slug',
        'institution_id',
        'enabled',
        'auto_sync',
        'max_pages',
        'fetch_media',
        'default_moderation',
        'last_synced_at',
        'last_imported_count',
        'last_sync_error',
    ];

    protected function casts(): array
    {
        return [
            'type' => ExternalImportSourceType::class,
            'enabled' => 'boolean',
            'auto_sync' => 'boolean',
            'max_pages' => 'integer',
            'fetch_media' => 'boolean',
            'last_synced_at' => 'datetime',
            'last_imported_count' => 'integer',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /** @return HasMany<Post, $this> */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'external_import_source_id');
    }

    public function isInstitutionScoped(): bool
    {
        return $this->type === ExternalImportSourceType::SikayetvarInstitution;
    }
}
