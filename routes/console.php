<?php

use App\Jobs\RebuildWardTimeCacheJob;
use Illuminate\Support\Facades\Artisan;

Artisan::command('kepts:ping', function (): int {
    $this->comment('KEPTS is alive.');

    return self::SUCCESS;
})->purpose('Simple internal health check.');

Artisan::command('patients:rebuild-ward-time-cache', function (): int {
    RebuildWardTimeCacheJob::dispatch();

    $this->info('Queued ward time cache rebuild job.');

    return self::SUCCESS;
})->purpose('Rebuild cached ward time breakdowns for all patients.');
