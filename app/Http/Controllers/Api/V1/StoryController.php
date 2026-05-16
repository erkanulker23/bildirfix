<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Story::query()
            ->active()
            ->with('user:id,name');

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->integer('city_id'));
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->integer('district_id'));
        }

        $stories = $query->latest()->paginate(perPage: $request->integer('per_page', 30));

        return response()->json($stories);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'media_url' => ['nullable', 'string', 'max:2048'],
            'description' => ['nullable', 'string', 'max:2000'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $story = Story::query()->create([
            ...$data,
            'user_id' => $request->user()->id,
        ]);

        $story->load('user:id,name');

        return response()->json(['data' => $story], 201);
    }
}
