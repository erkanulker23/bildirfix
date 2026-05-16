<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Post $post): JsonResponse
    {
        $comments = $post->comments()
            ->with('user:id,name')
            ->latest()
            ->paginate(perPage: 30);

        return response()->json($comments);
    }

    public function store(Request $request, Post $post): JsonResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:4000'],
        ]);

        $comment = Comment::query()->create([
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
            'content' => $data['content'],
        ]);

        $comment->load('user:id,name');

        return response()->json(['data' => $comment], 201);
    }
}
