<?php

namespace App\Http\Middleware;

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
            $maintenanceAdmin = DB::table('system_settings')
                ->where('key', 'maintenance_admin')
                ->value('value') ?? '0';
            
            if ($maintenanceAdmin === '1') {
                return response('
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Admin Maintenance</title>
                        <meta http-equiv="refresh" content="60">
                    </head>
                    <body style="font-family: Arial, sans-serif; text-align: center; padding: 50px; background-color: #f3f4f6;">
                        <h1 style="color: #374151; margin-bottom: 20px;">Admin Maintenance</h1>
                        <p style="color: #6b7280;">The admin panel is under maintenance. Please try again later.</p>
                    </body>
                    </html>
                ', 503);
            }
            return $next($request);
        }

        // Volunteers
        if (in_array('VOLUNTEER', $userRoles)) {
            $maintenanceVolunteer = DB::table('system_settings')
                ->where('key', 'maintenance_volunteer')
                ->value('value') ?? '0';
            
            if ($maintenanceVolunteer === '1') {
                return response('
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Volunteer Maintenance</title>
                        <meta http-equiv="refresh" content="60">
                    </head>
                    <body style="font-family: Arial, sans-serif; text-align: center; padding: 50px; background-color: #f3f4f6;">
                        <h1 style="color: #374151; margin-bottom: 20px;">Volunteer Maintenance</h1>
                        <p style="color: #6b7280;">Volunteer access is under maintenance. Please try again later.</p>
                    </body>
                    </html>
                ', 503);
            }
            return $next($request);
        }

        // General users
        $maintenanceUser = DB::table('system_settings')
            ->where('key', 'maintenance_user')
            ->value('value') ?? '0';
        
        if ($maintenanceUser === '1') {
            return response('
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Maintenance</title>
                    <meta http-equiv="refresh" content="60">
                </head>
                <body style="font-family: Arial, sans-serif; text-align: center; padding: 50px; background-color: #f3f4f6;">
                    <h1 style="color: #374151; margin-bottom: 20px;">Maintenance</h1>
                    <p style="color: #6b7280;">The application is under maintenance. Please try again later.</p>
                </body>
                </html>
            ', 503);
        }

        return $next($request);
    }
}


