<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API requests, don't redirect - return null to trigger JSON error response
        if ($request->expectsJson() || $request->is("api/*")) {
            return null;
        }

        // For web requests, redirect to login (if route exists)
        return route("login");
    }

    /**
     * Handle unauthenticated users for API requests
     */
    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson() || $request->is("api/*")) {
            abort(
                response()->json(
                    [
                        "message" =>
                            "Unauthenticated. Please login to continue.",
                        "error" => "authentication_required",
                    ],
                    401,
                ),
            );
        }

        return parent::unauthenticated($request, $guards);
    }
}
