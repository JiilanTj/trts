<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Database\Seeder;

class WithdrawalRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some existing users (assuming they exist)
        $users = User::where('role', 'user')->take(5)->get();
        
        if ($users->count() > 0) {
            foreach ($users as $user) {
                // Create 2-3 withdrawal requests per user with different statuses
                WithdrawalRequest::factory()->pending()->create(['user_id' => $user->id]);
                WithdrawalRequest::factory()->completed()->create(['user_id' => $user->id]);
                WithdrawalRequest::factory()->processing()->create(['user_id' => $user->id]);
            }
        }
        
        // Create some additional withdrawal requests with random users
        WithdrawalRequest::factory()->count(10)->create();
        
        echo "Withdrawal requests seeded successfully!\n";
    }
}
