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
        Schema::create('scheduled_order_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->enum('purchase_type', ['self', 'external'])->default('self');
            $table->boolean('from_etalase')->default(true);
            $table->boolean('auto_paid')->default(false);
            $table->string('external_customer_name')->nullable();
            $table->string('external_customer_phone')->nullable();
            $table->text('address');
            $table->text('user_notes')->nullable();
            // Store in UTC and keep timezone string for UI
            $table->timestamp('schedule_at')->index();
            $table->string('timezone', 64)->default('Asia/Jakarta');
            $table->enum('status', ['draft', 'scheduled', 'processing', 'completed', 'partial', 'failed', 'canceled'])->default('scheduled')->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_order_batches');
    }
};
