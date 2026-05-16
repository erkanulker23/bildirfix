<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = [
        'plate',
        'name',
        'slug',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
