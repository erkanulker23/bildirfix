<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\City;
use App\Models\District;
use App\Models\Institution;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BackfillInstitutionLogosCommand extends Command
{
    protected $signature = 'institutions:backfill-logos';

    protected $description = 'public/images/institutions içindeki PNG dosyalarını kurum logo_url alanına yazar.';

    public function handle(): int
    {
        $data = require database_path('data/turkiye_institutions.php');
        $logoDir = public_path('images/institutions');
        $updated = 0;

        $rows = [];
        $buyuksehir = array_flip($data['buyuksehir_plates'] ?? []);
        $citiesByPlate = City::query()->pluck('name', 'plate');

        foreach ($citiesByPlate as $plate => $cityName) {
            $isBuyuksehir = isset($buyuksehir[(int) $plate]);
            $name = $isBuyuksehir
                ? $cityName.' Büyükşehir Belediyesi'
                : $cityName.' Belediyesi';
            $slug = 'belediye-'.Str::slug($cityName.'-'.$plate);
            $rows[] = ['name' => $name, 'slug' => $slug];
        }

        foreach (['electricity', 'natural_gas', 'water', 'government', 'telecom', 'transport'] as $key) {
            foreach ($data[$key] ?? [] as $row) {
                if (! empty($row['name']) && ! empty($row['slug'])) {
                    $rows[] = ['name' => $row['name'], 'slug' => $row['slug']];
                }
            }
        }

        foreach ($rows as $row) {
            $updated += $this->applyLogoFile($row['name'], $row['slug'], $logoDir);
        }

        $buyuksehir = array_flip($data['buyuksehir_plates'] ?? []);
        $seen = [];

        foreach (District::query()->whereNotNull('turkiye_id')->with('city:id,name,plate')->get(['name', 'slug', 'turkiye_id', 'city_id']) as $district) {
            $city = $district->city;
            if ($city === null || $city->plate === null) {
                continue;
            }

            $plate = (int) $city->plate;
            $districtName = trim((string) $district->name);
            $districtKey = mb_strtolower($districtName, 'UTF-8');
            $dedupeKey = $plate.'|'.$districtKey;
            if ($districtName === '' || isset($seen[$dedupeKey])) {
                continue;
            }
            $seen[$dedupeKey] = true;

            $isBuyuksehir = isset($buyuksehir[$plate]);
            if (! $isBuyuksehir && ($districtKey === 'merkez' || $districtKey === mb_strtolower(trim((string) $city->name), 'UTF-8'))) {
                continue;
            }

            $slug = 'belediye-ilce-'.Str::slug($district->slug ?: ($districtName.'-'.$plate.'-'.$district->turkiye_id));
            $updated += $this->applyLogoFile($districtName.' Belediyesi', $slug, $logoDir);
        }

        $this->info("Güncellenen kurum kaydı: {$updated}");

        return self::SUCCESS;
    }

    private function applyLogoFile(string $institutionName, string $slug, string $logoDir): int
    {
        $path = $logoDir.DIRECTORY_SEPARATOR.$slug.'.png';
        if (! File::exists($path) || File::size($path) <= 200) {
            return 0;
        }

        $publicPath = '/images/institutions/'.$slug.'.png';

        return Institution::query()
            ->where('name', $institutionName)
            ->update(['logo_url' => $publicPath]);
    }
}
