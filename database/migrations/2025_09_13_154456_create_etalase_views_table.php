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
        Schema::create('etalase_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('etalase_owner_id');
            $table->string('visitor_ip', 45);
            $table->unsignedBigInteger('visitor_user_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('viewed_at')->useCurrent();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('etalase_owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('visitor_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            
            // Indexes untuk performance
            $table->index('etalase_owner_id');
            $table->index('viewed_at');
            $table->index(['visitor_ip', 'visitor_user_id'], 'idx_visitor_combo');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etalase_views');
    }
};
