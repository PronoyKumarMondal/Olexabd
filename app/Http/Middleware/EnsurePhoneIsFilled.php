<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneIsFilled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && empty(Auth::user()->phone)) {
            // Prevent infinite loop if already on verify page
            if (!$request->routeIs('auth.phone.*') && !$request->routeIs('logout')) {
                return redirect()->route('auth.phone.form');
            }
        }

        return $next($request);
    }
}
