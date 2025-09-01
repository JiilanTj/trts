<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Recreate column as required if it does not exist yet (fresh db) or adjust definition after rollback
            if (!Schema::hasColumn('orders', 'address')) {
                $table->string('address', 255)->after('external_customer_phone');
            } else {
                // Column exists (should be dropped on rollback path); keeping fallback no-op here.
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'address')) {
                $table->dropColumn('address');
            }
        });
    }
};
