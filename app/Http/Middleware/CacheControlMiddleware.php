<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheControlMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Check if the request is for the dashboard
        if (str_starts_with($request->path(), 'dashboard')) {
            // Prevent browser caching for dashboard pages
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

            // Track cache keys for this section
            if (count($request->segments()) > 1) {
                $section = $request->segment(2); // Get the section name (news, categories, etc.)
                $cacheKey = "dashboard_{$section}";
                
                // Store the cache key for later cleanup
                $dashboardKeys = Cache::get('dashboard_cache_keys', []);
                if (!in_array($cacheKey, $dashboardKeys)) {
                    $dashboardKeys[] = $cacheKey;
                    Cache::forever('dashboard_cache_keys', $dashboardKeys);
                }
            }
        }

        return $response;
    }
}
