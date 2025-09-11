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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'visitors')) {
                $table->integer('visitors')->default(0)->after('credit_score');
            }
            if (!Schema::hasColumn('users', 'followers')) {
                $table->integer('followers')->default(0)->after('visitors');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'followers')) {
                $table->dropColumn('followers');
            }
            if (Schema::hasColumn('users', 'visitors')) {
                $table->dropColumn('visitors');
            }
        });
    }
};
