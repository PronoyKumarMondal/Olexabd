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
        $source = $request->get('source');
        $utmSource = $request->get('utm_source');
        $utmMedium = $request->get('utm_medium');
        $utmCampaign = $request->get('utm_campaign');

        // 1. Determine Platform (source or utm_source)
        // User priority: Custom 'source' > UTM Source
        $platform = $source ?? $utmSource;

        if ($platform && !empty($platform)) {
            // Validate: Allow alphanumeric + dashes/underscores/spaces
            if (preg_match('/^[a-zA-Z0-9_\-\s]+$/', $platform)) {
                session(['order_platform' => strtolower($platform)]);
            }
        }

        // 2. Determine Campaign (traffic_source)
        // Priority: utm_campaign > utm_medium
        $campaign = $utmCampaign ?? $utmMedium;
        
        if ($campaign && !empty($campaign)) {
             if (preg_match('/^[a-zA-Z0-9_\-\s\(\)]+$/', $campaign)) {
                session(['order_campaign' => $campaign]);
            }
        }
        
        return $next($request);
    }
}
