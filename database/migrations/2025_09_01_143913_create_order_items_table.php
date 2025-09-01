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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->unsignedInteger('quantity');
            // snapshot pricing at time of order
            $table->unsignedBigInteger('unit_price'); // harga yang dibayar (biasa atau jual)
            $table->unsignedBigInteger('base_price'); // harga_biasa snapshot
            $table->unsignedBigInteger('sell_price'); // harga_jual snapshot (maybe same as base if not seller)
            $table->unsignedBigInteger('discount')->default(0); // nominal discount per item applied (if promo etc)
            $table->unsignedBigInteger('seller_margin')->default(0); // margin per item for seller external sale
            $table->unsignedBigInteger('line_total'); // (unit_price * qty) - discounts already factored
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
