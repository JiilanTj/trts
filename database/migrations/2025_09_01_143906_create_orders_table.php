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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // optional if seller purchasing for external customer (purchase_type from logic)
            $table->enum('purchase_type', ['self','external'])->default('self');
            $table->string('external_customer_name')->nullable();
            $table->string('external_customer_phone')->nullable();
            // monetary
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('discount_total')->default(0);
            $table->unsignedBigInteger('grand_total');
            $table->unsignedBigInteger('seller_margin_total')->default(0); // margin if seller external sale
            // manual payment proof & status
            $table->enum('payment_method', ['manual_transfer'])->default('manual_transfer');
            $table->enum('payment_status', ['unpaid','waiting_confirmation','paid','rejected'])->default('unpaid');
            $table->string('payment_proof_path')->nullable();
            $table->timestamp('payment_confirmed_at')->nullable();
            $table->foreignId('payment_confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            // order workflow status
            $table->enum('status', [
                'pending',           // just created, awaiting payment upload
                'awaiting_confirmation', // user uploaded proof, waiting admin
                'packaging',         // dikemas
                'shipped',           // dikirim
                'delivered',         // diterima
                'completed',         // selesai (confirmed)
                'cancelled'          // dibatalkan
            ])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->text('user_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
