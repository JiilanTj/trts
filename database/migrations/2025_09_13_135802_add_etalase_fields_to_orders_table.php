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
            $table->unsignedBigInteger('seller_id')->nullable()->after('user_id');
            $table->boolean('from_etalase')->default(false)->after('payment_method');
            $table->decimal('etalase_margin', 15, 2)->default(0)->after('from_etalase');
            
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['seller_id', 'from_etalase']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
            $table->dropIndex(['seller_id', 'from_etalase']);
            $table->dropColumn(['seller_id', 'from_etalase', 'etalase_margin']);
        });
    }
};
