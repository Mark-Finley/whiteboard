<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RebuildWardTimeCacheJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        Patient::query()
            ->with(['movements.fromWard', 'movements.toWard', 'ward'])
            ->chunkById(50, function ($patients): void {
                foreach ($patients as $patient) {
                    $patient->recomputeWardTimeCache();
                }
            });
    }
}
