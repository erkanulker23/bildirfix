<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use App\Models\Neighborhood;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function cities(): JsonResponse
    {
        $rows = City::query()
            ->orderBy('name')
            ->get(['id', 'plate', 'name', 'slug', 'latitude', 'longitude']);

        return response()->json(['data' => $rows]);
    }

    public function districts(City $city): JsonResponse
    {
        $rows = $city->districts()
            ->orderBy('name')
            ->get(['id', 'city_id', 'name', 'slug']);

        return response()->json(['data' => $rows]);
    }

    public function neighborhoods(District $district): JsonResponse
    {
        $rows = $district->neighborhoods()
            ->orderBy('name')
            ->get(['id', 'district_id', 'name', 'slug']);

        return response()->json(['data' => $rows]);
    }
}
