<?php

namespace Database\Seeders;

use App\Models\InvitationCode;
use App\Models\User;
use Illuminate\Database\Seeder;

class InvitationCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            $this->command->info('No admin user found. Please run AdminSeeder first.');
            return;
        }

        // Create some invitation codes
        $codes = [
            [
                'user_id' => $admin->id,
                'code' => 'ADMIN001',
                'max_usage' => 10,
                'expires_at' => now()->addMonths(3),
            ],
            [
                'user_id' => $admin->id,
                'code' => 'SELLER01',
                'max_usage' => 5,
                'expires_at' => now()->addMonth(),
            ],
            [
                'user_id' => $admin->id,
                'code' => 'WELCOME1',
                'max_usage' => 1,
                'expires_at' => null, // No expiration
            ],
        ];

        foreach ($codes as $codeData) {
            InvitationCode::create($codeData);
        }

        $this->command->info('Invitation codes seeded successfully.');
    }
}
