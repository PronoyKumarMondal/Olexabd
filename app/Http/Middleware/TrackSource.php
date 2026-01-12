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

        // Logic: Custom 'source' param > UTM Source > null
        $finalSource = $source ?? $utmSource;

        if ($finalSource && !empty($finalSource)) {
            // Append Campaign Name if available for better detail (e.g. "facebook_ads (WinterSale)")
            if ($utmCampaign) {
                $finalSource .= " ({$utmCampaign})";
            } elseif ($utmMedium) {
                 $finalSource .= " ({$utmMedium})";
            }

            // Validate source is alphanumeric/safe (allow parenthesis/spaces for campaign)
            if (preg_match('/^[a-zA-Z0-9_\-\s\(\)]+$/', $finalSource)) {
                session(['order_source' => $finalSource]);
            }
        }

        return $next($request);
    }
}
