<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LogRequestActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Determine User
        $user = 'Guest';
        if (auth('admin')->check()) {
            $user = 'Admin: ' . auth('admin')->user()->name . ' (ID: ' . auth('admin')->id() . ')';
        } elseif (auth()->check()) {
            $user = 'Customer: ' . auth()->user()->name . ' (ID: ' . auth()->id() . ')';
        }

        // Log the Request
        Log::info(sprintf(
            "[%s] %s | User: %s | IP: %s | Status: %s | Payload: %s",
            $request->method(),
            $request->fullUrl(),
            $user,
            $request->ip(),
            $response->getStatusCode(),
            json_encode($request->except(['password', 'password_confirmation', 'credit_card'])) // Log input safely
        ));

        return $response;
    }
}
