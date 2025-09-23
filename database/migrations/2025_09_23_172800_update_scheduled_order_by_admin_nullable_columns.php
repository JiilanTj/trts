<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('scheduled_order_by_admin')) { return; }
        $driver = DB::connection()->getDriverName();
        if ($driver !== 'mysql') { return; }

        // Drop foreign keys before altering nullability
        Schema::table('scheduled_order_by_admin', function (Blueprint $table) {
            try { $table->dropForeign(['store_showcase_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['product_id']); } catch (\Throwable $e) {}
        });

        // Alter columns to be nullable / set default
        DB::statement('ALTER TABLE `scheduled_order_by_admin` MODIFY `store_showcase_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `scheduled_order_by_admin` MODIFY `product_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `scheduled_order_by_admin` MODIFY `quantity` INT UNSIGNED NOT NULL DEFAULT 0');

        // Re-create foreign keys
        Schema::table('scheduled_order_by_admin', function (Blueprint $table) {
            $table->foreign('store_showcase_id')->references('id')->on('store_showcases');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('scheduled_order_by_admin')) { return; }
        $driver = DB::connection()->getDriverName();
        if ($driver !== 'mysql') { return; }

        // WARNING: This will fail if rows contain NULLs; adjust data before rollback.
        Schema::table('scheduled_order_by_admin', function (Blueprint $table) {
            try { $table->dropForeign(['store_showcase_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['product_id']); } catch (\Throwable $e) {}
        });

        DB::statement('ALTER TABLE `scheduled_order_by_admin` MODIFY `store_showcase_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `scheduled_order_by_admin` MODIFY `product_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `scheduled_order_by_admin` MODIFY `quantity` INT UNSIGNED NOT NULL');

        Schema::table('scheduled_order_by_admin', function (Blueprint $table) {
            $table->foreign('store_showcase_id')->references('id')->on('store_showcases');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }
};
