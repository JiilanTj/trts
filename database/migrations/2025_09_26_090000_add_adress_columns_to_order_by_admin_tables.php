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
        Schema::table('order_by_admin', function (Blueprint $table) {
            if (!Schema::hasColumn('order_by_admin', 'adress')) {
                $table->string('adress', 255)->default('')->after('product_id');
            }
        });

        Schema::table('scheduled_order_by_admin', function (Blueprint $table) {
            if (!Schema::hasColumn('scheduled_order_by_admin', 'adress')) {
                $table->string('adress', 255)->default('')->after('product_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_by_admin', function (Blueprint $table) {
            if (Schema::hasColumn('order_by_admin', 'adress')) {
                $table->dropColumn('adress');
            }
        });

        Schema::table('scheduled_order_by_admin', function (Blueprint $table) {
            if (Schema::hasColumn('scheduled_order_by_admin', 'adress')) {
                $table->dropColumn('adress');
            }
        });
    }
};
