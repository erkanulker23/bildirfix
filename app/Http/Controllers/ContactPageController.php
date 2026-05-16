<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\City;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ContactPageController extends Controller
{
    public function show(): View
    {
        $spotlightCampaigns = Campaign::query()
            ->publicApproved()
            ->with(['city', 'user'])
            ->orderByDesc('supporter_count')
            ->limit(9)
            ->get();

        $recentComplaints = Post::query()
            ->where('type', 'complaint')
            ->publicApproved()
            ->with(['city', 'district', 'category'])
            ->latest()
            ->limit(14)
            ->get();

        $counts = [
            'complaints_public' => Post::query()->where('type', 'complaint')->publicApproved()->count(),
            'campaigns_public' => Campaign::query()->publicApproved()->count(),
            'cities_seeded' => City::query()->count(),
        ];

        return view('pages.contact', [
            'spotlightCampaigns' => $spotlightCampaigns,
            'recentComplaints' => $recentComplaints,
            'counts' => $counts,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'topic' => ['nullable', 'string', 'max:80'],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        \Illuminate\Support\Facades\Log::notice('iletisim.form', [
            'name' => $data['name'],
            'email' => $data['email'],
            'topic' => $data['topic'] ?? null,
            'message' => $data['message'],
            'ip' => $request->ip(),
        ]);

        return back()->with('status', __('Mesajınız alındı. Yakında döneceğiz.'));
    }
}
