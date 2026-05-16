<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationsPageController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $notifications = $user->notifications()->paginate(perPage: 30);

        return view('pages.notifications', [
            'notifications' => $notifications,
        ]);
    }
}
