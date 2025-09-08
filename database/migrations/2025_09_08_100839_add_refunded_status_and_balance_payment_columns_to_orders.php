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
            if (!Schema::hasColumn('orders', 'payment_refunded_at')) {
                $table->timestamp('payment_refunded_at')->nullable()->after('payment_confirmed_by');
            }
            if (!Schema::hasColumn('orders', 'payment_refunded_by')) {
                $table->foreignId('payment_refunded_by')->nullable()->after('payment_refunded_at')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payment_refunded_by')) {
                $table->dropConstrainedForeignId('payment_refunded_by');
            }
            if (Schema::hasColumn('orders', 'payment_refunded_at')) {
                $table->dropColumn('payment_refunded_at');
            }
        });
    }
};
