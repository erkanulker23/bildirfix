<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\City;
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
            $path = $logoDir.DIRECTORY_SEPARATOR.$row['slug'].'.png';
            if (! File::exists($path) || File::size($path) <= 200) {
                continue;
            }

            $publicPath = '/images/institutions/'.$row['slug'].'.png';
            $count = Institution::query()
                ->where('name', $row['name'])
                ->update(['logo_url' => $publicPath]);

            if ($count > 0) {
                $updated += $count;
            }
        }

        $this->info("Güncellenen kurum kaydı: {$updated}");

        return self::SUCCESS;
    }
}
