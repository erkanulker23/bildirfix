<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user !== null && $user->canAccessAdminPanel()) {
            return redirect()->route('admin.dashboard');
        }

        return view('panel.dashboard', [
            'user' => $user,
        ]);
    }
}
