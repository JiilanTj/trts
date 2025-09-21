<?php

namespace App\Console\Commands;

use App\Jobs\ExecuteScheduledOrderBatch;
use App\Models\ScheduledOrderBatch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunDueScheduledOrderBatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-due-scheduled-order-batches {--limit=100 : Max batches to process in one run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find scheduled order batches that are due (schedule_at <= now UTC), claim them, and dispatch execution jobs.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $nowUtc = now('UTC');

        $this->info("Scanning for due batches at {$nowUtc->toDateTimeString()} UTC...");

        $claimed = 0;
        $batchIds = [];

        // Fetch candidates first to keep lock windows small
        $candidates = ScheduledOrderBatch::query()
            ->where('status', 'scheduled')
            ->where('schedule_at', '<=', $nowUtc)
            ->orderBy('schedule_at')
            ->limit($limit)
            ->pluck('id');

        foreach ($candidates as $id) {
            $claimedNow = DB::transaction(function () use ($id, $nowUtc) {
                $batch = ScheduledOrderBatch::where('id', $id)->lockForUpdate()->first();
                if (!$batch) { return false; }
                if ($batch->status !== 'scheduled') { return false; }
                if ($batch->schedule_at->gt($nowUtc)) { return false; }
                $batch->update([
                    'status' => 'processing',
                    'started_at' => now(),
                ]);
                return true;
            });

            if ($claimedNow) {
                $claimed++;
                $batchIds[] = $id;
                // Dispatch to dedicated queue
                dispatch((new ExecuteScheduledOrderBatch($id))->onQueue('scheduled'));
            }
        }

        $this->info("Claimed {$claimed} batch(es). Dispatched jobs for IDs: " . (empty($batchIds) ? '-' : implode(',', $batchIds)));

        return self::SUCCESS;
    }
}
