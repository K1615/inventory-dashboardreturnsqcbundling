<?php

namespace App\Http\Middleware;

use App\Support\Roles;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoleSelected
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Roles::isLoggedIn()) {
            if ($request->expectsJson() || $request->is('inventory/api/*') || $request->is('api/*') || $request->is('warehouse-layout/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in to continue.',
                ], 401);
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
