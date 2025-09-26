<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMaintenanceMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Never block system guard users
        if (auth('system')->check()) {
            return $next($request);
        }

        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Admins
        if ($user->hasRole('SUPER_ADMIN')) {
            if (SystemSetting::get('maintenance_admin', '0') === '1') {
                return response()->view('system.maintenance', [
                    'title' => 'Admin Maintenance',
                    'message' => 'The admin panel is under maintenance. Please try again later.'
                ], 503);
            }
            return $next($request);
        }

        // Volunteers
        if ($user->hasRole('VOLUNTEER')) {
            if (SystemSetting::get('maintenance_volunteer', '0') === '1') {
                return response()->view('system.maintenance', [
                    'title' => 'Volunteer Maintenance',
                    'message' => 'Volunteer access is under maintenance. Please try again later.'
                ], 503);
            }
            return $next($request);
        }

        // General users
        if (SystemSetting::get('maintenance_user', '0') === '1') {
            return response()->view('system.maintenance', [
                'title' => 'Maintenance',
                'message' => 'The application is under maintenance. Please try again later.'
            ], 503);
        }

        return $next($request);
    }
}


