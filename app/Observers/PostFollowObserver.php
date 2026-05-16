<?php

namespace App\Observers;

use App\Models\PostFollow;

class PostFollowObserver
{
    public function created(PostFollow $follow): void
    {
        $follow->post->increment('follow_count');
    }

    public function deleted(PostFollow $follow): void
    {
        $post = $follow->post;

        if ($post->follow_count > 0) {
            $post->decrement('follow_count');
        }
    }
}
