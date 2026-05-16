<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Support;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostSupportWebController extends Controller
{
    public function __invoke(Request $request, Post $post): RedirectResponse
    {
        if (! $post->isPubliclyApproved()) {
            abort(404);
        }

        $existing = Support::query()
            ->where('user_id', $request->user()->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return back()->with('status', __('Desteğini geri çektin.'));
        }

        Support::query()->create([
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
        ]);

        return back()->with('status', __('Destek verdin, teşekkürler!'));
    }
}
