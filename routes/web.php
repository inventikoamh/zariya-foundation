<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\VolunteerController;
// use Illuminate\Support\Facades\Route; // duplicate removed
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::view('/', 'welcome');

// Deployment route (temporary - remove after deployment)
Route::get('/deploy', function () {
    $secret_key = 'your-secret-deployment-key-2024'; // Change this to match deploy.php
    $requested_key = request('key', '');

    if ($requested_key !== $secret_key) {
        return response('Access denied. Please provide correct key parameter.', 403);
    }

    return redirect('/deploy.php?key=' . $secret_key);
})->name('deploy');

// Help pages (publicly accessible)
Route::prefix('help')->name('help.')->group(function () {
    Route::view('/', 'help.index')->name('index');
    Route::view('/general', 'help.general')->name('general');
    Route::view('/volunteer', 'help.volunteer')->name('volunteer');
    Route::view('/admin', 'help.admin')->name('admin');
    Route::view('/system', 'help.system')->name('system');
});

// System user auth routes
Route::prefix('system')->name('system.')->group(function () {
    Route::middleware('guest:system')->group(function () {
        Route::view('/login', 'auth.system-login')->name('login');
        Route::post('/login', function (Request $request) {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
            if (auth()->guard('system')->attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended(route('system.dashboard'));
            }
            return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
        })->name('login.attempt');
    });

    Route::middleware('auth:system')->group(function () {
        Route::get('/dashboard', function () {
            return view('system.dashboard');
        })->name('dashboard');

        // System settings
        Route::get('/settings/general', [\App\Http\Controllers\System\SettingsController::class, 'general'])->name('settings.general');
        Route::post('/settings/general', [\App\Http\Controllers\System\SettingsController::class, 'saveGeneral'])->name('settings.general.save');
        Route::get('/settings/smtp', [\App\Http\Controllers\System\SettingsController::class, 'smtp'])->name('settings.smtp');
        Route::post('/settings/smtp', [\App\Http\Controllers\System\SettingsController::class, 'saveSmtp'])->name('settings.smtp.save');
        Route::post('/settings/smtp/test', [\App\Http\Controllers\System\SettingsController::class, 'testSmtp'])->name('settings.smtp.test');

        // Front page editor
        Route::get('/frontpage', [\App\Http\Controllers\System\SettingsController::class, 'frontpage'])->name('frontpage');
        Route::post('/frontpage', [\App\Http\Controllers\System\SettingsController::class, 'saveFrontpage'])->name('frontpage.save');

        // ENV manager
        Route::get('/env', [\App\Http\Controllers\System\SettingsController::class, 'env'])->name('env');
        Route::post('/env', [\App\Http\Controllers\System\SettingsController::class, 'saveEnv'])->name('env.save');

        // Cron manager
        Route::get('/cron', [\App\Http\Controllers\System\SettingsController::class, 'cron'])->name('cron');
        Route::post('/cron/run', function() { \Artisan::call('schedule:run'); return back()->with('success', 'Schedule run triggered'); })->name('cron.run');

        // Database Management
        Route::get('/database', [\App\Http\Controllers\System\DatabaseController::class, 'index'])->name('database.index');
        Route::get('/database/tables', [\App\Http\Controllers\System\DatabaseController::class, 'tables'])->name('database.tables');
        Route::get('/database/tables/{table}/data', [\App\Http\Controllers\System\DatabaseController::class, 'tableData'])->name('database.tables.data');
        Route::get('/database/tables/{table}/structure', [\App\Http\Controllers\System\DatabaseController::class, 'tableStructure'])->name('database.tables.structure');
        Route::get('/database/migrations', [\App\Http\Controllers\System\DatabaseController::class, 'migrations'])->name('database.migrations');
        Route::get('/database/seeders', [\App\Http\Controllers\System\DatabaseController::class, 'seeders'])->name('database.seeders');
        Route::post('/database/migrate', [\App\Http\Controllers\System\DatabaseController::class, 'migrate'])->name('database.migrate');
        Route::post('/database/rollback', [\App\Http\Controllers\System\DatabaseController::class, 'rollback'])->name('database.rollback');
        Route::post('/database/seed', [\App\Http\Controllers\System\DatabaseController::class, 'seed'])->name('database.seed');
        Route::post('/database/fresh', [\App\Http\Controllers\System\DatabaseController::class, 'fresh'])->name('database.fresh');
        Route::post('/database/migrate/selected', [\App\Http\Controllers\System\DatabaseController::class, 'migrateSelected'])->name('database.migrate.selected');
        Route::post('/database/migrate/single', [\App\Http\Controllers\System\DatabaseController::class, 'migrateSingle'])->name('database.migrate.single');
        Route::post('/database/rollback/single', [\App\Http\Controllers\System\DatabaseController::class, 'rollbackSingle'])->name('database.rollback.single');
        Route::get('/database/config', [\App\Http\Controllers\System\DatabaseController::class, 'config'])->name('database.config');
        Route::post('/database/config', [\App\Http\Controllers\System\DatabaseController::class, 'updateConfig'])->name('database.config.update');

        // Email Management
        Route::get('/email', [\App\Http\Controllers\System\EmailManagementController::class, 'index'])->name('email.index');
        Route::get('/email/templates', [\App\Http\Controllers\System\EmailManagementController::class, 'templates'])->name('email.templates');
        Route::get('/email/templates/{id}/edit', [\App\Http\Controllers\System\EmailManagementController::class, 'editTemplate'])->name('email.templates.edit');
        Route::put('/email/templates/{id}', [\App\Http\Controllers\System\EmailManagementController::class, 'updateTemplate'])->name('email.templates.update');
        Route::get('/email/settings', [\App\Http\Controllers\System\EmailManagementController::class, 'settings'])->name('email.settings');
        Route::put('/email/settings/{id}', [\App\Http\Controllers\System\EmailManagementController::class, 'updateSetting'])->name('email.settings.update');
        Route::get('/email/notifications', [\App\Http\Controllers\System\EmailManagementController::class, 'notifications'])->name('email.notifications');
        Route::post('/email/retry-failed', [\App\Http\Controllers\System\EmailManagementController::class, 'retryFailed'])->name('email.retry-failed');
        Route::post('/email/test', [\App\Http\Controllers\System\EmailManagementController::class, 'testEmail'])->name('email.test');

        Route::post('/logout', function (Request $request) {
            auth('system')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('system.login');
        })->name('logout');
    });
});

