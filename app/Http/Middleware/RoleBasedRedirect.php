<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleBasedRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && $request->is('dashboard')) {
            $user = Auth::user();
            
            if ($user->hasRole('SUPER_ADMIN')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('VOLUNTEER')) {
                return redirect()->route('volunteer.dashboard');
            } else {
                // Normal users (no specific role) - let them access the dashboard
                // Don't redirect, let the request continue to the dashboard
            }
        }

        return $next($request);
    }
}
