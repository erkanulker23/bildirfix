<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Post;
use App\Support\TurkishRegions;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class CityExploreController extends Controller
{
    public function __invoke(): View
    {
        $cities = City::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'plate']);

        $counts = Post::query()
            ->publicApproved()
            ->selectRaw('city_id, count(*) as c')
            ->groupBy('city_id')
            ->pluck('c', 'city_id');

        $citiesPayload = $cities->map(static function (City $city) use ($counts): array {
            $plate = $city->plate !== null ? (int) $city->plate : null;
            $region = TurkishRegions::keyForPlate($plate);

            return [
                'id' => $city->id,
                'name' => $city->name,
                'slug' => $city->slug,
                'plate' => $plate,
                'region' => $region,
                'count' => (int) ($counts[$city->id] ?? 0),
                'url' => route('cities.show', $city),
            ];
        })->values()->all();

        $regionLabels = [];
        foreach (TurkishRegions::ORDER as $key) {
            $regionLabels[$key] = TurkishRegions::label($key);
        }

        return view('cities.explore', [
            'citiesJson' => $citiesPayload,
            'regionOrder' => TurkishRegions::ORDER,
            'regionLabels' => $regionLabels,
            'seo' => [
                'description' => Str::limit(
                    __('İlini seç; yalnızca o ile kayıtlı, moderasyonu tamamlanmış şikâyet yayınlarını incele. İlleri bölgelere veya alfabe ile düzenle.'),
                    320
                ),
                'canonical' => route('cities.explore', absolute: true),
                'og_title' => __('Şehrini keşfet').' • '.config('app.name'),
                'og_type' => 'website',
            ],
            'structuredData' => [],
        ]);
    }
}
