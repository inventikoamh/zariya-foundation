<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // Check roles using direct database query to avoid getMorphClass errors
        $userRoles = \DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->pluck('roles.name')
            ->toArray();

        // Admins
        if (in_array('SUPER_ADMIN', $userRoles)) {
            if (SystemSetting::get('maintenance_admin', '0') === '1') {
                return response()->view('system.maintenance', [
                    'title' => 'Admin Maintenance',
                    'message' => 'The admin panel is under maintenance. Please try again later.'
                ], 503);
            }
            return $next($request);
        }

        // Volunteers
        if (in_array('VOLUNTEER', $userRoles)) {
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


