<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('order_by_admin')) { return; }
        $connection = config('database.default');
        $driver = config("database.connections.$connection.driver");

        if ($driver === 'mysql') {
            // Expand enum values for status: add PACKED, SHIPPED, DELIVERED
            try {
                DB::statement("ALTER TABLE `order_by_admin` MODIFY `status` ENUM('PENDING','CONFIRMED','PACKED','SHIPPED','DELIVERED') NOT NULL DEFAULT 'PENDING'");
            } catch (\Throwable $e) {
                // ignore if already altered or unsupported
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('order_by_admin')) { return; }
        $connection = config('database.default');
        $driver = config("database.connections.$connection.driver");

        if ($driver === 'mysql') {
            // Revert enum to original values
            // Note: Rows with other values must be migrated back before this or this will fail.
            try {
                // Normalize values that are not allowed in the smaller enum before altering
                DB::table('order_by_admin')->whereIn('status', ['PACKED','SHIPPED','DELIVERED'])->update(['status' => 'CONFIRMED']);
                DB::statement("ALTER TABLE `order_by_admin` MODIFY `status` ENUM('PENDING','CONFIRMED') NOT NULL DEFAULT 'PENDING'");
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }
};
