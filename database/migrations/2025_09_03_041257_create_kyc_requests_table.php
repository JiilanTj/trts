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
        Schema::create('kyc_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('full_name', 150); // nama pada KTP
            $table->string('nik', 32)->nullable(); // optional if needed
            $table->string('birth_place',120)->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('rt_rw', 30)->nullable();
            $table->string('village', 120)->nullable(); // Kel/Desa
            $table->string('district', 120)->nullable(); // Kecamatan
            $table->string('religion', 40)->nullable();
            $table->string('marital_status', 40)->nullable();
            $table->string('occupation', 120)->nullable();
            $table->string('nationality', 50)->nullable();
            // File paths
            $table->string('ktp_front_path')->nullable();
            $table->string('ktp_back_path')->nullable();
            $table->string('selfie_ktp_path')->nullable();
            // Status
            $table->enum('status_kyc', ['pending','review','approved','rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['user_id','status_kyc']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_requests');
    }
};
