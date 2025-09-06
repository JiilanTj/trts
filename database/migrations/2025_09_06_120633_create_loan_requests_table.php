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
        Schema::create('loan_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('amount_requested'); // Amount in rupiah
            $table->unsignedInteger('duration_months'); // Loan duration in months
            $table->enum('purpose', ['business_expansion', 'inventory', 'equipment', 'working_capital', 'other']);
            $table->text('purpose_description')->nullable(); // Detailed description
            $table->decimal('interest_rate', 5, 2)->nullable(); // Interest rate percentage
            $table->unsignedBigInteger('monthly_payment')->nullable(); // Calculated monthly payment
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'disbursed', 'active', 'completed', 'defaulted'])->default('pending');
            $table->text('admin_notes')->nullable(); // Admin review notes
            $table->text('rejection_reason')->nullable(); // Reason for rejection
            $table->json('documents')->nullable(); // JSON array of uploaded document paths
            $table->json('credit_assessment')->nullable(); // JSON data of credit assessment
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamp('due_date')->nullable(); // Final payment due date
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
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
        Schema::dropIfExists('loan_requests');
    }
};
