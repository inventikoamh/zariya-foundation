<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Http\Middleware\RoleMaintenanceMiddleware;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register route middleware alias
        app('router')->aliasMiddleware('role.maintenance', RoleMaintenanceMiddleware::class);

        // Define morph maps for Livewire compatibility (excluding User to avoid Spatie permissions conflicts)
        Relation::morphMap([
            'donation' => \App\Models\Donation::class,
            'beneficiary' => \App\Models\Beneficiary::class,
            'remark' => \App\Models\Remark::class,
            'achievement' => \App\Models\Achievement::class,
            'user_achievement' => \App\Models\UserAchievement::class,
            'volunteer_assignment' => \App\Models\VolunteerAssignment::class,
        ]);
    }
}
