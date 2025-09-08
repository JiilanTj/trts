<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) { return; }
        $connection = config('database.default');
        $driver = config("database.connections.$connection.driver");

        // Expand enum to include new values for MySQL. For other drivers we skip (SQLite stores as TEXT already)
        if ($driver === 'mysql') {
            // payment_method: add 'balance'
            try {
                DB::statement("ALTER TABLE `orders` MODIFY `payment_method` ENUM('manual_transfer','balance') NOT NULL DEFAULT 'manual_transfer'");
            } catch (Throwable $e) {
                // silently ignore if already altered
            }
            // payment_status: add 'refunded'
            try {
                DB::statement("ALTER TABLE `orders` MODIFY `payment_status` ENUM('unpaid','waiting_confirmation','paid','rejected','refunded') NOT NULL DEFAULT 'unpaid'");
            } catch (Throwable $e) {
                // ignore
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders')) { return; }
        $connection = config('database.default');
        $driver = config("database.connections.$connection.driver");
        if ($driver === 'mysql') {
            // Revert to original enums (without balance & refunded). Data using removed values should be adjusted before rollback.
            try {
                // Replace any 'balance' method with 'manual_transfer' before shrinking enum to avoid SQL error
                DB::table('orders')->where('payment_method','balance')->update(['payment_method' => 'manual_transfer']);
                DB::statement("ALTER TABLE `orders` MODIFY `payment_method` ENUM('manual_transfer') NOT NULL DEFAULT 'manual_transfer'");
            } catch (Throwable $e) {
                // ignore
            }
            try {
                // Replace refunded statuses to a safe value before shrinking enum
                DB::table('orders')->where('payment_status','refunded')->update(['payment_status' => 'paid']);
                DB::statement("ALTER TABLE `orders` MODIFY `payment_status` ENUM('unpaid','waiting_confirmation','paid','rejected') NOT NULL DEFAULT 'unpaid'");
            } catch (Throwable $e) {
                // ignore
            }
        }
    }
};
