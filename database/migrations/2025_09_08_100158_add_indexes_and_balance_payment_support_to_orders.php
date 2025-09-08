<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Create indexes only if they don't exist (MySQL variant) using raw information_schema check
        });

        $this->createIndexIfNotExists('orders', 'orders_user_status_idx', 'CREATE INDEX orders_user_status_idx ON orders (user_id, status)');
        $this->createIndexIfNotExists('orders', 'orders_payment_status_idx', 'CREATE INDEX orders_payment_status_idx ON orders (payment_status)');
        $this->createIndexIfNotExists('orders', 'orders_status_payment_status_idx', 'CREATE INDEX orders_status_payment_status_idx ON orders (status, payment_status)');
        $this->createIndexIfNotExists('orders', 'orders_created_at_idx', 'CREATE INDEX orders_created_at_idx ON orders (created_at)');

        $this->createIndexIfNotExists('order_items', 'order_items_order_product_idx', 'CREATE INDEX order_items_order_product_idx ON order_items (order_id, product_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexIfExists('orders', 'orders_user_status_idx');
        $this->dropIndexIfExists('orders', 'orders_payment_status_idx');
        $this->dropIndexIfExists('orders', 'orders_status_payment_status_idx');
        $this->dropIndexIfExists('orders', 'orders_created_at_idx');

        $this->dropIndexIfExists('order_items', 'order_items_order_product_idx');
    }

    /**
     * Create index if it does not exist (MySQL variant).
     */
    private function createIndexIfNotExists(string $table, string $index, string $createSql): void
    {
        $database = DB::getDatabaseName();
        $exists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists();
        if (!$exists) {
            DB::statement($createSql);
        }
    }

    /**
     * Drop index if it exists (MySQL variant).
     */
    private function dropIndexIfExists(string $table, string $index): void
    {
        $database = DB::getDatabaseName();
        $exists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists();
        if ($exists) {
            DB::statement("DROP INDEX `$index` ON `$table`");
        }
    }
};
