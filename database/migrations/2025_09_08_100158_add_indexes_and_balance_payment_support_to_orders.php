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
        // Ensure table exists before attempting to create indexes
        if (!Schema::hasTable('orders') || !Schema::hasTable('order_items')) {
            return;
        }

        // Create indexes in a driver-compatible way (supports MySQL & SQLite)
        $this->createIndexIfNotExists('orders', 'orders_user_status_idx', ['user_id', 'status']);
        $this->createIndexIfNotExists('orders', 'orders_payment_status_idx', ['payment_status']);
        $this->createIndexIfNotExists('orders', 'orders_status_payment_status_idx', ['status', 'payment_status']);
        $this->createIndexIfNotExists('orders', 'orders_created_at_idx', ['created_at']);

        $this->createIndexIfNotExists('order_items', 'order_items_order_product_idx', ['order_id', 'product_id']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes in a driver-compatible way (supports MySQL & SQLite)
        $this->dropIndexIfExists('orders', 'orders_user_status_idx');
        $this->dropIndexIfExists('orders', 'orders_payment_status_idx');
        $this->dropIndexIfExists('orders', 'orders_status_payment_status_idx');
        $this->dropIndexIfExists('orders', 'orders_created_at_idx');

        $this->dropIndexIfExists('order_items', 'order_items_order_product_idx');
    }

    /**
     * Create index if it does not exist, supporting MySQL and SQLite.
     */
    private function createIndexIfNotExists(string $table, string $index, array $columns): void
    {
        if (!$this->tableExists($table)) {
            return;
        }

        if ($this->indexExists($table, $index)) {
            return;
        }

        $driver = $this->driver();
        $columnList = implode(', ', array_map(fn($c) => $this->quoteIdentifier($c, $driver), $columns));
        $tableName = $this->quoteIdentifier($table, $driver);

        // Both MySQL and SQLite accept this CREATE INDEX syntax (without IF NOT EXISTS for MySQL compat)
        $sql = "CREATE INDEX {$index} ON {$tableName} ({$columnList})";
        DB::statement($sql);
    }

    /**
     * Drop index if it exists, supporting MySQL and SQLite.
     */
    private function dropIndexIfExists(string $table, string $index): void
    {
        $driver = $this->driver();

        if (!$this->indexExists($table, $index)) {
            return;
        }

        if ($driver === 'mysql') {
            $tableName = $this->quoteIdentifier($table, $driver);
            $indexName = $this->quoteIdentifier($index, $driver);
            DB::statement("DROP INDEX {$indexName} ON {$tableName}");
        } elseif ($driver === 'sqlite') {
            // In SQLite, indexes are not namespaced by table
            DB::statement("DROP INDEX IF EXISTS {$index}");
        } else {
            // Fallback: try generic form
            try {
                DB::statement("DROP INDEX {$index}");
            } catch (\Throwable $e) {
                // Ignore if unsupported
            }
        }
    }

    private function driver(): string
    {
        return DB::connection()->getDriverName();
    }

    private function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }

    private function indexExists(string $table, string $index): bool
    {
        $driver = $this->driver();
        if ($driver === 'mysql') {
            $database = DB::getDatabaseName();
            return DB::table('information_schema.STATISTICS')
                ->where('TABLE_SCHEMA', $database)
                ->where('TABLE_NAME', $table)
                ->where('INDEX_NAME', $index)
                ->exists();
        }

        if ($driver === 'sqlite') {
            // PRAGMA index_list returns rows with columns: seq, name, unique, origin, partial
            $result = DB::select("PRAGMA index_list('" . str_replace("'", "''", $table) . "')");
            foreach ($result as $row) {
                // row is stdClass with property 'name'
                if (isset($row->name) && $row->name === $index) {
                    return true;
                }
            }
            return false;
        }

        // Fallback: attempt to query via Doctrine DBAL if available
        try {
            if (class_exists(\Doctrine\DBAL\Schema\AbstractSchemaManager::class)) {
                $schema = DB::connection()->getDoctrineSchemaManager();
                $indexes = $schema->listTableIndexes($table);
                return array_key_exists($index, $indexes);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return false;
    }

    private function quoteIdentifier(string $identifier, string $driver): string
    {
        if ($driver === 'mysql') {
            return '`' . str_replace('`', '``', $identifier) . '`';
        }
        if ($driver === 'sqlite') {
            return '"' . str_replace('"', '""', $identifier) . '"';
        }
        return '"' . str_replace('"', '""', $identifier) . '"';
    }
};
