<?php

namespace Database\Seeders;

use App\Models\InvitationCode;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdditionalInvitationCodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->command->warn('No admin user found. Please run AdminSeeder first.');
            return;
        }

        $codes = [
            'F003',
            'GISKAF007',
            'LINA5007',
            '5004',
            '927005',
            'MELANYH0495',
            'APRILH0495',
            'YOLANDAPUTRIANJANIR215',
            'PUTUAYUDIANAR061',
            'AJENGPUSPAPRADIPTA129K',
            'OCHAD007',
            'DWIS003',
            'PUTUAYUDEWID220',
            'MAYANGPUTRIE434',
            'MEGASORAYAMS001',
            'MIRALESTARIK0771',
        ];

        foreach ($codes as $code) {
            InvitationCode::updateOrCreate([
                'code' => $code,
            ], [
                'user_id' => $admin->id,
                'max_usage' => 100,
                'expires_at' => null,
            ]);
        }

        $this->command->info(count($codes) . ' additional invitation codes seeded successfully.');
    }
}
