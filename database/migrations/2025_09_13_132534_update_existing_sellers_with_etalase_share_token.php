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
        // Update existing sellers with etalase share tokens
        \App\Models\User::where('is_seller', true)
            ->whereNull('etalase_share_token')
            ->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    $token = \App\Models\User::generateUniqueEtalaseShareToken();
                    $user->update([
                        'etalase_share_token' => $token
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set all seller etalase share tokens to null
        \App\Models\User::where('is_seller', true)
            ->update(['etalase_share_token' => null]);
    }
};
