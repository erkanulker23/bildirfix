<?php

namespace App\Enums;

enum PostStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Açık',
            self::InProgress => 'İnceleniyor',
            self::Resolved => 'Çözüldü',
            self::Rejected => 'Reddedildi',
        };
    }
}
