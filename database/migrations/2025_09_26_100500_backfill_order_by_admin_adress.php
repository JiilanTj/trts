<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('order_by_admin') || !Schema::hasTable('scheduled_order_by_admin')) {
            return;
        }

        $this->backfillFromItems();
        $this->backfillFromSchedules();
    }

    public function down(): void
    {
        // no-op: data backfill cannot be safely reverted
    }

    private function backfillFromItems(): void
    {
        if (!Schema::hasTable('scheduled_order_by_admin_items')) {
            return;
        }

        $rows = DB::table('scheduled_order_by_admin_items as i')
            ->join('scheduled_order_by_admin as s', 'i.scheduled_id', '=', 's.id')
            ->join('order_by_admin as o', 'i.created_order_id', '=', 'o.id')
            ->where(function ($q) {
                $q->whereNull('o.adress')->orWhere('o.adress', '=','');
            })
            ->whereNotNull('s.adress')
            ->where('s.adress', '<>', '')
            ->select('o.id as order_id', 's.adress')
            ->get()
            ->unique('order_id');

        foreach ($rows as $row) {
            DB::table('order_by_admin')
                ->where('id', $row->order_id)
                ->update(['adress' => $row->adress]);
        }
    }

    private function backfillFromSchedules(): void
    {
        $rows = DB::table('scheduled_order_by_admin as s')
            ->join('order_by_admin as o', 's.created_order_id', '=', 'o.id')
            ->where(function ($q) {
                $q->whereNull('o.adress')->orWhere('o.adress', '=','');
            })
            ->whereNotNull('s.adress')
            ->where('s.adress', '<>', '')
            ->select('o.id as order_id', 's.adress')
            ->get()
            ->unique('order_id');

        foreach ($rows as $row) {
            DB::table('order_by_admin')
                ->where('id', $row->order_id)
                ->update(['adress' => $row->adress]);
        }
    }
};
