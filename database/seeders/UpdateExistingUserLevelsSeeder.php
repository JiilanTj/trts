<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TopupRequest;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class UpdateExistingUserLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Calculate retroactive transaction amounts for existing users
     */
    public function run(): void
    {
        $this->command->info('Starting retroactive user level calculation...');
        
        $users = User::where('role', 'user')->get();
        $updated = 0;
        
        foreach ($users as $user) {
            // Calculate total approved topup amounts
            $topupTotal = TopupRequest::where('user_id', $user->id)
                ->where('status', 'approved')
                ->sum('amount');
            
            // Calculate total paid order amounts
            $orderTotal = Order::where('user_id', $user->id)
                ->where('payment_status', 'paid')
                ->sum('grand_total');
            
            $totalTransactionAmount = $topupTotal + $orderTotal;
            
            // Update user transaction amount
            $user->update([
                'total_transaction_amount' => $totalTransactionAmount,
                'last_level_check' => now(),
            ]);
            
            // Check and upgrade level if qualified
            $oldLevel = $user->level;
            $upgraded = $user->checkAndUpgradeLevel();
            
            if ($upgraded) {
                $this->command->info("User {$user->username}: Level {$oldLevel} → {$user->fresh()->level} (Rp " . number_format($totalTransactionAmount, 0, ',', '.') . ")");
            } else {
                $this->command->line("User {$user->username}: Level {$user->level} (Rp " . number_format($totalTransactionAmount, 0, ',', '.') . ")");
            }
            
            $updated++;
        }
        
        $this->command->info("Processed {$updated} users successfully.");
    }
}
