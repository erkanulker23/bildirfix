<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\District;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GeoDistrictsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $cityId = $request->integer('city_id');
        if ($cityId < 1) {
            abort(422);
        }

        $rows = District::query()
            ->where('city_id', $cityId)
            ->whereNotNull('turkiye_id')
            ->orderBy('name')
            ->get(['id', 'name', 'turkiye_id']);

        return response()->json(['data' => $rows]);
    }
}
