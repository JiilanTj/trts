<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_order_by_admin_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_id')->constrained('scheduled_order_by_admin')->cascadeOnDelete();
            $table->foreignId('store_showcase_id')->constrained('store_showcases');
            $table->foreignId('product_id')->constrained('products');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('created_order_id')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // Optional: clean single-item columns later; keep for backward compatibility during transition
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_order_by_admin_items');
    }
};
