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
        Schema::create('user_follows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('follower_id'); // User yang follow
            $table->unsignedBigInteger('following_id'); // User yang di-follow (seller)
            $table->timestamp('followed_at')->useCurrent();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('follower_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('following_id')->references('id')->on('users')->onDelete('cascade');
            
            // Unique constraint - satu user cuma bisa follow seller sekali
            $table->unique(['follower_id', 'following_id'], 'unique_follow_relationship');
            
            // Indexes untuk performance
            $table->index('follower_id');
            $table->index('following_id');
            $table->index('followed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_follows');
    }
};
