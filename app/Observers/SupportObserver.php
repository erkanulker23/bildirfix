<?php

namespace App\Observers;

use App\Events\SupportCountChanged;
use App\Models\Support;

class SupportObserver
{
    public function created(Support $support): void
    {
        $post = $support->post;
        $post->increment('support_count');

        event(new SupportCountChanged($post->id, (int) $post->fresh()->support_count));
    }

    public function deleted(Support $support): void
    {
        $post = $support->post;

        if ($post->support_count > 0) {
            $post->decrement('support_count');
        }

        event(new SupportCountChanged($post->id, max(0, (int) $post->fresh()->support_count)));
    }
}
