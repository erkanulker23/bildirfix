<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Support;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function toggle(Request $request, Post $post): JsonResponse
    {
        $existing = Support::query()
            ->where('user_id', $request->user()->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json([
                'supported' => false,
                'support_count' => (int) $post->fresh()->support_count,
            ]);
        }

        Support::query()->create([
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
        ]);

        return response()->json([
            'supported' => true,
            'support_count' => (int) $post->fresh()->support_count,
        ]);
    }
}
