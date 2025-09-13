<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\StoreShowcase;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing showcases with share tokens
        StoreShowcase::whereNull('share_token')
            ->chunkById(100, function ($showcases) {
                foreach ($showcases as $showcase) {
                    $showcase->update([
                        'share_token' => $this->generateUniqueShareToken()
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set all share tokens to null
        StoreShowcase::query()->update(['share_token' => null]);
    }

    /**
     * Generate unique share token
     */
    private function generateUniqueShareToken()
    {
        do {
            $token = Str::random(32);
        } while (StoreShowcase::where('share_token', $token)->exists());

        return $token;
    }
};
