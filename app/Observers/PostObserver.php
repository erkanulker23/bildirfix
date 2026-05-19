<?php

namespace App\Observers;

use App\Enums\PostModerationStatus;
use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\PostStatusLog;
use App\Support\IndexNowSubmitter;

class PostObserver
{
    public function created(Post $post): void
    {
        PostStatusLog::query()->create([
            'post_id' => $post->id,
            'actor_user_id' => auth()->id(),
            'status' => $post->status instanceof PostStatus ? $post->status->value : (string) $post->status,
            'note' => 'created',
        ]);

        if ($post->moderation_status === PostModerationStatus::Approved) {
            event(new \App\Events\PostPublished($post));
            app(IndexNowSubmitter::class)->submitUrl(route('posts.show', $post, absolute: true));
        }
    }

    public function updated(Post $post): void
    {
        if ($post->wasChanged('moderation_status') && $post->moderation_status === PostModerationStatus::Approved) {
            event(new \App\Events\PostPublished($post));
            app(IndexNowSubmitter::class)->submitUrl(route('posts.show', $post, absolute: true));
        }

        if ($post->wasChanged('status')) {
            PostStatusLog::query()->create([
                'post_id' => $post->id,
                'actor_user_id' => auth()->id(),
                'status' => $post->status instanceof PostStatus ? $post->status->value : (string) $post->status,
                'note' => null,
            ]);
        }
    }
}
