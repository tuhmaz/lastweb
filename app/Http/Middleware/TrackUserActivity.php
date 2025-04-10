<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $now = now();

            // Update user's activity status
            $user->update([
                'last_activity' => $now,
                'last_seen' => $now,
                'is_online' => true,
                'status' => 'online'
            ]);
        }

        return $next($request);
    }
}
