<?php

namespace App\Services;

use App\Models\SecurityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SecurityLogService
{
    /**
     * تحليل النشاط المشبوه وحساب درجة الخطورة.
     *
     * @param SecurityLog $log
     * @return int
     */
    public function analyzeSuspiciousActivity(SecurityLog $log): int
    {
        $score = $this->calculateIpRiskScore($log);
        $score += $this->calculateFailedLoginRiskScore($log);
        $score += $this->calculateSensitiveRouteRiskScore($log);

        $log->risk_score = min($score, 100);
        $log->save();

        return $score;
    }

    /**
     * حساب درجة الخطورة بناءً على تكرار IP.
     *
     * @param SecurityLog $log
     * @return int
     */
    protected function calculateIpRiskScore(SecurityLog $log): int
    {
        $ipCount = SecurityLog::where('ip_address', $log->ip_address)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        $score = 0;
        if ($ipCount > 10) $score += 30;
        if ($ipCount > 50) $score += 50;

        return $score;
    }

    /**
     * حساب درجة الخطورة بناءً على محاولات تسجيل الدخول الفاشلة.
     *
     * @param SecurityLog $log
     * @return int
     */
    protected function calculateFailedLoginRiskScore(SecurityLog $log): int
    {
        if ($log->event_type !== SecurityLog::EVENT_TYPES['LOGIN_FAILED']) {
            return 0;
        }

        $failedAttempts = SecurityLog::where('ip_address', $log->ip_address)
            ->where('event_type', SecurityLog::EVENT_TYPES['LOGIN_FAILED'])
            ->where('created_at', '>=', now()->subHours(1))
            ->count();

        $score = 0;
        if ($failedAttempts > 5) $score += 40;
        if ($failedAttempts > 20) $score += 60;

        return $score;
    }

    /**
     * حساب درجة الخطورة بناءً على الوصول إلى مسارات حساسة.
     *
     * @param SecurityLog $log
     * @return int
     */
    protected function calculateSensitiveRouteRiskScore(SecurityLog $log): int
    {
        if (str_contains($log->route, 'admin') || str_contains($log->route, 'api')) {
            return 20;
        }

        return 0;
    }

    /**
     * تنظيف السجلات القديمة.
     */
    public function cleanOldRecords(): void
    {
        // احتفظ بالسجلات الخطيرة لمدة سنة
        SecurityLog::where('severity', '!=', SecurityLog::SEVERITY_LEVELS['CRITICAL'])
            ->where('created_at', '<=', now()->subMonths(6))
            ->chunk(1000, function ($logs) {
                $logs->each->delete();
            });

        // احتفظ بالسجلات العادية لمدة 6 أشهر
        SecurityLog::where('severity', SecurityLog::SEVERITY_LEVELS['INFO'])
            ->where('created_at', '<=', now()->subMonths(3))
            ->chunk(1000, function ($logs) {
                $logs->each->delete();
            });
    }

    /**
     * الحصول على إحصائيات سريعة.
     *
     * @return array
     */
    public function getQuickStats(): array
    {
        $cacheKey = 'security_logs_stats';
    
        return Cache::remember($cacheKey, now()->addMinutes(15), function () {
            return [
                'total_events' => SecurityLog::count(),
                'critical_events' => SecurityLog::where('severity', SecurityLog::SEVERITY_LEVELS['CRITICAL'])->count(),
                'unresolved_issues' => SecurityLog::where('is_resolved', false)->count(),
                'recent_suspicious' => SecurityLog::where('event_type', SecurityLog::EVENT_TYPES['SUSPICIOUS_ACTIVITY'])
                    ->where('created_at', '>=', now()->subDay())
                    ->count(),
                'blocked_ips' => SecurityLog::where('event_type', 'blocked_access')
                    ->distinct('ip_address')
                    ->count(),
                'top_attacked_routes' => SecurityLog::select('route')
                    ->where('severity', '>=', SecurityLog::SEVERITY_LEVELS['WARNING'])
                    ->groupBy('route')
                    ->orderByRaw('COUNT(*) DESC')
                    ->limit(5)
                    ->get(),
            ];
        });
    }
 
}