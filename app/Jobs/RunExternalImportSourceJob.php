<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\ExternalImportSource;
use App\Services\ExternalImport\ExternalComplaintImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RunExternalImportSourceJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 900;

    public function __construct(public int $sourceId) {}

    public function handle(ExternalComplaintImportService $service): void
    {
        $source = ExternalImportSource::query()->find($this->sourceId);
        if ($source === null || ! $source->enabled) {
            return;
        }

        $service->run($source);
    }
}
