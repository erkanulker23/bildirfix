<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserAdminController extends Controller
{
    public function __invoke(Request $request): View
    {
        $q = User::query()->orderByDesc('id');

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $like = '%'.$search.'%';
            $q->where(function ($w) use ($like): void {
                $w->where('email', 'like', $like)
                    ->orWhere('name', 'like', $like)
                    ->orWhere('phone', 'like', $like);
            });
        }

        $mail = $request->query('mail', 'all');
        if (! is_string($mail)) {
            $mail = 'all';
        }
        if (! in_array($mail, ['all', 'yes', 'no'], true)) {
            $mail = 'all';
        }
        if ($mail === 'yes') {
            $q->whereNotNull('email_verified_at');
        } elseif ($mail === 'no') {
            $q->whereNull('email_verified_at');
        }

        $users = $q->withCount('posts')->paginate(35)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
            'mailFilter' => $mail,
        ]);
    }
}
