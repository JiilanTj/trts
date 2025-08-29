<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        if (!DB::table('settings')->exists()) {
            DB::table('settings')->insert([
                'id' => 1,
                'payment_provider' => 'BCA',
                'account_name' => 'Admin',
                'account_number' => '1234567890',
                'logo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
