<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_order_by_admin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users'); // admin id
            $table->foreignId('user_id')->constrained('users');     // seller id
            $table->foreignId('store_showcase_id')->constrained('store_showcases');
            $table->foreignId('product_id')->constrained('products');
            $table->unsignedInteger('quantity');
            $table->dateTime('schedule_at'); // stored in UTC
            $table->string('timezone', 64)->default('Asia/Jakarta');
            $table->enum('status', ['scheduled','processing','completed','failed','canceled'])->default('scheduled')->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedBigInteger('created_order_id')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_order_by_admin');
    }
};
