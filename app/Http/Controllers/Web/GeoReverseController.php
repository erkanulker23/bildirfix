<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

final class GeoReverseController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $lat = $request->float('lat');
        $lng = $request->float('lng');

        if (! is_finite($lat) || ! is_finite($lng) || abs($lat) > 90 || abs($lng) > 180) {
            abort(422, 'Geçersiz koordinat.');
        }

        $cacheKey = sprintf('geo:reverse:%.5f:%.5f', $lat, $lng);

        $payload = Cache::remember($cacheKey, 3600, fn () => $this->resolve($lat, $lng));

        return response()->json(['data' => $payload]);
    }

    /**
     * @return array<string, mixed>
     */
    private function resolve(float $lat, float $lng): array
    {
        $city = $this->nearestCity($lat, $lng);
        if ($city === null) {
            return [
                'city_id' => null,
                'district_id' => null,
                'neighborhood_turkiye_id' => null,
                'neighborhood_name' => null,
            ];
        }

        $address = $this->nominatimAddress($lat, $lng);
        $district = $this->matchDistrict((int) $city->id, $address);
        $neighborhood = null;

        if ($district !== null) {
            $neighborhood = $this->matchNeighborhood($district, $address);
        }

        return [
            'city_id' => (int) $city->id,
            'city_name' => $city->name,
            'district_id' => $district?->id,
            'district_name' => $district?->name,
            'neighborhood_turkiye_id' => $neighborhood['turkiye_id'] ?? null,
            'neighborhood_name' => $neighborhood['name'] ?? null,
        ];
    }

    private function nearestCity(float $lat, float $lng): ?City
    {
        $best = null;
        $bestKm = PHP_FLOAT_MAX;

        foreach (City::query()->whereNotNull('latitude')->whereNotNull('longitude')->get(['id', 'name', 'latitude', 'longitude']) as $city) {
            $km = $this->distanceKm($lat, $lng, (float) $city->latitude, (float) $city->longitude);
            if ($km < $bestKm) {
                $bestKm = $km;
                $best = $city;
            }
        }

        if ($best !== null && $bestKm <= 120) {
            return $best;
        }

        $address = $this->nominatimAddress($lat, $lng);
        $candidates = array_filter([
            $address['province'] ?? null,
            $address['state'] ?? null,
            $address['city'] ?? null,
        ]);

        foreach ($candidates as $name) {
            $normalized = $this->normalizeTr((string) $name);
            if ($normalized === '') {
                continue;
            }
            $match = City::query()
                ->get(['id', 'name', 'latitude', 'longitude'])
                ->first(fn (City $c) => $this->normalizeTr($c->name) === $normalized
                    || str_contains($this->normalizeTr($c->name), $normalized)
                    || str_contains($normalized, $this->normalizeTr($c->name)));

            if ($match !== null) {
                return $match;
            }
        }

        return $best;
    }

    /**
     * @param  array<string, string|null>  $address
     */
    private function matchDistrict(int $cityId, array $address): ?District
    {
        $candidates = array_filter([
            $address['town'] ?? null,
            $address['county'] ?? null,
            $address['city_district'] ?? null,
            $address['district'] ?? null,
            $address['suburb'] ?? null,
        ]);

        $districts = District::query()
            ->where('city_id', $cityId)
            ->whereNotNull('turkiye_id')
            ->orderBy('name')
            ->get(['id', 'name', 'turkiye_id']);

        foreach ($candidates as $raw) {
            $needle = $this->normalizeTr((string) $raw);
            if ($needle === '') {
                continue;
            }
            foreach ($districts as $district) {
                $hay = $this->normalizeTr($district->name);
                if ($hay === $needle || str_contains($hay, $needle) || str_contains($needle, $hay)) {
                    return $district;
                }
            }
        }

        return $districts->first();
    }

    /**
     * @param  array<string, string|null>  $address
     * @return array{turkiye_id: int, name: string}|null
     */
    private function matchNeighborhood(District $district, array $address): ?array
    {
        if ($district->turkiye_id === null) {
            return null;
        }

        $labels = array_filter([
            $address['neighbourhood'] ?? null,
            $address['suburb'] ?? null,
            $address['quarter'] ?? null,
            $address['residential'] ?? null,
        ]);

        if ($labels === []) {
            return null;
        }

        $rows = $this->fetchNeighborhoods((int) $district->turkiye_id);
        foreach ($labels as $raw) {
            $needle = $this->normalizeTr((string) $raw);
            if ($needle === '') {
                continue;
            }
            foreach ($rows as $row) {
                $hay = $this->normalizeTr((string) ($row['name'] ?? ''));
                if ($hay === $needle || str_contains($hay, $needle) || str_contains($needle, $hay)) {
                    return [
                        'turkiye_id' => (int) $row['id'],
                        'name' => (string) $row['name'],
                    ];
                }
            }
        }

        return null;
    }

    /**
     * @return list<array{id: int|string, name: string}>
     */
    private function fetchNeighborhoods(int $turkiyeDistrictId): array
    {
        $cacheKey = 'geo:turkiye:nh:'.$turkiyeDistrictId;

        /** @var list<array{id: int|string, name: string}> $cached */
        $cached = Cache::remember($cacheKey, (int) config('geo.neighborhood_cache_ttl', 86400), function () use ($turkiyeDistrictId): array {
            $all = [];
            $page = 1;
            do {
                $response = Http::timeout(25)
                    ->acceptJson()
                    ->get('https://api.turkiyeapi.dev/v1/neighborhoods', [
                        'districtId' => $turkiyeDistrictId,
                        'page' => $page,
                        'pageSize' => 200,
                    ]);
                if (! $response->successful()) {
                    break;
                }
                $chunk = $response->json('data') ?? [];
                if (! is_array($chunk) || $chunk === []) {
                    break;
                }
                foreach ($chunk as $item) {
                    if (! is_array($item)) {
                        continue;
                    }
                    $all[] = [
                        'id' => $item['id'] ?? 0,
                        'name' => (string) ($item['name'] ?? ''),
                    ];
                }
                $page++;
            } while (count($chunk) >= 200 && $page <= 30);

            return $all;
        });

        return $cached;
    }

    /**
     * @return array<string, string|null>
     */
    private function nominatimAddress(float $lat, float $lng): array
    {
        $cacheKey = sprintf('geo:nominatim:%.5f:%.5f', $lat, $lng);

        /** @var array<string, string|null> $address */
        $address = Cache::remember($cacheKey, 86400, function () use ($lat, $lng): array {
            try {
                $response = Http::timeout(12)
                    ->withHeaders([
                        'User-Agent' => config('app.name', 'simdibildir').'/1.0 (contact@'.parse_url((string) config('app.url'), PHP_URL_HOST).')',
                    ])
                    ->get('https://nominatim.openstreetmap.org/reverse', [
                        'lat' => $lat,
                        'lon' => $lng,
                        'format' => 'json',
                        'addressdetails' => 1,
                        'accept-language' => 'tr',
                        'zoom' => 16,
                    ]);
                if (! $response->successful()) {
                    return [];
                }
                $raw = $response->json('address');
                if (! is_array($raw)) {
                    return [];
                }
                $out = [];
                foreach ($raw as $key => $value) {
                    if (is_string($value) && $value !== '') {
                        $out[(string) $key] = $value;
                    }
                }

                return $out;
            } catch (\Throwable) {
                return [];
            }
        });

        return $address;
    }

    private function distanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $r = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function normalizeTr(string $value): string
    {
        $v = Str::of($value)->lower()->trim()->toString();
        $v = str_replace(['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ'], ['i', 'g', 'u', 's', 'o', 'c', 'i'], $v);
        $v = preg_replace('/\s+(ilce|ilçe|mahallesi|mah\.?|merkez)$/u', '', $v) ?? $v;

        return trim($v);
    }
}
