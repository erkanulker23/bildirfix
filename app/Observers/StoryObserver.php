<?php

namespace App\Observers;

use App\Models\Story;

class StoryObserver
{
    public function creating(Story $story): void
    {
        if (! $story->expires_at) {
            $story->expires_at = now()->addHours(24);
        }
    }
}
