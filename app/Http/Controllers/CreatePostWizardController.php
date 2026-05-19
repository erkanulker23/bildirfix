<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Institution;
use App\Support\ComplaintDraftSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CreatePostWizardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $draft = ComplaintDraftSession::get($request) ?? [];

        $cityId = $request->integer('city_id')
            ?: (isset($draft['city_id']) ? (int) $draft['city_id'] : null)
            ?: City::query()->where('plate', 34)->value('id');

        $cities = City::query()->orderBy('plate')->get(['id', 'name', 'plate']);

        $selectedInstitutionIds = collect(old('institution_ids', $draft['institution_ids'] ?? []))
            ->filter()
            ->map(static fn ($id) => (int) $id)
            ->unique()
            ->values();

        $legacySingle = old('institution_id', $draft['institution_id'] ?? null);
        if (($legacySingle !== null && $legacySingle !== '') && $selectedInstitutionIds->isEmpty()) {
            $selectedInstitutionIds = collect([(int) $legacySingle]);
        }

        $selectedInstitutions = $selectedInstitutionIds->isEmpty()
            ? collect()
            : Institution::query()->whereIn('id', $selectedInstitutionIds)->orderBy('name')->get(['id', 'name', 'logo_url']);

        $suggestedInstitutions = Institution::query()
            ->when($cityId, fn ($q) => $q->where(function ($sq) use ($cityId): void {
                $sq->where('city_id', $cityId)->orWhereNull('city_id');
            }))
            ->where('type', 'municipality')
            ->orderBy('name')
            ->limit(16)
            ->get(['id', 'name', 'logo_url']);

        return view('pages.create-post', [
            'selectedInstitutions' => $selectedInstitutions,
            'suggestedInstitutions' => $suggestedInstitutions,
            'cityId' => $cityId,
            'cities' => $cities,
            'complaintDraft' => $draft,
            'minimalChrome' => false,
            'hidePageHero' => true,
        ]);
    }
}
