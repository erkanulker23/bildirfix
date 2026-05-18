<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CitySearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $needle = mb_strtolower($q, 'UTF-8');
        $like = '%'.$needle.'%';

        $cities = City::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'plate'])
            ->filter(function (City $city) use ($needle, $like): bool {
                $name = mb_strtolower($city->name, 'UTF-8');
                $slug = mb_strtolower((string) $city->slug, 'UTF-8');
                $plate = $city->plate !== null ? (string) $city->plate : '';

                return str_contains($name, $needle)
                    || str_contains($slug, $needle)
                    || str_contains($plate, $needle);
            })
            ->take(12)
            ->map(fn (City $city): array => [
                'id' => $city->id,
                'name' => $city->name,
                'plate' => $city->plate,
                'label' => $city->plate !== null
                    ? $city->name.' ('.$city->plate.')'
                    : $city->name,
            ])
            ->values();

        return response()->json($cities);
    }
}
