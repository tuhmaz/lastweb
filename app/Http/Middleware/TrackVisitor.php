<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\VisitorTracking;
use Illuminate\Support\Facades\Auth;
use Stevebauman\Location\Facades\Location;

class TrackVisitor
{
    public function handle(Request $request, Closure $next)
    {
        $ipAddress = $request->ip();
        
        // Get location information
        $position = Location::get($ipAddress);
        
        // Update or create visitor tracking record
        $visitor = VisitorTracking::firstOrCreate(
            ['ip_address' => $ipAddress],
            [
                'user_agent' => $request->userAgent(),
                'country' => $position ? $position->countryName : null,
                'city' => $position ? $position->cityName : null,
                'user_id' => Auth::id(),
                'last_activity' => now()
            ]
        );

        // Always update last_activity
        $visitor->update(['last_activity' => now()]);

        // Track page visit
        $visitor->pageVisits()->create([
            'page_url' => $request->fullUrl()
        ]);

        return $next($request);
    }
}
