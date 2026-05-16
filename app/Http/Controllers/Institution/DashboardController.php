<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('institution.dashboard', [
            'user' => auth()->user(),
            'institution' => auth()->user()?->managedInstitution,
        ]);
    }
}
