<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PostModerationStatus;
use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Post::query()
            ->publicApproved()
            ->with(['user:id,name', 'category:id,name', 'city:id,name', 'district:id,name', 'institution:id,name']);

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->integer('city_id'));
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->integer('district_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $posts = $query->latest()->paginate(perPage: $request->integer('per_page', 15));

        return response()->json($posts);
    }

    public function show(Request $request, Post $post): JsonResponse
    {
        if (! $post->isVisibleTo($request->user())) {
            abort(404);
        }

        $post->load(['user:id,name', 'category:id,name', 'city:id,name', 'district:id,name', 'neighborhood:id,name', 'institution:id,name', 'moderatedBy:id,name']);

        return response()->json(['data' => $post]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:8000'],
            'media_url' => ['nullable', 'string', 'max:2048'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'neighborhood_id' => ['nullable', 'integer', 'exists:neighborhoods,id'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'institution_id' => ['nullable', 'integer', 'exists:institutions,id'],
        ]);

        $post = Post::query()->create([
            ...$data,
            'user_id' => $request->user()->id,
            'type' => 'complaint',
            'status' => PostStatus::Open,
            'moderation_status' => PostModerationStatus::Pending,
            'moderated_at' => null,
            'moderated_by_user_id' => null,
            'moderation_note' => null,
        ]);

        $post->load(['user:id,name', 'category:id,name', 'city:id,name', 'district:id,name']);

        return response()->json([
            'message' => __('Şikâyet gönderildi. Süper yönetici onayından sonra herkese açılacak.'),
            'data' => $post,
        ], 201);
    }
}
