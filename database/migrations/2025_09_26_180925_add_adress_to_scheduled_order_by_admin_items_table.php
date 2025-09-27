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
        Schema::table('scheduled_order_by_admin_items', function (Blueprint $table) {
            if (!Schema::hasColumn('scheduled_order_by_admin_items', 'adress')) {
                $table->string('adress', 255)->default('')->after('quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheduled_order_by_admin_items', function (Blueprint $table) {
            if (Schema::hasColumn('scheduled_order_by_admin_items', 'adress')) {
                $table->dropColumn('adress');
            }
        });
    }
};
