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
        // Expand enum values for status: add PACKED, SHIPPED, DELIVERED
        DB::statement("ALTER TABLE `order_by_admin` MODIFY `status` ENUM('PENDING','CONFIRMED','PACKED','SHIPPED','DELIVERED') NOT NULL DEFAULT 'PENDING'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum to original values
        // Note: Rows with other values must be migrated back before this or this will fail.
        DB::statement("ALTER TABLE `order_by_admin` MODIFY `status` ENUM('PENDING','CONFIRMED') NOT NULL DEFAULT 'PENDING'");
    }
};
