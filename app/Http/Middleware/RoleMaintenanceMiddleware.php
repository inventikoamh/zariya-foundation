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
        // Completely disable middleware to isolate the getMorphClass error
        return $next($request);
    }
}


