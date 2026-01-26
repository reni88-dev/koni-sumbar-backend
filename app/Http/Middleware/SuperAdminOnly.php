<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminOnly
{
    /**
     * Handle an incoming request.
     * Only allows super admin users to access the route.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!auth()->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Access denied. Super Admin only.'], 403);
        }

        return $next($request);
    }
}
