<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfilePageController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = auth()->user();
        abort_if($user === null, 403);

        if ($user->canAccessAdminPanel()) {
            return redirect()->route('admin.dashboard');
        }

        $posts = Post::query()
            ->where('user_id', $user->id)
            ->with(['city:id,name', 'category:id,name'])
            ->latest()
            ->paginate(perPage: 12);

        return view('pages.profile', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }
}
