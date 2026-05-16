<?php

namespace App\Enums;

enum CampaignModerationStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Unpublished = 'unpublished';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Onay bekliyor',
            self::Approved => 'Yayında',
            self::Rejected => 'Yayına alınmadı',
            self::Unpublished => 'Yayından kaldırıldı',
        };
    }
}
