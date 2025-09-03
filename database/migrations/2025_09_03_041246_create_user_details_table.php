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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phone', 30)->nullable();
            $table->string('secondary_phone', 30)->nullable();
            $table->string('gender', 20)->nullable(); // Laki-laki / Perempuan / lainnya
            $table->date('birth_date')->nullable();
            $table->string('birth_place', 120)->nullable();
            $table->string('address_line', 255)->nullable();
            $table->string('rt_rw', 30)->nullable();
            $table->string('village', 120)->nullable(); // Kel/Desa
            $table->string('district', 120)->nullable(); // Kecamatan
            $table->string('city', 120)->nullable();
            $table->string('province', 120)->nullable();
            $table->string('postal_code', 15)->nullable();
            $table->string('nationality', 80)->nullable(); // WNI / WNA
            $table->string('marital_status', 40)->nullable(); // Menikah / Belum Menikah / Duda / Janda
            $table->string('religion', 40)->nullable();
            $table->string('occupation', 120)->nullable();
            $table->json('extra')->nullable(); // fleksibel
            $table->timestamps();
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
