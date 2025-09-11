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
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Banking Information
            $table->string('account_holder_name'); // Nama Rekening Penerima
            $table->string('account_number'); // Nomor Rekening
            $table->string('bank_name'); // Nama Bank
            $table->string('bank_code')->nullable(); // Kode Bank (optional)
            
            // Withdrawal Details
            $table->decimal('amount', 15, 2); // Nominal penarikan
            $table->decimal('admin_fee', 10, 2)->default(0); // Biaya admin
            $table->decimal('total_deducted', 15, 2); // Total yang dipotong dari saldo
            
            // Status and Processing
            $table->enum('status', [
                'pending', 
                'processing', 
                'completed', 
                'rejected', 
                'cancelled'
            ])->default('pending');
            
            // Additional Information
            $table->text('notes')->nullable(); // Catatan user
            $table->text('admin_notes')->nullable(); // Catatan admin
            $table->string('transaction_reference')->nullable(); // Referensi transaksi
            
            // Processing Timestamps
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null'); // Admin yang memproses
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};
