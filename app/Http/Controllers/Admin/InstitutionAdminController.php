<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstitutionAdminController extends Controller
{
    public function index(): View
    {
        $institutions = Institution::query()
            ->with(['city:id,name', 'accountUser:id,name,email'])
            ->orderBy('name')
            ->paginate(30);

        return view('admin.institutions.index', compact('institutions'));
    }

    public function edit(Institution $institution): View
    {
        $cities = City::query()->orderBy('name')->get(['id', 'name', 'plate']);

        return view('admin.institutions.edit', [
            'institution' => $institution,
            'cities' => $cities,
        ]);
    }

    public function update(Request $request, Institution $institution): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:120'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'verified' => ['required', 'integer', 'in:0,1'],
            'website' => ['nullable', 'string', 'max:2048'],
            'public_email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $data['verified'] = ((int) $data['verified']) === 1;

        foreach (['website', 'public_email', 'phone', 'address', 'type'] as $key) {
            if (isset($data[$key]) && $data[$key] === '') {
                $data[$key] = null;
            }
        }

        $institution->update($data);

        return redirect()
            ->route('admin.institutions.edit', $institution)
            ->with('status', __('Kurum bilgileri güncellendi.'));
    }
}
