<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Institution;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class InstitutionsTurkeySeeder extends Seeder
{
    private const LOGO_DIR = 'images/institutions';

    public static bool $downloadLogos = true;

    public function run(): void
    {
        $downloadLogos = self::$downloadLogos;

        $data = require database_path('data/turkiye_institutions.php');
        $citiesByPlate = City::query()->pluck('id', 'plate');

        if ($citiesByPlate->count() < 81) {
            $this->command?->warn('81 il bulunamadı. Önce: php artisan turkiye:sync-geo');
        }

        $logoDir = public_path(self::LOGO_DIR);
        if ($downloadLogos && ! File::isDirectory($logoDir)) {
            File::makeDirectory($logoDir, 0755, true);
        }

        $buyuksehir = array_flip($data['buyuksehir_plates']);
        $municipalityDomains = $data['municipality_domains'];

        foreach ($citiesByPlate as $plate => $cityId) {
            $city = City::query()->find($cityId);
            if (! $city) {
                continue;
            }

            $isBuyuksehir = isset($buyuksehir[(int) $plate]);
            $name = $isBuyuksehir
                ? $city->name.' Büyükşehir Belediyesi'
                : $city->name.' Belediyesi';

            $slug = 'belediye-'.Str::slug($city->name.'-'.$plate);
            $domain = $municipalityDomains[(int) $plate] ?? Str::slug($city->name).'.bel.tr';

            $this->upsertInstitution([
                'name' => $name,
                'slug' => $slug,
                'type' => 'municipality',
                'plate' => (int) $plate,
                'domain' => $domain,
                'website' => 'https://www.'.$domain,
            ], $citiesByPlate, $downloadLogos, $logoDir);
        }

        $groups = [
            'electricity' => 'electricity',
            'natural_gas' => 'natural_gas',
            'water' => 'water',
            'government' => 'government',
            'telecom' => 'telecom',
            'transport' => 'transport',
        ];

        foreach ($groups as $key => $type) {
            foreach ($data[$key] as $row) {
                $row['type'] = $type;
                $this->upsertInstitution($row, $citiesByPlate, $downloadLogos, $logoDir);
            }
        }

        $total = Institution::query()->count();
        $this->command?->info("Türkiye kurumları yüklendi. Toplam: {$total}");
    }

    /**
     * @param  \Illuminate\Support\Collection<int, int>  $citiesByPlate
     * @param  array{name: string, slug: string, type: string, plate?: int|null, domain?: string, website?: string}  $row
     */
    private function upsertInstitution(
        array $row,
        $citiesByPlate,
        bool $downloadLogos,
        string $logoDir,
    ): void {
        $plate = $row['plate'] ?? null;
        $cityId = $plate !== null ? ($citiesByPlate[(int) $plate] ?? null) : null;

        $logoUrl = null;
        if ($downloadLogos && ! empty($row['domain']) && ! empty($row['slug'])) {
            $logoUrl = $this->downloadLogo($row['domain'], $row['slug'], $logoDir);
        }

        Institution::query()->updateOrCreate(
            ['name' => $row['name']],
            [
                'city_id' => $cityId,
                'type' => $row['type'],
                'verified' => true,
                'logo_url' => $logoUrl,
                'website' => $row['website'] ?? null,
            ],
        );
    }

    private function downloadLogo(string $domain, string $slug, string $logoDir): ?string
    {
        $domain = preg_replace('#^https?://#', '', rtrim($domain, '/'));
        $filename = $slug.'.png';
        $absolute = $logoDir.DIRECTORY_SEPARATOR.$filename;
        $publicPath = '/'.self::LOGO_DIR.'/'.$filename;

        if (File::exists($absolute) && File::size($absolute) > 200) {
            return $publicPath;
        }

        $sources = [
            'https://logo.clearbit.com/'.$domain,
            'https://www.google.com/s2/favicons?domain='.$domain.'&sz=128',
            'https://'.$domain.'/favicon.ico',
        ];

        foreach ($sources as $url) {
            try {
                $response = Http::timeout(12)
                    ->withHeaders(['User-Agent' => 'SimdibildirInstitutionSeeder/1.0'])
                    ->get($url);

                if ($response->successful() && strlen($response->body()) > 200) {
                    File::put($absolute, $response->body());

                    return $publicPath;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }
}
