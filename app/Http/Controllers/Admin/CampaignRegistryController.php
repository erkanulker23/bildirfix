<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\CampaignModerationStatus;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignRegistryController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filter = $request->query('durum', 'all');
        if (! is_string($filter)) {
            $filter = 'all';
        }

        $allowed = ['all', 'pending', 'approved', 'rejected', 'unpublished'];
        if (! in_array($filter, $allowed, true)) {
            $filter = 'all';
        }

        $q = Campaign::query()
            ->with(['user:id,name', 'city:id,name', 'moderatedBy:id,name'])
            ->orderByDesc('created_at');

        if ($filter !== 'all') {
            $q->where('moderation_status', CampaignModerationStatus::from($filter));
        }

        $campaigns = $q->paginate(25)->withQueryString();

        return view('admin.campaigns.registry', [
            'campaigns' => $campaigns,
            'statusFilter' => $filter,
        ]);
    }
}
