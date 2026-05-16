<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\District;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class GeoNeighborhoodsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $districtPk = $request->integer('district_id');
        if ($districtPk < 1) {
            abort(422);
        }

        /** @var District|null $district */
        $district = District::query()->find($districtPk);
        if ($district === null || $district->turkiye_id === null) {
            abort(404);
        }

        $tid = (int) $district->turkiye_id;
        $ttl = (int) config('geo.neighborhood_cache_ttl', 86400);

        /** @var array{data: array<int, array{id: mixed, name: string, districtId: mixed}>, district_id: int, turkiye_district_id: int} $payload */
        $payload = Cache::remember(
            'geo:turkiye:nh:'.$tid,
            max(300, $ttl),
            function () use ($tid, $districtPk): array {
                $data = self::fetchAllNeighborhoodPages($tid);

                return [
                    'data' => $data,
                    'district_id' => $districtPk,
                    'turkiye_district_id' => $tid,
                ];
            }
        );

        return response()->json($payload);
    }

    /**
     * @return array<int, array{id: mixed, name: string, districtId: mixed}>
     */
    private static function fetchAllNeighborhoodPages(int $turkiyeDistrictId): array
    {
        $merged = [];
        $page = 1;

        while ($page <= 50) {
            $resp = Http::timeout(45)
                ->acceptJson()
                ->get('https://api.turkiyeapi.dev/v1/neighborhoods', [
                    'districtId' => $turkiyeDistrictId,
                    'page' => $page,
                    'pageSize' => 500,
                ]);

            if (! $resp->successful()) {
                break;
            }

            /** @var array<string, mixed> */
            $json = $resp->json();
            $chunk = $json['data'] ?? [];

            if (! is_array($chunk) || $chunk === []) {
                break;
            }

            foreach ($chunk as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $merged[] = [
                    'id' => $row['id'] ?? null,
                    'name' => (string) ($row['name'] ?? ''),
                    'districtId' => $row['districtId'] ?? null,
                ];
            }

            if (count($chunk) < 500) {
                break;
            }

            $page++;
        }

        return $merged;
    }
}
