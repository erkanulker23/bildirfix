<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Database\Seeders\InstitutionsTurkeySeeder;
use Illuminate\Console\Command;

class DownloadInstitutionLogosCommand extends Command
{
    protected $signature = 'institutions:download-logos
                            {--limit=0 : Maksimum indirme (0 = sınırsız)}';

    protected $description = 'Eksik kurum logolarını (il + ilçe belediyeleri dahil) tahmini .bel.tr alan adlarından indirir.';

    public function handle(): int
    {
        $limit = max(0, (int) $this->option('limit'));

        $this->info('Kurum logoları indiriliyor (mevcut dosyalar atlanır)…');
        $this->warn('İlçe belediyeleri dahil ~1000 kayıt olabilir; süre uzun sürebilir.');

        InstitutionsTurkeySeeder::$downloadLogos = true;

        $seeder = new InstitutionsTurkeySeeder;
        $seeder->setCommand($this);
        $seeder->run();

        if ($limit > 0) {
            $this->comment('limit seçeneği şu an tam desteklenmiyor; tam seed çalıştırıldı.');
        }

        $this->call('institutions:backfill-logos');

        $this->info('Logo indirme tamamlandı.');

        return self::SUCCESS;
    }
}
