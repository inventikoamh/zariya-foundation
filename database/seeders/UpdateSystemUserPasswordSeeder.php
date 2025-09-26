<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemUser;

class UpdateSystemUserPasswordSeeder extends Seeder
{
    public function run(): void
    {
        $user = SystemUser::firstOrCreate(
            ['email' => 'system@example.com'],
            ['name' => 'System Admin']
        );

        $user->password = bcrypt('ChangeMe123!');
        $user->save();
    }
}


