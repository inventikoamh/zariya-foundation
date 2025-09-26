<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemUser;

class SystemUserSeeder extends Seeder
{
    public function run(): void
    {
        SystemUser::firstOrCreate(
            ['email' => 'system@example.com'],
            [
                'name' => 'System Admin',
                'password' => bcrypt('ChangeMe123!'),
            ]
        );
    }
}


