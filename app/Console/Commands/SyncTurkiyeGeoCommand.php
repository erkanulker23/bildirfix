<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\City;
use App\Models\District;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncTurkiyeGeoCommand extends Command
{
    protected $signature = 'turkiye:sync-geo {--url=https://api.turkiyeapi.dev/v1/provinces : API tabanı}';

    protected $description = 'Türkiye iller ve ilçeleri TurkiyeAPI üzerinden senkronize eder (mahalleler istek anında çekilir).';

    public function handle(): int
    {
        $base = rtrim((string) $this->option('url'), '/');
        $this->info('İller ve ilçeler indiriliyor…');

        $response = Http::timeout(120)
            ->acceptJson()
            ->get($base, ['page' => 1, 'pageSize' => 100]);

        if (! $response->successful()) {
            $this->error('API yanıtı alınamadı: HTTP '.$response->status());

            return self::FAILURE;
        }

        $json = $response->json();
        $provinces = $json['data'] ?? [];
        if (! is_array($provinces) || $provinces === []) {
            $this->error('Beklenmeyen JSON yapısı.');

            return self::FAILURE;
        }

        foreach ($provinces as $province) {
            $plate = (int) ($province['id'] ?? 0);
            $name = (string) ($province['name'] ?? '');
            if ($plate < 1 || $name === '') {
                continue;
            }

            $lat = $province['coordinates']['latitude'] ?? null;
            $lng = $province['coordinates']['longitude'] ?? null;

            $city = City::query()->updateOrCreate(
                ['plate' => $plate],
                [
                    'name' => $name,
                    'slug' => Str::slug($name.'-'.$plate),
                    'latitude' => is_numeric($lat) ? round((float) $lat, 7) : null,
                    'longitude' => is_numeric($lng) ? round((float) $lng, 7) : null,
                ],
            );

            $districts = $province['districts'] ?? [];
            if (! is_array($districts)) {
                continue;
            }

            foreach ($districts as $d) {
                $tid = (int) ($d['id'] ?? 0);
                $dname = (string) ($d['name'] ?? '');
                if ($tid < 1 || $dname === '') {
                    continue;
                }

                District::query()->updateOrCreate(
                    ['turkiye_id' => $tid],
                    [
                        'city_id' => $city->id,
                        'name' => $dname,
                        'slug' => Str::slug($plate.'-'.$dname.'-'.$tid),
                    ],
                );
            }
        }

        $this->info('Tamam: '.City::query()->count().' il, '.District::query()->count().' ilçe.');

        return self::SUCCESS;
    }
}
