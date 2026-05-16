<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContentReport extends Model
{
    protected $fillable = [
        'reporter_user_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'status',
        'reviewed_by_user_id',
        'admin_note',
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }
}
