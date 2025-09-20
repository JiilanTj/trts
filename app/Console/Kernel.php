<?php

namespace App\Console;

use App\Console\Commands\RunDueScheduledOrderBatches;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Run fallback scheduler every minute
        $schedule->command(RunDueScheduledOrderBatches::class)->everyMinute();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
