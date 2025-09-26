<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user to create accounts
        $admin = User::whereHas('roles', function($query) {
            $query->where('name', 'SUPER_ADMIN');
        })->first();

        if (!$admin) {
            $this->command->warn('No admin user found. Please run the roles seeder first.');
            return;
        }

        // Create sample accounts
        $accounts = [
            [
                'name' => 'Main Bank Account',
                'account_number' => 'ACC' . date('Ymd') . '001',
                'type' => Account::TYPE_BANK,
                'bank_name' => 'Foundation Bank',
                'branch_name' => 'Main Branch',
                'ifsc_code' => 'FNDB0001234',
                'currency' => 'USD',
                'opening_balance' => 10000.00,
                'current_balance' => 10000.00,
                'description' => 'Primary bank account for foundation operations',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Cash Account',
                'account_number' => 'ACC' . date('Ymd') . '002',
                'type' => Account::TYPE_CASH,
                'currency' => 'USD',
                'opening_balance' => 5000.00,
                'current_balance' => 5000.00,
                'description' => 'Cash account for petty expenses',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
        ];

        foreach ($accounts as $accountData) {
            Account::create($accountData);
        }

        $this->command->info('Finance accounts created successfully!');
    }
}
