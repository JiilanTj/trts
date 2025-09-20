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
        Schema::create('scheduled_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('scheduled_order_batches')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users');
            $table->foreignId('product_id')->constrained('products');
            $table->unsignedInteger('quantity');
            // Optional price guard
            $table->unsignedBigInteger('price_cap')->nullable();
            $table->unsignedTinyInteger('tolerance_percent')->nullable();
            $table->enum('status', ['pending', 'created', 'failed', 'skipped'])->default('pending')->index();
            $table->foreignId('created_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['batch_id', 'seller_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_order_items');
    }
};
