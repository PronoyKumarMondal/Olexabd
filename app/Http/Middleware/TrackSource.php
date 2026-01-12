<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackSource
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('source') && !empty($request->source)) {
            // Validate source is alphanumeric/safe (basic security)
            if (preg_match('/^[a-zA-Z0-9_\-]+$/', $request->source)) {
                // Store in session for attribution
                session(['order_source' => $request->source]);
                \Illuminate\Support\Facades\Log::info("TrackSource: Captured source '{$request->source}' for session " . session()->getId());
            }
        } else {
             // Log if we already have it
             if (session()->has('order_source')) {
                 // \Illuminate\Support\Facades\Log::info("TrackSource: Session retains source '" . session('order_source') . "'");
             }
        }

        return $next($request);
    }
}
