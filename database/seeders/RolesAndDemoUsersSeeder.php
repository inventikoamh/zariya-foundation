<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndDemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            'SUPER_ADMIN',
            'VOLUNTEER'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Create demo users
        $demoUsers = [
            [
                'name' => 'Super Admin',
                'email' => 'demo+admin@example.com',
                'phone' => '9000000001',
                'password' => 'Demo@123',
                'phone_verified_at' => now(),
                'role' => 'SUPER_ADMIN'
            ],
            [
                'name' => 'Demo Volunteer',
                'email' => 'demo+volunteer@example.com',
                'phone' => '9000000002',
                'password' => 'Demo@123',
                'phone_verified_at' => now(),
                'role' => 'VOLUNTEER'
            ],
            [
                'name' => 'Demo User',
                'email' => 'demo+user@example.com',
                'phone' => '9000000003',
                'password' => 'Demo@123',
                'phone_verified_at' => now(),
                'role' => null // Normal user - no specific role
            ]
        ];

        foreach ($demoUsers as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            
            $user = User::firstOrCreate(
                ['phone' => $userData['phone']],
                $userData
            );
            
            // Only assign role if it's not null (normal users have no role)
            if ($role) {
                $user->assignRole($role);
            }
        }

        $this->command->info('Roles and demo users created successfully!');
        $this->command->info('Demo users:');
        $this->command->info('- Super Admin: 9000000001 / Demo@123');
        $this->command->info('- Volunteer: 9000000002 / Demo@123');
        $this->command->info('- Normal User: 9000000003 / Demo@123 (can donate and request benefits)');
    }
}
