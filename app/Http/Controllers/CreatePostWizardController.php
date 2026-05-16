<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
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

        $categories = Category::query()->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug']);

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
            : Institution::query()->whereIn('id', $selectedInstitutionIds)->orderBy('name')->get(['id', 'name']);

        return view('pages.create-post', [
            'categories' => $categories,
            'selectedInstitutions' => $selectedInstitutions,
            'cityId' => $cityId,
            'cities' => $cities,
            'complaintDraft' => $draft,
            'minimalChrome' => false,
        ]);
    }
}
