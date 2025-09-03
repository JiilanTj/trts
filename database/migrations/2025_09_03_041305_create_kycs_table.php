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
        Schema::create('kycs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kyc_request_id')->nullable()->constrained('kyc_requests')->nullOnDelete();
            $table->string('full_name',150);
            $table->string('nik',32)->nullable();
            $table->string('birth_place',120)->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('rt_rw',30)->nullable();
            $table->string('village',120)->nullable();
            $table->string('district',120)->nullable();
            $table->string('religion',40)->nullable();
            $table->string('marital_status',40)->nullable();
            $table->string('occupation',120)->nullable();
            $table->string('nationality',50)->nullable();
            $table->string('ktp_front_path')->nullable();
            $table->string('ktp_back_path')->nullable();
            $table->string('selfie_ktp_path')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('meta')->nullable(); // fleksibel
            $table->timestamps();
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kycs');
    }
};
