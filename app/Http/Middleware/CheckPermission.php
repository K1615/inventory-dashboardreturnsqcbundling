<?php

namespace App\Http\Middleware;

use App\Support\Roles;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Usage in routes: ->middleware('permission:create_po')
     * This is the real enforcement layer — it runs regardless of whether
     * the button that triggered the request was disabled in the UI.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! Roles::can($permission)) {
            $label = Roles::label();

            if ($request->expectsJson() || $request->is('inventory/api/*') || $request->is('api/*') || $request->is('warehouse-layout/*')) {
                return response()->json([
                    'success' => false,
                    'message' => "Your role ({$label}) does not have permission to do this. This action requires Manager or Admin access.",
                ], 403);
            }

            abort(403, "Your role ({$label}) does not have permission to do this.");
        }

        return $next($request);
    }
}