// Auth routes
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
});

// Public donation routes (authenticated)
Route::middleware(['auth', 'role.maintenance'])->group(function () {
    Route::get('/donate', \App\Livewire\Donations\DonationSubmit::class)->name('donate');
    Route::get('/donations/{donation}', \App\Livewire\Donations\DonationDetails::class)->name('donations.show');
    Route::get('/my-impact', \App\Livewire\DonorImpact::class)->name('donor.impact');
});

// Unified wizard routes (for new users)
Route::get('/wizard/{type}', \App\Livewire\UnifiedWizard::class)->name('wizard')->where('type', 'donation|beneficiary');

// Beneficiary request flow routes
Route::get('/request-assistance', \App\Livewire\BeneficiaryRequest::class)->name('beneficiary.submit')->middleware(['auth','role.maintenance']);

// Finance routes (admin only)
Route::middleware(['auth', 'role:SUPER_ADMIN', 'role.maintenance'])->prefix('finance')->name('finance.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Finance\FinanceDashboard::class)->name('dashboard');
    Route::get('/accounts', \App\Livewire\Finance\AccountManagement::class)->name('accounts');
    Route::get('/expenses', \App\Livewire\Finance\ExpenseManagement::class)->name('expenses');
    Route::get('/transfers', \App\Livewire\Finance\AccountTransfer::class)->name('transfers');
    Route::get('/reports', \App\Livewire\Finance\TransactionReports::class)->name('reports');
});

