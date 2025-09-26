<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Database Check ===\n";
echo "Connection: " . config('database.default') . "\n";
echo "Database: " . config('database.connections.sqlite.database') . "\n";

try {
    echo "\n=== Table Counts ===\n";
    echo "Countries: " . App\Models\Country::count() . "\n";
    echo "Users: " . App\Models\User::count() . "\n";
    echo "Donations: " . App\Models\Donation::count() . "\n";
    echo "Beneficiaries: " . App\Models\Beneficiary::count() . "\n";
    echo "Accounts: " . App\Models\Account::count() . "\n";
    echo "Email Templates: " . App\Models\EmailTemplate::count() . "\n";
    
    echo "\n=== Sample Data ===\n";
    $countries = App\Models\Country::take(2)->get();
    foreach ($countries as $country) {
        echo "Country: {$country->name} ({$country->code})\n";
    }
    
    $users = App\Models\User::take(3)->get();
    foreach ($users as $user) {
        echo "User: {$user->name} ({$user->email})\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Check Complete ===\n";
