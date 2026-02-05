<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForceEmailVerification
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check if user is logged in
        if (Auth::check()) {
            // Check FRESH user from DB (in case they verified in another tab)
            if (!Auth::user()->fresh()->hasVerifiedEmail()) {
                // Allow access only to verification routes and logout
                // Allow access to verification routes, phone verification, and logout
                if (!$request->routeIs('verification.*') && 
                    !$request->routeIs('auth.phone.*') &&
                    !$request->routeIs('logout')) {
                    return redirect()->route('verification.notice');
                }
            }
        }

        return $next($request);
    }
}
