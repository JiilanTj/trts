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
        Schema::create('store_showcases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('sort_order')->default(0); // Urutan display di etalase
            $table->boolean('is_featured')->default(false); // Produk unggulan di etalase
            $table->boolean('is_active')->default(true); // Status aktif di etalase
            $table->datetime('featured_until')->nullable(); // Sampai kapan jadi featured
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'is_active']);
            $table->index(['user_id', 'is_featured', 'sort_order']);
            $table->index(['product_id']);
            $table->unique(['user_id', 'product_id']); // Satu user tidak bisa showcase produk yang sama 2x
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_showcases');
    }
};
