<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\View\View;

class CampaignCreateController extends Controller
{
    public function __invoke(): View
    {
        return view('campaigns.create', [
            'cities' => City::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