// Protected routes
Route::middleware(['auth', 'role.maintenance', 'role.redirect'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Dashboard\UserDashboard::class)->name('dashboard');

    Route::get('/profile', \App\Livewire\Profile\Profile::class)->name('profile');

    // My donations route for regular users
    Route::get('/my-donations', \App\Livewire\Donations\MyDonations::class)->name('my-donations');
    Route::get('/my-donations/{donation}', \App\Livewire\Donations\DonationDetails::class)->name('my-donations.show');
    Route::get('/my-requests', \App\Livewire\Beneficiaries\MyRequests::class)->name('my-requests');
    Route::get('/my-requests/{beneficiary}', \App\Livewire\Beneficiaries\RequestDetails::class)->name('my-requests.show');

    // Achievements route
    Route::get('/achievements', \App\Livewire\UserAchievements::class)->name('achievements');

    // Logout route
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

// Admin routes
Route::middleware(['auth', 'role:SUPER_ADMIN', 'role.maintenance'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // User Management routes
    Route::get('/users', \App\Livewire\Admin\Users\UsersIndex::class)->name('users.index');
    Route::get('/users/create', \App\Livewire\Admin\Users\UserCreate::class)->name('users.create');
    Route::get('/users/{user}', \App\Livewire\Admin\Users\UserShow::class)->name('users.show');
    Route::get('/users/{user}/edit', \App\Livewire\Admin\Users\UserEdit::class)->name('users.edit');

    // Localization Management routes
    Route::get('/localization', \App\Livewire\Admin\Localization\LocalizationIndex::class)->name('localization.index');
    Route::get('/localization/volunteers', \App\Livewire\Admin\Localization\VolunteerAssignmentManage::class)->name('localization.volunteers');
    Route::get('/localization/countries', \App\Livewire\Admin\Localization\CountryManage::class)->name('localization.countries');
    Route::get('/localization/states', \App\Livewire\Admin\Localization\StateManage::class)->name('localization.states');
    Route::get('/localization/cities', \App\Livewire\Admin\Localization\CityManage::class)->name('localization.cities');

    // Status Management routes
    Route::get('/status-management', \App\Livewire\Admin\StatusManagement::class)->name('status-management');

    // Certificate Management routes
    Route::get('/certificates', \App\Livewire\Admin\CertificateManagement::class)->name('certificates.index');

    // Achievement Management routes
    Route::get('/achievements', \App\Livewire\Admin\AchievementManagement::class)->name('achievements.index');
    Route::get('/achievements/guide', function () {
        return view('admin.achievement-guide');
    })->name('achievements.guide');

    // Login Method Management routes
    Route::get('/login-methods', [\App\Http\Controllers\Admin\LoginMethodController::class, 'index'])->name('login-methods.index');
    Route::put('/login-methods/{loginMethod}', [\App\Http\Controllers\Admin\LoginMethodController::class, 'update'])->name('login-methods.update');
    Route::post('/login-methods/{loginMethod}/toggle', [\App\Http\Controllers\Admin\LoginMethodController::class, 'toggle'])->name('login-methods.toggle');
    Route::get('/login-methods/{loginMethod}', [\App\Http\Controllers\Admin\LoginMethodController::class, 'getSettings'])->name('login-methods.show');

    // Donations Management routes
    Route::get('/donations', \App\Livewire\Admin\Donations\DonationsIndex::class)->name('donations.index');
    Route::get('/donations/create', \App\Livewire\Admin\Donations\DonationCreate::class)->name('donations.create');
    Route::get('/donations/{donation}', \App\Livewire\Admin\Donations\DonationShow::class)->name('donations.show');
    Route::get('/donations/{donation}/edit', \App\Livewire\Admin\Donations\DonationEdit::class)->name('donations.edit');
    Route::get('/donation-history', \App\Livewire\Admin\DonationHistoryIndex::class)->name('donation-history.index');
    Route::get('/donation-history/{beneficiary}', \App\Livewire\Admin\DonationHistory::class)->name('donation-history.show');
    Route::get('/service-donations', \App\Livewire\Admin\ServiceDonationsIndex::class)->name('service-donations.index');

    // Beneficiaries Management routes
    Route::get('/beneficiaries', \App\Livewire\Admin\Beneficiaries\BeneficiariesIndex::class)->name('beneficiaries.index');
    Route::get('/beneficiaries/{beneficiary}', \App\Livewire\Admin\Beneficiaries\BeneficiaryShow::class)->name('beneficiaries.show');
    Route::get('/beneficiaries/{beneficiary}/edit', \App\Livewire\Admin\Beneficiaries\BeneficiaryEdit::class)->name('beneficiaries.edit');
    Route::get('/beneficiaries/{beneficiary}/provide-donation', \App\Livewire\Admin\ProvideDonation::class)->name('beneficiaries.provide-donation');

    // Materialistic Donations Management routes
    Route::get('/materialistic-donations', \App\Livewire\Finance\MaterialisticDonationsManagement::class)->name('materialistic-donations.index');

});

// Volunteer routes
Route::middleware(['auth', 'role:VOLUNTEER', 'role.maintenance'])->prefix('volunteer')->name('volunteer.')->group(function () {
    Route::get('/dashboard', [VolunteerController::class, 'index'])->name('dashboard');

    // Volunteer Donation Management routes
    Route::get('/donations', \App\Livewire\Volunteer\Donations\VolunteerDonations::class)->name('donations.index');
    Route::get('/donations/{donation}', \App\Livewire\Volunteer\Donations\VolunteerDonationShow::class)->name('donations.show');

    // Volunteer Request Management routes
    Route::get('/requests', \App\Livewire\Volunteer\VolunteerRequests::class)->name('requests.index');
    Route::get('/requests/{request}', \App\Livewire\Volunteer\VolunteerRequestShow::class)->name('requests.show');

    // Volunteer Materialistic Donations routes
    Route::get('/materialistic-donations', \App\Livewire\Volunteer\MaterialisticDonations::class)->name('materialistic-donations.index');

    // Volunteer Service Donations routes
    Route::get('/service-donations', \App\Livewire\Volunteer\ServiceDonations::class)->name('service-donations.index');

    // Volunteer Personal Donation routes (volunteers can also make donations)
    Route::get('/donate', \App\Livewire\Volunteer\VolunteerDonationSubmit::class)->name('donate');
    Route::get('/my-donations', \App\Livewire\Volunteer\VolunteerMyDonations::class)->name('my-donations');
    Route::get('/my-donations/{donation}', \App\Livewire\Volunteer\VolunteerDonationDetails::class)->name('my-donations.show');

    // Volunteer Personal Request routes
    Route::get('/my-requests', \App\Livewire\Volunteer\VolunteerMyRequests::class)->name('my-requests');
    Route::get('/my-requests/{beneficiary}', \App\Livewire\Volunteer\VolunteerRequestDetails::class)->name('my-requests.show');
    Route::get('/request-assistance', \App\Livewire\Volunteer\VolunteerBeneficiaryRequest::class)->name('request-assistance');

    // Volunteer Achievements route
    Route::get('/achievements', \App\Livewire\Volunteer\VolunteerAchievements::class)->name('achievements');
});

require __DIR__.'/auth.php';
