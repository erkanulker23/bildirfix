<?php

namespace App\Events;

use App\Enums\PostModerationStatus;
use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostPublished implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Post $post)
    {
        $this->post->loadMissing(['user:id,name', 'category:id,name', 'city:id,name']);
    }

    public function broadcastWhen(): bool
    {
        return filled($this->post->city_id)
            && $this->post->moderation_status === PostModerationStatus::Approved;
    }

    /**
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('city.'.$this->post->city_id.'.posts'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'post.published';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->post->id,
            'title' => $this->post->title,
            'city_id' => $this->post->city_id,
            'district_id' => $this->post->district_id,
            'support_count' => $this->post->support_count,
            'created_at' => $this->post->created_at?->toIso8601String(),
        ];
    }
}
