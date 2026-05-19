<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'topic',
        'message',
        'ip_address',
        'user_agent',
        'read_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function topicLabel(): string
    {
        $value = trim((string) ($this->topic ?? ''));

        foreach (config('contact.form_topics', []) as $row) {
            if ((string) data_get($row, 'value') === $value) {
                return (string) __(data_get($row, 'label', $value));
            }
        }

        return $value !== '' ? $value : (string) __('Genel bildirim');
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }
}
