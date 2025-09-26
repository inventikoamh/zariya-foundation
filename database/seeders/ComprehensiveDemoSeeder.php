<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Donation;
use App\Models\Beneficiary;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Transaction;
use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Models\VolunteerAssignment;
use App\Models\SystemUser;
use App\Models\SystemSetting;
use App\Models\Status;
use App\Models\Remark;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ComprehensiveDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        $this->clearExistingData();

        // Create system settings
        $this->createSystemSettings();

        // Create countries, states, cities
        $this->createLocations();

        // Create statuses
        $this->createStatuses();

        // Create system users
        $this->createSystemUsers();

        // Create demo users
        $this->createDemoUsers();

        // Create accounts
        $this->createAccounts();

        // Create beneficiaries
        $this->createBeneficiaries();

        // Create donations
        $this->createDonations();

        // Create volunteer assignments
        $this->createVolunteerAssignments();

        // Create expenses
        $this->createExpenses();

        // Create transactions
        $this->createTransactions();

        // Create achievements and user achievements
        $this->createAchievements();

        // Create remarks
        $this->createRemarks();
    }

    private function clearExistingData()
    {
        // Disable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear in reverse dependency order
        UserAchievement::truncate();
        Transaction::truncate();
        Expense::truncate();
        VolunteerAssignment::truncate();
        Remark::truncate();
        Donation::truncate();
        Beneficiary::truncate();
        Account::truncate();
        Achievement::truncate();
        Status::truncate();
        SystemSetting::truncate();
        SystemUser::truncate();
        User::truncate();
        City::truncate();
        State::truncate();
        Country::truncate();

        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function createSystemSettings()
    {
        SystemSetting::firstOrCreate(
            ['key' => 'crm_name'],
            [
                'value' => 'Indian Service Foundation CRM',
                'type' => 'string'
            ]
        );

        SystemSetting::firstOrCreate(
            ['key' => 'crm_logo'],
            [
                'value' => null,
                'type' => 'string'
            ]
        );

        SystemSetting::firstOrCreate(
            ['key' => 'contact_email'],
            [
                'value' => 'contact@indianservicefoundation.org',
                'type' => 'string'
            ]
        );

        SystemSetting::firstOrCreate(
            ['key' => 'contact_phone'],
            [
                'value' => '+91-9876543210',
                'type' => 'string'
            ]
        );

        // Front page settings
        SystemSetting::firstOrCreate(
            ['key' => 'front_title'],
            [
                'value' => 'Indian Service Foundation - Making a Difference Together',
                'type' => 'string'
            ]
        );

        SystemSetting::firstOrCreate(
            ['key' => 'front_headline'],
            [
                'value' => 'Indian Service Foundation',
                'type' => 'string'
            ]
        );

        SystemSetting::firstOrCreate(
            ['key' => 'front_subheadline'],
            [
                'value' => 'Making a difference together. Join us in creating positive change through donations, volunteer work, and community support.',
                'type' => 'string'
            ]
        );

        SystemSetting::firstOrCreate(
            ['key' => 'front_cta_primary_text'],
            [
                'value' => 'Donate Now',
                'type' => 'string'
            ]
        );

        SystemSetting::firstOrCreate(
            ['key' => 'front_cta_primary_link'],
            [
                'value' => route('wizard', 'donation'),
                'type' => 'string'
            ]
        );

        SystemSetting::firstOrCreate(
            ['key' => 'front_cta_secondary_text'],
            [
                'value' => 'Request Assistance',
                'type' => 'string'
            ]
        );

        SystemSetting::firstOrCreate(
            ['key' => 'front_cta_secondary_link'],
            [
                'value' => route('wizard', 'beneficiary'),
                'type' => 'string'
            ]
        );

        SystemSetting::firstOrCreate(
            ['key' => 'front_bg_from'],
            [
                'value' => '#eff6ff',
                'type' => 'string'
            ]
        );

        SystemSetting::firstOrCreate(
            ['key' => 'front_bg_to'],
            [
                'value' => '#e0e7ff',
                'type' => 'string'
            ]
        );
    }

    private function createLocations()
    {
        // Create basic countries, states, and cities for demo
        $usa = Country::firstOrCreate([
            'name' => 'United States',
            'code' => 'USA',
            'phone_code' => '+1',
            'is_active' => true
        ]);

        $india = Country::firstOrCreate([
            'name' => 'India',
            'code' => 'IND',
            'phone_code' => '+91',
            'is_active' => true
        ]);

        // Create states
        $california = State::firstOrCreate([
            'name' => 'California',
            'code' => 'CA',
            'country_id' => $usa->id,
            'is_active' => true
        ]);

        $maharashtra = State::firstOrCreate([
            'name' => 'Maharashtra',
            'code' => 'MH',
            'country_id' => $india->id,
            'is_active' => true
        ]);

        // Create cities
        City::firstOrCreate([
            'name' => 'Los Angeles',
            'state_id' => $california->id,
            'country_id' => $usa->id,
            'is_active' => true
        ]);

        City::firstOrCreate([
            'name' => 'San Francisco',
            'state_id' => $california->id,
            'country_id' => $usa->id,
            'is_active' => true
        ]);

        City::firstOrCreate([
            'name' => 'Mumbai',
            'state_id' => $maharashtra->id,
            'country_id' => $india->id,
            'is_active' => true
        ]);

        City::firstOrCreate([
            'name' => 'Pune',
            'state_id' => $maharashtra->id,
            'country_id' => $india->id,
            'is_active' => true
        ]);
    }

    private function createStatuses()
    {
        $statuses = [
            ['name' => 'active', 'display_name' => 'Active', 'type' => 'general', 'color' => '#10B981'],
            ['name' => 'inactive', 'display_name' => 'Inactive', 'type' => 'general', 'color' => '#6B7280'],
            ['name' => 'pending', 'display_name' => 'Pending', 'type' => 'donation', 'color' => '#F59E0B'],
            ['name' => 'completed', 'display_name' => 'Completed', 'type' => 'donation', 'color' => '#10B981'],
            ['name' => 'cancelled', 'display_name' => 'Cancelled', 'type' => 'donation', 'color' => '#EF4444'],
            ['name' => 'assigned', 'display_name' => 'Assigned', 'type' => 'volunteer', 'color' => '#3B82F6'],
            ['name' => 'in_progress', 'display_name' => 'In Progress', 'type' => 'volunteer', 'color' => '#F97316'],
        ];

        foreach ($statuses as $status) {
            Status::create($status);
        }
    }

    private function createSystemUsers()
    {
        SystemUser::create([
            'name' => 'System Administrator',
            'email' => 'admin@foundationcrm.com',
            'password' => Hash::make('password')
        ]);
    }

    private function createDemoUsers()
    {
        $users = [
            [
                'name' => 'John Smith',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'phone' => '9876543210',
                'phone_country_code' => '+91',
                'first_name' => 'John',
                'last_name' => 'Smith',
                'gender' => 'male',
                'email_verified_at' => now()
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'password' => Hash::make('password'),
                'phone' => '9876543211',
                'phone_country_code' => '+91',
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'gender' => 'female',
                'email_verified_at' => now()
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike@example.com',
                'password' => Hash::make('password'),
                'phone' => '9876543212',
                'phone_country_code' => '+91',
                'first_name' => 'Mike',
                'last_name' => 'Wilson',
                'gender' => 'male',
                'email_verified_at' => now()
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily@example.com',
                'password' => Hash::make('password'),
                'phone' => '9876543213',
                'phone_country_code' => '+91',
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'gender' => 'female',
                'email_verified_at' => now()
            ],
            [
                'name' => 'David Brown',
                'email' => 'david@example.com',
                'password' => Hash::make('password'),
                'phone' => '9876543214',
                'phone_country_code' => '+91',
                'first_name' => 'David',
                'last_name' => 'Brown',
                'gender' => 'male',
                'email_verified_at' => now()
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa@example.com',
                'password' => Hash::make('password'),
                'phone' => '9876543215',
                'phone_country_code' => '+91',
                'first_name' => 'Lisa',
                'last_name' => 'Anderson',
                'gender' => 'female',
                'email_verified_at' => now()
            ],
            [
                'name' => 'Robert Taylor',
                'email' => 'robert@example.com',
                'password' => Hash::make('password'),
                'phone' => '9876543216',
                'phone_country_code' => '+91',
                'first_name' => 'Robert',
                'last_name' => 'Taylor',
                'gender' => 'male',
                'email_verified_at' => now()
            ],
            [
                'name' => 'Jennifer Martinez',
                'email' => 'jennifer@example.com',
                'password' => Hash::make('password'),
                'phone' => '9876543217',
                'phone_country_code' => '+91',
                'first_name' => 'Jennifer',
                'last_name' => 'Martinez',
                'gender' => 'female',
                'email_verified_at' => now()
            ]
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Assign roles using Spatie permissions
        $volunteers = User::whereIn('email', ['mike@example.com', 'emily@example.com', 'robert@example.com'])->get();
        $admin = User::where('email', 'david@example.com')->first();
        // Note: Other users (john, sarah, lisa, jennifer) are normal users with no specific role

        // Create roles if they don't exist (only SUPER_ADMIN and VOLUNTEER according to ROLE_STRUCTURE.md)
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'VOLUNTEER']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'SUPER_ADMIN']);

        // Assign roles
        foreach ($volunteers as $volunteer) {
            $volunteer->assignRole('VOLUNTEER');
        }
        if ($admin) {
            $admin->assignRole('SUPER_ADMIN');
        }
        // Note: Donors (john, sarah, lisa, jennifer) are normal users with no specific role
    }

    private function createAccounts()
    {
        // Get the first user as created_by
        $firstUser = User::first();

        $accounts = [
            [
                'name' => 'Main Foundation Account',
                'type' => 'bank',
                'account_number' => '1234567890',
                'ifsc_code' => 'SBIN0001234',
                'bank_name' => 'State Bank of India',
                'current_balance' => 50000.00,
                'currency' => 'INR',
                'is_active' => true,
                'created_by' => $firstUser->id
            ],
            [
                'name' => 'Emergency Fund',
                'type' => 'bank',
                'account_number' => '9876543210',
                'ifsc_code' => 'HDFC0005678',
                'bank_name' => 'HDFC Bank',
                'current_balance' => 25000.00,
                'currency' => 'INR',
                'is_active' => true,
                'created_by' => $firstUser->id
            ],
            [
                'name' => 'International Account',
                'type' => 'bank',
                'account_number' => '5555666677',
                'ifsc_code' => 'ICIC0009999',
                'bank_name' => 'ICICI Bank',
                'current_balance' => 10000.00,
                'currency' => 'USD',
                'is_active' => true,
                'created_by' => $firstUser->id
            ]
        ];

        foreach ($accounts as $accountData) {
            Account::create($accountData);
        }
    }

    private function createBeneficiaries()
    {
        $volunteers = User::role('VOLUNTEER')->get();
        $admin = User::role('SUPER_ADMIN')->first();

        $beneficiaries = [
            [
                'name' => 'Rahul Sharma',
                'email' => 'rahul.sharma@example.com',
                'phone' => '+91-9876543210',
                'category' => 'medical',
                'description' => 'Medical assistance for heart surgery',
                'urgency_notes' => 'Patient requires immediate surgery',
                'status' => 'approved',
                'priority' => 'high',
                'estimated_amount' => 50000.00,
                'currency' => 'INR',
                'location' => [
                    'country' => 'India',
                    'state' => 'Maharashtra',
                    'city' => 'Mumbai',
                    'pincode' => '400001'
                ],
                'assigned_to' => $volunteers->first()->id,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'admin_notes' => 'Approved for medical assistance'
            ],
            [
                'name' => 'Priya Patel',
                'email' => 'priya.patel@example.com',
                'phone' => '+91-9876543211',
                'category' => 'education',
                'description' => 'Educational support for children',
                'urgency_notes' => 'Children need school supplies and tuition',
                'status' => 'approved',
                'priority' => 'medium',
                'estimated_amount' => 15000.00,
                'currency' => 'INR',
                'location' => [
                    'country' => 'India',
                    'state' => 'Maharashtra',
                    'city' => 'Pune',
                    'pincode' => '411001'
                ],
                'assigned_to' => $volunteers->skip(1)->first()->id,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'admin_notes' => 'Approved for educational support'
            ],
            [
                'name' => 'Amit Kumar',
                'email' => 'amit.kumar@example.com',
                'phone' => '+91-9876543212',
                'category' => 'food',
                'description' => 'Food assistance for family',
                'urgency_notes' => 'Family of 5 needs immediate food support',
                'status' => 'approved',
                'priority' => 'high',
                'estimated_amount' => 10000.00,
                'currency' => 'INR',
                'location' => [
                    'country' => 'India',
                    'state' => 'Maharashtra',
                    'city' => 'Mumbai',
                    'pincode' => '400002'
                ],
                'assigned_to' => $volunteers->first()->id,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'admin_notes' => 'Approved for food assistance'
            ]
        ];

        foreach ($beneficiaries as $beneficiaryData) {
            Beneficiary::create($beneficiaryData);
        }
    }

    private function createDonations()
    {
        $donors = User::whereDoesntHave('roles')->get();
        $volunteers = User::role('VOLUNTEER')->get();
        $cities = City::all();
        $states = State::all();
        $countries = Country::all();

        // Ensure we have enough data
        if ($donors->count() < 4) {
            $this->command->warn('Not enough donors found. Creating additional donors...');
            return;
        }
        if ($volunteers->count() < 2) {
            $this->command->warn('Not enough volunteers found. Creating additional volunteers...');
            return;
        }
        if ($countries->count() < 2) {
            $this->command->warn('Not enough countries found. Creating additional countries...');
            return;
        }

        $donations = [
            [
                'type' => 'monetary',
                'details' => [
                    'amount' => 5000.00,
                    'currency' => 'INR',
                    'payment_method' => 'bank_transfer'
                ],
                'status' => 'completed',
                'donor_id' => $donors->first()->id,
                'country_id' => $countries->skip(1)->first()?->id ?? $countries->first()->id,
                'state_id' => $states->skip(1)->first()?->id ?? $states->first()->id,
                'city_id' => $cities->skip(2)->first()?->id ?? $cities->first()->id,
                'pincode' => '400001',
                'address' => '123 Main Street, Mumbai',
                'assigned_to' => $volunteers->first()->id,
                'assigned_by' => User::role('SUPER_ADMIN')->first()->id,
                'assigned_at' => now()->subDays(5),
                'completed_at' => now()->subDays(2),
                'completion_notes' => 'Donation successfully processed and transferred to beneficiary account',
                'notes' => 'Emergency medical fund donation',
                'is_urgent' => true,
                'priority' => 3
            ],
            [
                'type' => 'materialistic',
                'details' => [
                    'item_name' => 'School Supplies',
                    'item_description' => 'Books, notebooks, pens, and educational materials',
                    'alternate_phone' => '+91-9876543210',
                    'images' => []
                ],
                'status' => 'in_progress',
                'donor_id' => $donors->skip(1)->first()?->id ?? $donors->first()->id,
                'country_id' => $countries->skip(1)->first()?->id ?? $countries->first()->id,
                'state_id' => $states->skip(1)->first()?->id ?? $states->first()->id,
                'city_id' => $cities->skip(3)->first()?->id ?? $cities->first()->id,
                'pincode' => '411001',
                'address' => '456 Park Avenue, Pune',
                'assigned_to' => $volunteers->skip(1)->first()?->id ?? $volunteers->first()->id,
                'assigned_by' => User::role('SUPER_ADMIN')->first()->id,
                'assigned_at' => now()->subDays(3),
                'notes' => 'Educational materials for underprivileged children',
                'is_urgent' => false,
                'priority' => 2
            ],
            [
                'type' => 'service',
                'details' => [
                    'service_type' => 'teaching',
                    'service_description' => 'Volunteer teaching services for mathematics and science',
                    'availability' => ['weekends', 'evenings']
                ],
                'status' => 'assigned',
                'donor_id' => $donors->skip(2)->first()?->id ?? $donors->first()->id,
                'country_id' => $countries->skip(1)->first()?->id ?? $countries->first()->id,
                'state_id' => $states->skip(1)->first()?->id ?? $states->first()->id,
                'city_id' => $cities->skip(2)->first()?->id ?? $cities->first()->id,
                'pincode' => '400002',
                'address' => '789 Garden Road, Mumbai',
                'assigned_to' => $volunteers->first()->id,
                'assigned_by' => User::role('SUPER_ADMIN')->first()->id,
                'assigned_at' => now()->subDays(1),
                'notes' => 'Teaching services for community education program',
                'is_urgent' => false,
                'priority' => 2
            ],
            [
                'type' => 'monetary',
                'details' => [
                    'amount' => 1000.00,
                    'currency' => 'USD',
                    'payment_method' => 'credit_card'
                ],
                'status' => 'pending',
                'donor_id' => $donors->skip(3)->first()?->id ?? $donors->first()->id,
                'country_id' => $countries->first()->id,
                'state_id' => $states->first()->id,
                'city_id' => $cities->first()->id,
                'pincode' => '90210',
                'address' => '123 Hollywood Blvd, Los Angeles',
                'notes' => 'Monthly recurring donation for food program',
                'is_urgent' => false,
                'priority' => 1
            ],
            [
                'type' => 'materialistic',
                'details' => [
                    'item_name' => 'Clothing and Blankets',
                    'item_description' => 'Winter clothing, blankets, and warm accessories',
                    'alternate_phone' => '+1-555-0101',
                    'images' => []
                ],
                'status' => 'completed',
                'donor_id' => $donors->first()->id,
                'country_id' => $countries->skip(1)->first()?->id ?? $countries->first()->id,
                'state_id' => $states->skip(1)->first()?->id ?? $states->first()->id,
                'city_id' => $cities->skip(2)->first()?->id ?? $cities->first()->id,
                'pincode' => '400003',
                'address' => '321 Oak Street, Mumbai',
                'assigned_to' => $volunteers->skip(1)->first()?->id ?? $volunteers->first()->id,
                'assigned_by' => User::role('SUPER_ADMIN')->first()->id,
                'assigned_at' => now()->subDays(7),
                'completed_at' => now()->subDays(4),
                'completion_notes' => 'Clothing distributed to 25 families in need',
                'notes' => 'Winter relief materials for homeless families',
                'is_urgent' => true,
                'priority' => 3
            ]
        ];

        foreach ($donations as $donationData) {
            Donation::create($donationData);
        }
    }

    private function createVolunteerAssignments()
    {
        $volunteers = User::role('VOLUNTEER')->get();
        $countries = Country::all();
        $states = State::all();
        $cities = City::all();

        // Check if we have enough data
        if ($volunteers->count() < 1) {
            $this->command->warn('Not enough volunteers found for assignments.');
            return;
        }
        if ($countries->count() < 1) {
            $this->command->warn('Not enough countries found for assignments.');
            return;
        }

        $assignments = [
            [
                'user_id' => $volunteers->first()->id,
                'assignment_type' => 'country',
                'country_id' => $countries->first()->id,
                'role' => 'head_volunteer',
                'is_active' => true,
                'notes' => 'Head volunteer for United States operations'
            ],
            [
                'user_id' => $volunteers->skip(1)->first()?->id ?? $volunteers->first()->id,
                'assignment_type' => 'state',
                'country_id' => $countries->first()->id,
                'state_id' => $states->first()->id,
                'role' => 'volunteer',
                'is_active' => true,
                'notes' => 'Volunteer for California state operations'
            ],
            [
                'user_id' => $volunteers->skip(2)->first()?->id ?? $volunteers->first()->id,
                'assignment_type' => 'city',
                'country_id' => $countries->skip(1)->first()->id,
                'state_id' => $states->skip(1)->first()->id,
                'city_id' => $cities->skip(2)->first()->id,
                'role' => 'volunteer',
                'is_active' => true,
                'notes' => 'Volunteer for Mumbai city operations'
            ]
        ];

        foreach ($assignments as $assignmentData) {
            VolunteerAssignment::create($assignmentData);
        }
    }

    private function createExpenses()
    {
        $accounts = Account::all();
        $admin = User::role('SUPER_ADMIN')->first();
        $volunteer = User::role('VOLUNTEER')->first();

        $expenses = [
            [
                'title' => 'Medical supplies for beneficiary',
                'description' => 'Medical supplies for beneficiary',
                'amount' => 2500.00,
                'currency' => 'INR',
                'category' => 'other',
                'account_id' => $accounts[0]->id,
                'requested_by' => $volunteer->id,
                'status' => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => now()->subDays(2)
            ],
            [
                'title' => 'Transportation costs for volunteer activities',
                'description' => 'Transportation costs for volunteer activities',
                'amount' => 500.00,
                'currency' => 'INR',
                'category' => 'transportation',
                'account_id' => $accounts[0]->id,
                'requested_by' => $volunteer->id,
                'status' => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => now()->subDays(4)
            ],
            [
                'title' => 'Office supplies and equipment',
                'description' => 'Office supplies and equipment',
                'amount' => 1200.00,
                'currency' => 'INR',
                'category' => 'office_supplies',
                'account_id' => $accounts[1]->id,
                'requested_by' => $volunteer->id,
                'status' => 'pending'
            ],
            [
                'title' => 'Emergency food distribution',
                'description' => 'Emergency food distribution',
                'amount' => 3000.00,
                'currency' => 'INR',
                'category' => 'other',
                'account_id' => $accounts[0]->id,
                'requested_by' => $volunteer->id,
                'status' => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => now()
            ]
        ];

        foreach ($expenses as $expenseData) {
            Expense::create($expenseData);
        }
    }

    private function createTransactions()
    {
        $accounts = Account::all();
        $donations = Donation::where('type', 'monetary')->get();
        $expenses = Expense::all();
        $admin = User::role('SUPER_ADMIN')->first();

        $transactions = [
            [
                'transaction_number' => 'TXN' . now()->format('Ymd') . rand(1000, 9999),
                'type' => 'donation',
                'amount' => 5000.00,
                'currency' => 'INR',
                'description' => 'Donation received from John Smith',
                'status' => 'completed',
                'to_account_id' => $accounts[0]->id,
                'donation_id' => $donations[0]->id,
                'created_by' => $admin->id,
                'approved_by' => $admin->id,
                'approved_at' => now()->subDays(5),
                'processed_at' => now()->subDays(5)
            ],
            [
                'transaction_number' => 'TXN' . now()->format('Ymd') . rand(1000, 9999),
                'type' => 'expense',
                'amount' => 2500.00,
                'currency' => 'INR',
                'description' => 'Medical expense payment',
                'status' => 'completed',
                'from_account_id' => $accounts[0]->id,
                'expense_id' => $expenses->first()->id,
                'created_by' => $admin->id,
                'approved_by' => $admin->id,
                'approved_at' => now()->subDays(3),
                'processed_at' => now()->subDays(3)
            ],
            [
                'transaction_number' => 'TXN' . now()->format('Ymd') . rand(1000, 9999),
                'type' => 'donation',
                'amount' => 1000.00,
                'currency' => 'USD',
                'description' => 'International donation received',
                'status' => 'pending',
                'to_account_id' => $accounts[2]->id,
                'donation_id' => $donations->skip(3)->first()?->id ?? $donations->first()->id,
                'created_by' => $admin->id
            ]
        ];

        foreach ($transactions as $transactionData) {
            Transaction::create($transactionData);
        }
    }

    private function createAchievements()
    {
        // Create some achievements
        $achievements = [
            [
                'name' => 'First Donor',
                'description' => 'Made your very first donation to the foundation.',
                'type' => 'donation',
                'category' => 'milestone',
                'icon_image' => 'achievements/icons/first_donor.png',
                'criteria' => [
                    'type' => 'milestone',
                    'milestone' => 'first_donation',
                ],
                'points' => 20,
                'rarity' => 'common',
                'is_active' => true,
                'is_repeatable' => false,
            ],
            [
                'name' => 'Generous Heart',
                'description' => 'Completed 5 donations to the foundation.',
                'type' => 'donation',
                'category' => 'completion',
                'icon_image' => 'achievements/icons/generous_heart.png',
                'criteria' => [
                    'type' => 'donation_count',
                    'min_count' => 5,
                    'status' => 'completed',
                ],
                'points' => 75,
                'rarity' => 'uncommon',
                'is_active' => true,
                'is_repeatable' => true,
            ],
            [
                'name' => 'Volunteer Hero',
                'description' => 'Completed 10 volunteer assignments.',
                'type' => 'volunteer',
                'category' => 'completion',
                'icon_image' => 'achievements/icons/volunteer_hero.png',
                'criteria' => [
                    'type' => 'volunteer_completion',
                    'min_completions' => 10,
                ],
                'points' => 200,
                'rarity' => 'rare',
                'is_active' => true,
                'is_repeatable' => true,
            ]
        ];

        foreach ($achievements as $achievementData) {
            Achievement::create($achievementData);
        }

        // Award some achievements to users
        $users = User::whereDoesntHave('roles')->take(3)->get();
        $achievements = Achievement::take(2)->get();

        foreach ($users as $index => $user) {
            if ($index < 2) {
                UserAchievement::create([
                    'user_id' => $user->id,
                    'achievement_id' => $achievements[$index]->id,
                    'earned_at' => now()->subDays(rand(1, 30)),
                    'metadata' => ['source' => 'demo_data'],
                    'is_notified' => true
                ]);
            }
        }
    }

    private function createRemarks()
    {
        $donations = Donation::all();
        $users = User::all();

        $remarks = [
            [
                'remarkable_type' => 'App\Models\Donation',
                'remarkable_id' => $donations[0]->id,
                'user_id' => $users[0]->id,
                'type' => 'general',
                'remark' => 'Donation submitted successfully'
            ],
            [
                'remarkable_type' => 'App\Models\Donation',
                'remarkable_id' => $donations[0]->id,
                'user_id' => $users[4]->id, // Admin user
                'type' => 'assignment',
                'remark' => 'Assigned to volunteer for processing'
            ],
            [
                'remarkable_type' => 'App\Models\Donation',
                'remarkable_id' => $donations[0]->id,
                'user_id' => $users[2]->id, // Volunteer
                'type' => 'completion',
                'remark' => 'Donation processed and funds transferred to beneficiary'
            ]
        ];

        foreach ($remarks as $remarkData) {
            Remark::create($remarkData);
        }
    }
}
