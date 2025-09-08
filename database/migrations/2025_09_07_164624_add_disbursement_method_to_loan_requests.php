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
        Schema::table('loan_requests', function (Blueprint $table) {
            // Add disbursement method (saldo or bank transfer)
            $table->enum('disbursement_method', ['saldo', 'bank_transfer'])->default('saldo')->after('purpose_description');
            
            // Bank account details for bank transfer
            $table->string('bank_name', 100)->nullable()->after('disbursement_method');
            $table->string('bank_account_number', 50)->nullable()->after('bank_name');
            $table->string('bank_account_name', 100)->nullable()->after('bank_account_number');
            
            // Track disbursement details
            $table->text('disbursement_notes')->nullable()->after('admin_notes');
            $table->string('disbursement_reference')->nullable()->after('disbursement_notes'); // For bank transfer reference
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            $table->dropColumn([
                'disbursement_method',
                'bank_name',
                'bank_account_number', 
                'bank_account_name',
                'disbursement_notes',
                'disbursement_reference'
            ]);
        });
    }
};
