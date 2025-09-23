<?php

namespace App\Console\Commands;

use App\Jobs\ExecuteScheduledOrderByAdmin;
use App\Models\ScheduledOrderByAdmin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunDueScheduledOrderByAdmin extends Command
{
    protected $signature = 'app:run-due-scheduled-order-by-admin {--limit=200 : Max rows to process}';

    protected $description = 'Claim and dispatch due scheduled order-by-admin rows (schedule_at <= now UTC).';

    public function handle()
    {
        $limit = (int) $this->option('limit');
        $nowUtc = now('UTC');
        $this->info("Scanning scheduled order-by-admin at {$nowUtc->toDateTimeString()} UTC...");

        $candidates = ScheduledOrderByAdmin::query()
            ->where('status', 'scheduled')
            ->where('schedule_at', '<=', $nowUtc)
            ->orderBy('schedule_at')
            ->limit($limit)
            ->pluck('id');

        $claimedIds = [];
        foreach ($candidates as $id) {
            $claimed = DB::transaction(function () use ($id, $nowUtc) {
                $row = ScheduledOrderByAdmin::where('id', $id)->lockForUpdate()->first();
                if (!$row) { return false; }
                if ($row->status !== 'scheduled') { return false; }
                if ($row->schedule_at->gt($nowUtc)) { return false; }
                $row->update(['status' => 'processing', 'started_at' => now()]);
                return true;
            });
            if ($claimed) {
                $claimedIds[] = $id;
                dispatch((new ExecuteScheduledOrderByAdmin($id))->onQueue('scheduled'));
            }
        }

        $this->info('Claimed: ' . count($claimedIds) . ' row(s). IDs: ' . (empty($claimedIds) ? '-' : implode(',', $claimedIds)));
        return self::SUCCESS;
    }
}
