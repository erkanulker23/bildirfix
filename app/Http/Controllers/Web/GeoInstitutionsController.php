<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GeoInstitutionsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $cityId = $request->integer('city_id');
        $q = trim((string) $request->query('q', ''));

        $query = Institution::query()->orderBy('name');

        if ($q !== '') {
            if (mb_strlen($q) < 2) {
                return response()->json(['data' => []]);
            }

            $escaped = addcslashes($q, '%_\\');
            $query->where('name', 'like', '%'.$escaped.'%');

            if ($cityId > 0) {
                $query->where(function ($sq) use ($cityId): void {
                    $sq->where('city_id', $cityId)->orWhereNull('city_id');
                });
            }

            $rows = $query->limit(50)->get(['id', 'name']);

            return response()->json(['data' => $rows]);
        }

        if ($cityId < 1) {
            abort(422);
        }

        $rows = Institution::query()
            ->where(function ($sq) use ($cityId): void {
                $sq->where('city_id', $cityId)->orWhereNull('city_id');
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['data' => $rows]);
    }
}
