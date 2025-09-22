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
        Schema::create('order_by_admin', function (Blueprint $table) {
            $table->id();
            // Relations
            $table->foreignId('admin_id')->constrained('users');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('store_showcase_id')->constrained('store_showcases');
            $table->foreignId('product_id')->constrained('products');
            // Payload
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('unit_price');
            $table->unsignedBigInteger('total_price');
            $table->enum('status', ['PENDING','CONFIRMED'])->default('PENDING')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_by_admin');
    }
};
