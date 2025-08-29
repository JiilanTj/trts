<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'full_name' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'balance' => 0,
            'level' => 10, // Admin level
            'role' => 'admin',
        ]);

        User::create([
            'full_name' => 'Test User',
            'username' => 'testuser',
            'password' => Hash::make('user123'),
            'balance' => 1000,
            'level' => 1,
            'role' => 'user',
        ]);
    }
}
