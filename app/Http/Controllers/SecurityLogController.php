<?php

namespace App\Http\Controllers;

use App\Models\SecurityLog;
use App\Services\SecurityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SecurityLogController extends Controller
{
    protected $eventTypes = [
        'login_failed',
        'suspicious_activity',
        'blocked_access',
        'unauthorized_access',
        'password_reset',
        'account_locked',
        'permission_change'
    ];

    protected $severityLevels = [
        'info',
        'warning',
        'danger',
        'critical'
    ];

    protected $securityLogService;

    public function __construct(SecurityLogService $securityLogService)
    {
        $this->securityLogService = $securityLogService;
    }

    /**
     * عرض لوحة تحليل سجلات الأمان.
     */
    public function index()
    {
        $stats = $this->securityLogService->getQuickStats();

        $recentLogs = SecurityLog::with('user')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($log) {
                $log->event_type_color = $log->getEventTypeColorAttribute();
                return $log;
            });

        return view('content.dashboard.security.index', compact('stats', 'recentLogs'));
    }

    /**
     * Display the security logs list.
     */
    public function logs(Request $request)
    {
        $query = SecurityLog::with('user')
            ->when($request->filled('event_type'), function ($q) use ($request) {
                return $q->where('event_type', $request->event_type);
            })
            ->when($request->filled('severity'), function ($q) use ($request) {
                return $q->where('severity', $request->severity);
            })
            ->when($request->filled('ip'), function ($q) use ($request) {
                return $q->where('ip_address', 'like', "%{$request->ip}%");
            })
            ->when($request->filled('is_resolved'), function ($q) use ($request) {
                return $q->where('is_resolved', $request->is_resolved === 'true');
            })
            ->when($request->filled('date_from'), function ($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->date_to);
            });

        $logs = $query->latest()->paginate(15);
        $logs->getCollection()->transform(function ($log) {
            $log->event_type_color = $this->getEventTypeColor($log->event_type);
            return $log;
        });

        return view('content.dashboard.security.logs', [
            'logs' => $logs,
            'eventTypes' => $this->eventTypes,
            'severityLevels' => $this->severityLevels
        ]);
    }

    /**
     * Display security analytics.
     */
    public function analytics(Request $request)
    {
        // Calculate date range
        $range = $request->get('range', 'week');
        $startDate = match ($range) {
            'today' => now()->startOfDay(),
            'week' => now()->subDays(7)->startOfDay(),
            'month' => now()->subDays(30)->startOfDay(),
            'custom' => Carbon::parse($request->get('start_date'))->startOfDay(),
            default => now()->subDays(7)->startOfDay(),
        };
        $endDate = $range === 'custom' 
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : now();

        // Calculate security score based on risk scores and resolution times
        $stats = SecurityLog::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                AVG(risk_score) as avg_risk,
                COUNT(*) as total_events,
                COUNT(CASE WHEN is_resolved = 1 THEN 1 END) as resolved_events,
                AVG(CASE WHEN resolved_at IS NOT NULL 
                    THEN TIMESTAMPDIFF(MINUTE, created_at, resolved_at) 
                    ELSE NULL END) as avg_resolution_time
            ')
            ->first();

        $securityScore = $this->calculateSecurityScore(
            $stats->avg_risk ?? 0,
            $stats->total_events,
            $stats->resolved_events,
            $stats->avg_resolution_time ?? 0
        );

        // Calculate score change
        $previousStats = SecurityLog::whereBetween('created_at', [$startDate->copy()->subDays($endDate->diffInDays($startDate)), $startDate])
            ->selectRaw('
                AVG(risk_score) as avg_risk,
                COUNT(*) as total_events,
                COUNT(CASE WHEN is_resolved = 1 THEN 1 END) as resolved_events,
                AVG(CASE WHEN resolved_at IS NOT NULL 
                    THEN TIMESTAMPDIFF(MINUTE, created_at, resolved_at) 
                    ELSE NULL END) as avg_resolution_time
            ')
            ->first();

        $previousScore = $this->calculateSecurityScore(
            $previousStats->avg_risk ?? 0,
            $previousStats->total_events,
            $previousStats->resolved_events,
            $previousStats->avg_resolution_time ?? 0
        );

        $scoreChange = $securityScore - $previousScore;

        // Get event distribution
        $eventDistribution = SecurityLog::whereBetween('created_at', [$startDate, $endDate])
            ->select('event_type', DB::raw('count(*) as count'))
            ->groupBy('event_type')
            ->orderByDesc('count')
            ->get();

        // Get events timeline
        $eventsTimeline = SecurityLog::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get();

        // Get top attacked routes with risk assessment
        $topAttackedRoutes = SecurityLog::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('route')
            ->select(
                'route',
                DB::raw('count(*) as count'),
                DB::raw('MAX(created_at) as last_attack'),
                DB::raw('AVG(risk_score) as avg_risk'),
                DB::raw('MAX(occurrence_count) as max_occurrences')
            )
            ->groupBy('route')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(function ($route) {
                $route->last_attack = Carbon::parse($route->last_attack);
                return $route;
            });

        // Get geographic distribution with attack patterns
        $geoDistribution = SecurityLog::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('country_code')
            ->select(
                'country_code',
                'city',
                DB::raw('count(*) as events_count'),
                DB::raw('COUNT(DISTINCT ip_address) as unique_ips'),
                DB::raw('AVG(risk_score) as avg_risk'),
                DB::raw('COUNT(DISTINCT attack_type) as attack_types_count')
            )
            ->groupBy('country_code', 'city')
            ->orderByDesc('events_count')
            ->limit(5)
            ->get();

        // Get response time analysis
        $avgResponseTime = $stats->avg_resolution_time ? round($stats->avg_resolution_time) : 0;
        $resolutionRate = $stats->total_events > 0 
            ? round(($stats->resolved_events / $stats->total_events) * 100)
            : 100;
        $pendingIssues = $stats->total_events - $stats->resolved_events;

        // Get response time trend
        $responseTimeTrend = SecurityLog::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('resolved_at')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) as avg_time'),
                DB::raw('AVG(risk_score) as avg_risk')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get();

        return view('content.dashboard.security.analytics', compact(
            'securityScore',
            'scoreChange',
            'eventDistribution',
            'eventsTimeline',
            'topAttackedRoutes',
            'geoDistribution',
            'avgResponseTime',
            'resolutionRate',
            'pendingIssues',
            'responseTimeTrend'
        ));
    }

    /**
     * Mark a security log as resolved.
     */
    public function resolve(SecurityLog $log, Request $request)
    {
        $request->validate([
            'notes' => 'required|string'
        ]);

        $log->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
            'resolution_notes' => $request->notes
        ]);

        return response()->json([
            'message' => __('Security log marked as resolved successfully.')
        ]);
    }

    /**
     * Delete a security log.
     */
    public function destroy(SecurityLog $log)
    {
        $log->delete();

        return back()->with('success', __('Security log deleted successfully.'));
    }

    /**
     * Export security logs to CSV.
     */
    public function export(Request $request)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="security-logs.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $logs = SecurityLog::with('user')
            ->when($request->filled('event_type'), function ($q) use ($request) {
                return $q->where('event_type', $request->event_type);
            })
            ->when($request->filled('severity'), function ($q) use ($request) {
                return $q->where('severity', $request->severity);
            })
            ->when($request->filled('is_resolved'), function ($q) use ($request) {
                return $q->where('is_resolved', $request->is_resolved === 'true');
            })
            ->latest()
            ->get();

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Time',
                'IP Address',
                'Event Type',
                'Description',
                'User',
                'Route',
                'Severity',
                'Status',
                'Risk Score',
                'Country',
                'City',
                'Attack Type',
                'Occurrences'
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at,
                    $log->ip_address,
                    $log->event_type,
                    $log->description,
                    $log->user ? $log->user->name : 'System',
                    $log->route,
                    $log->severity,
                    $log->is_resolved ? 'Resolved' : 'Pending',
                    $log->risk_score,
                    $log->country_code,
                    $log->city,
                    $log->attack_type,
                    $log->occurrence_count
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Display the list of blocked IP addresses.
     */
    public function blockedIps()
    {
        // Get blocked IPs with pagination
        $blockedLogs = SecurityLog::where('event_type', 'blocked_access')
            ->select([
                'ip_address',
                'country_code',
                'city',
                'attack_type',
                DB::raw('MAX(created_at) as last_attempt'),
                DB::raw('COUNT(*) as attempts_count'),
                DB::raw('MAX(risk_score) as max_risk_score')
            ])
            ->groupBy('ip_address', 'country_code', 'city', 'attack_type')
            ->orderBy('last_attempt', 'desc')
            ->paginate(10);

        // Calculate statistics
        $totalBlocked = SecurityLog::where('event_type', 'blocked_access')
            ->distinct('ip_address')
            ->count();

        $highRiskCount = SecurityLog::where('event_type', 'blocked_access')
            ->where('risk_score', '>=', 75)
            ->distinct('ip_address')
            ->count();

        $recentlyBlocked = SecurityLog::where('event_type', 'blocked_access')
            ->where('created_at', '>=', now()->subDay())
            ->distinct('ip_address')
            ->count();

        $avgRiskScore = (int) SecurityLog::where('event_type', 'blocked_access')
            ->avg('risk_score');

        return view('content.dashboard.security.blocked-ips', compact(
            'blockedLogs',
            'totalBlocked',
            'highRiskCount',
            'recentlyBlocked',
            'avgRiskScore'
        ));
    }

    /**
     * Display trusted IPs list.
     */
    public function trustedIps()
    {
        $trustedLogs = SecurityLog::where('event_type', 'trusted_access')
            ->select('ip_address',
                    DB::raw('MAX(created_at) as last_access'),
                    DB::raw('COUNT(*) as access_count'),
                    'country_code',
                    'city',
                    DB::raw('GROUP_CONCAT(DISTINCT user_id) as users')
            )
            ->groupBy('ip_address', 'country_code', 'city')
            ->orderByDesc('last_access')
            ->paginate(15);

        return view('content.dashboard.security.trusted-ips', [
            'trustedLogs' => $trustedLogs,
            'totalTrusted' => SecurityLog::where('event_type', 'trusted_access')
                                ->distinct('ip_address')
                                ->count()
        ]);
    }

    /**
     * Block an IP address.
     */
    public function blockIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'required|string|max:255'
        ]);

        // Create a security log entry for blocking
        SecurityLog::create([
            'ip_address' => $request->ip_address,
            'event_type' => 'blocked_access',
            'description' => $request->reason,
            'user_id' => Auth::id(),
            'severity' => 'warning',
            'risk_score' => 75, // Default risk score for manually blocked IPs
            'is_resolved' => false
        ]);

        return response()->json([
            'message' => __('IP address has been blocked successfully'),
            'status' => 'success'
        ]);
    }

    /**
     * Trust an IP address.
     */
    public function trustIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'required|string|max:255'
        ]);

        // Create a security log entry for trusting
        SecurityLog::create([
            'ip_address' => $request->ip_address,
            'event_type' => 'trusted_access',
            'description' => $request->reason,
            'user_id' => Auth::id(),
            'severity' => 'info',
            'risk_score' => 0, // Trusted IPs have 0 risk score
            'is_resolved' => true
        ]);

        return response()->json([
            'message' => __('IP address has been added to trusted list successfully'),
            'status' => 'success'
        ]);
    }

    /**
     * Remove IP from blocked list.
     */
    public function unblockIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip'
        ]);

        // Update all blocked entries for this IP
        SecurityLog::where('ip_address', $request->ip_address)
            ->where('event_type', 'blocked_access')
            ->update([
                'is_resolved' => true,
                'resolved_at' => now(),
                'resolved_by' => Auth::id(),
                'resolution_notes' => 'IP manually unblocked'
            ]);

        return response()->json([
            'message' => __('IP address has been unblocked successfully'),
            'status' => 'success'
        ]);
    }

    /**
     * Remove IP from trusted list.
     */
    public function untrustIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip'
        ]);

        // Update all trusted entries for this IP
        SecurityLog::where('ip_address', $request->ip_address)
            ->where('event_type', 'trusted_access')
            ->update([
                'is_resolved' => false,
                'resolved_at' => null,
                'resolved_by' => null,
                'resolution_notes' => 'IP manually removed from trusted list'
            ]);

        return response()->json([
            'message' => __('IP address has been removed from trusted list successfully'),
            'status' => 'success'
        ]);
    }

    /**
     * Display detailed information about an IP address.
     */
    public function ipDetails($ip)
    {
        // Get all logs for this IP
        $logs = SecurityLog::where('ip_address', $ip)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get summary statistics
        $stats = [
            'first_seen' => $logs->last()->created_at ?? null,
            'last_seen' => $logs->first()->created_at ?? null,
            'total_events' => $logs->count(),
            'event_types' => $logs->groupBy('event_type')
                ->map(fn($group) => $group->count()),
            'risk_scores' => [
                'current' => $logs->first()->risk_score ?? 0,
                'average' => $logs->avg('risk_score'),
                'max' => $logs->max('risk_score')
            ],
            'locations' => $logs->groupBy('country_code')
                ->map(fn($group) => [
                    'count' => $group->count(),
                    'cities' => $group->pluck('city')->unique()->filter()->values()
                ]),
            'user_agents' => $logs->pluck('user_agent')->unique()->filter()->values(),
            'routes' => $logs->pluck('route')->unique()->filter()->values(),
            'users' => $logs->pluck('user.name')->unique()->filter()->values()
        ];

        return view('content.dashboard.security.partials.ip-details', compact('logs', 'stats', 'ip'));
    }

    /**
     * Calculate security score based on various metrics.
     */
    protected function calculateSecurityScore($avgRisk, $totalEvents, $resolvedEvents, $avgResolutionTime)
    {
        if ($totalEvents === 0) {
            return 100;
        }

        // Risk score component (30%)
        $riskScore = max(0, 100 - ($avgRisk * 10));
        
        // Resolution rate component (40%)
        $resolutionRate = ($resolvedEvents / $totalEvents) * 100;
        
        // Response time component (30%)
        $responseScore = max(0, 100 - (($avgResolutionTime / 120) * 100));

        return round(
            ($riskScore * 0.3) +
            ($resolutionRate * 0.4) +
            ($responseScore * 0.3)
        );
    }

    /**
     * Get color for event type.
     */
    protected function getEventTypeColor($eventType)
    {
        return match ($eventType) {
            'login_failed' => 'danger',
            'suspicious_activity' => 'warning',
            'blocked_access' => 'danger',
            'unauthorized_access' => 'danger',
            'password_reset' => 'info',
            'account_locked' => 'warning',
            'permission_change' => 'info',
            default => 'secondary'
        };
    }
}
