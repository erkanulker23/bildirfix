<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\RunExternalImportSourceJob;
use App\Models\ExternalImportSource;
use App\Services\ExternalImport\ExternalComplaintImportService;
use Illuminate\Console\Command;

class RunExternalImportsCommand extends Command
{
    protected $signature = 'bildir:import-external
                            {--source= : Belirli kaynak ID}
                            {--sync : Kuyruk yerine anında çalıştır}';

    protected $description = 'Şikayetvar vb. dış kaynaklardan şikâyet içe aktarır';

    public function handle(ExternalComplaintImportService $service): int
    {
        $sourceId = $this->option('source');
        $query = ExternalImportSource::query()->where('enabled', true);

        if (is_string($sourceId) && $sourceId !== '') {
            $query->whereKey((int) $sourceId);
        } else {
            $query->where('auto_sync', true);
        }

        $sources = $query->get();
        if ($sources->isEmpty()) {
            $this->warn('Çalıştırılacak etkin kaynak yok.');

            return self::SUCCESS;
        }

        foreach ($sources as $source) {
            $this->line("Kaynak #{$source->id}: {$source->name}");

            if ($this->option('sync')) {
                $result = $service->run($source);
                $this->info("  → {$result['imported']} yeni, {$result['skipped']} atlandı");
                if ($result['errors'] !== []) {
                    $this->warn('  Hatalar: '.count($result['errors']));
                }
            } else {
                RunExternalImportSourceJob::dispatch($source->id);
                $this->info('  → kuyruğa alındı');
            }
        }

        return self::SUCCESS;
    }
}
