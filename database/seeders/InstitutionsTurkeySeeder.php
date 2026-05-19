<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use App\Models\Institution;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
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

        $districtCount = $this->seedDistrictMunicipalities($buyuksehir, $citiesByPlate, $downloadLogos, $logoDir);
        $this->command?->info("İlçe belediyeleri: {$districtCount} kayıt işlendi.");

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
     * İlçe belediyeleri (973+ ilçe); logo için tahmini {ilce}.bel.tr alan adı kullanılır.
     *
     * @param  array<int, int>  $buyuksehir
     * @param  Collection<int, int>  $citiesByPlate
     */
    private function seedDistrictMunicipalities(
        array $buyuksehir,
        $citiesByPlate,
        bool $downloadLogos,
        string $logoDir,
    ): int {
        $processed = 0;
        $seen = [];

        $districts = District::query()
            ->whereNotNull('turkiye_id')
            ->with('city:id,name,plate,slug')
            ->orderBy('city_id')
            ->orderBy('name')
            ->get(['id', 'city_id', 'name', 'slug', 'turkiye_id']);

        foreach ($districts as $district) {
            $city = $district->city;
            if ($city === null || $city->plate === null) {
                continue;
            }

            $plate = (int) $city->plate;
            $districtName = trim((string) $district->name);
            if ($districtName === '') {
                continue;
            }

            $districtKey = mb_strtolower($districtName, 'UTF-8');
            $dedupeKey = $plate.'|'.$districtKey;
            if (isset($seen[$dedupeKey])) {
                continue;
            }
            $seen[$dedupeKey] = true;

            $isBuyuksehir = isset($buyuksehir[$plate]);
            $cityNameLower = mb_strtolower(trim((string) $city->name), 'UTF-8');

            if (! $isBuyuksehir && $districtKey === 'merkez') {
                continue;
            }

            if (! $isBuyuksehir && $districtKey === $cityNameLower) {
                continue;
            }

            $institutionName = $districtName.' Belediyesi';
            $slug = 'belediye-ilce-'.Str::slug($district->slug ?: ($districtName.'-'.$plate.'-'.$district->turkiye_id));
            $domainSlug = Str::slug($districtName, '');
            if ($domainSlug === '') {
                continue;
            }

            $domain = $domainSlug.'.bel.tr';

            $this->upsertInstitution([
                'name' => $institutionName,
                'slug' => $slug,
                'type' => 'municipality',
                'plate' => $plate,
                'domain' => $domain,
                'website' => 'https://www.'.$domain,
            ], $citiesByPlate, $downloadLogos, $logoDir);

            $processed++;
        }

        return $processed;
    }

    /**
     * @param  Collection<int, int>  $citiesByPlate
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

        $logoUrl = $this->resolveLogoUrl($row, $downloadLogos, $logoDir);

        $payload = [
            'city_id' => $cityId,
            'type' => $row['type'],
            'verified' => true,
            'website' => $row['website'] ?? null,
        ];

        if ($logoUrl !== null) {
            $payload['logo_url'] = $logoUrl;
        }

        Institution::query()->updateOrCreate(
            ['name' => $row['name']],
            $payload,
        );
    }

    /**
     * @param  array{name: string, slug?: string, domain?: string}  $row
     */
    private function resolveLogoUrl(array $row, bool $downloadLogos, string $logoDir): ?string
    {
        $slug = $row['slug'] ?? null;
        if ($slug === null || $slug === '') {
            return null;
        }

        $filename = $slug.'.png';
        $absolute = $logoDir.DIRECTORY_SEPARATOR.$filename;
        $publicPath = '/'.self::LOGO_DIR.'/'.$filename;

        if (File::exists($absolute) && File::size($absolute) > 200) {
            return $publicPath;
        }

        if ($downloadLogos && ! empty($row['domain'])) {
            return $this->downloadLogo($row['domain'], $slug, $logoDir);
        }

        return null;
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

            usleep(80_000);
        }

        return null;
    }
}
