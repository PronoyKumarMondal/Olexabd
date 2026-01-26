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
            // If email is NOT verified
            if (!Auth::user()->hasVerifiedEmail()) {
                // Allow access only to verification routes and logout
                if (!$request->routeIs('verification.*') && 
                    !$request->routeIs('logout')) {
                    return redirect()->route('verification.notice');
                }
            }
        }

        return $next($request);
    }
}
