<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Database\Seeders\InstitutionsTurkeySeeder;
use Illuminate\Console\Command;

class SeedTurkiyeInstitutionsCommand extends Command
{
    protected $signature = 'institutions:seed-turkey
                            {--no-logos : Logo indirmeyi atla}
                            {--sync-geo : Önce illeri TurkiyeAPI ile senkronize et}';

    protected $description = 'Türkiye belediye, EDAŞ, doğalgaz, su ve kamu kurumlarını logolarıyla birlikte yükler.';

    public function handle(): int
    {
        if ($this->option('sync-geo')) {
            $this->call('turkiye:sync-geo');
        }

        InstitutionsTurkeySeeder::$downloadLogos = ! $this->option('no-logos');

        $this->info('Kurumlar yükleniyor (belediyeler, enerji, kamu)…');

        $seeder = new InstitutionsTurkeySeeder;
        $seeder->setCommand($this);
        $seeder->run();

        $this->newLine();
        $this->info('Tamamlandı. Kurumlar veritabanında kalıcıdır; tekrar çalıştırmak günceller (updateOrCreate).');

        return self::SUCCESS;
    }
}
