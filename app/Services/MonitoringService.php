<?php

namespace App\Services;

use App\Models\VisitorTracking;
use Carbon\Carbon;
use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;



class MonitoringService
{
    protected $geoip;

    public function __construct()
    {
        $this->geoip = App::make('geoip');
    }

    private function getActiveUsers()
{
    try {
        return DB::table('visitors_tracking')
            ->select([
                'ip_address',
                'user_id',
                'url',
                'country',
                'city',
                'browser',
                'os',
                'last_activity'
            ])
            ->where('last_activity', '>=', now()->subMinutes(5))
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'ip_address' => $item->ip_address ?? 'Unknown',
                    'user_id' => $item->user_id ?? 'Guest',
                    'url' => $item->url ?? '-',
                    'country' => $item->country ?? 'Unknown',
                    'city' => $item->city ?? 'Unknown',
                    'browser' => $item->browser ?? 'Unknown',
                    'os' => $item->os ?? 'Unknown',
                    'last_activity' => $item->last_activity ?? now()
                ];
            });
    } catch (\Exception $e) {
        Log::error('Error getting active users: ' . $e->getMessage());
        return collect([]);
    }
}

    public function getVisitorStats()
    {
        $current = VisitorTracking::where('last_activity', '>=', now()->subMinutes(5))->count();

        $previousHour = VisitorTracking::whereBetween('last_activity', [
            now()->subHours(2),
            now()->subHour()
        ])->count();

        $currentHour = VisitorTracking::where('last_activity', '>=', now()->subHour())->count();

        $change = $previousHour > 0
            ? (($currentHour - $previousHour) / $previousHour) * 100
            : 0;

        $history = [];
        for ($i = 23; $i >= 0; $i--) {
            $start = now()->subHours($i);
            $end   = $start->copy()->addHour();

            $count = VisitorTracking::whereBetween('last_activity', [$start, $end])->count();

            $history[] = [
                'timestamp' => $start->timestamp * 1000,
                'count'     => $count
            ];
        }

        return [
            'current' => $current,
            'change'  => round($change, 1),
            'history' => $history
        ];
    }

    public function getRequestStats()
    {
        $stats = VisitorTracking::selectRaw('
            COUNT(*) as total_requests,
            COUNT(DISTINCT ip_address) as unique_visitors,
            COUNT(DISTINCT url) as unique_pages
        ')
            ->where('created_at', '>=', now()->subDay())
            ->first();

        return [
            'total_requests' => $stats->total_requests,
            'unique_visitors' => $stats->unique_visitors,
            'unique_pages' => $stats->unique_pages
        ];
    }

    private function getResponseTimes()
{
    if (Schema::hasColumn('visitors_tracking', 'response_time')) {
        $stats = VisitorTracking::selectRaw('
            AVG(response_time) as avg_response,
            MAX(response_time) as max_response,
            MIN(response_time) as min_response
        ')
            ->where('created_at', '>=', now()->subHour())
            ->first();

        return [
            'average' => round($stats->avg_response ?? 0, 2),
            'maximum' => round($stats->max_response ?? 0, 2),
            'minimum' => round($stats->min_response ?? 0, 2)
        ];
    }

    return [
        'average' => 0,
        'maximum' => 0,
        'minimum' => 0
    ];
}
    public function getUserLocation($ipAddress)
    {
        try {
            $record = $this->geoip->city($ipAddress);

            return [
                'country' => $record->country->name,
                'city' => $record->city->name,
                'latitude' => $record->location->latitude,
                'longitude' => $record->location->longitude,
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Unable to retrieve location data.',
            ];
        }
    }
}