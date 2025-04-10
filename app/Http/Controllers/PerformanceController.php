<?php

namespace App\Http\Controllers;

use App\Services\SystemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PerformanceController extends Controller
{
    protected $systemService;

    public function __construct(SystemService $systemService)
    {
        $this->systemService = $systemService;
    }

    public function __invoke(Request $request)
    {
        $this->middleware('auth');
    }

    /**
     * عرض صفحة الأداء الرئيسية
     */
    public function index()
    {
        try {
            $systemInfo = $this->systemService->getSystemInfo();
            return view('content.dashboard.performance.index', compact('systemInfo'));
        } catch (\Exception $e) {
            Log::error('Error in performance index view: ' . $e->getMessage());
            return view('content.dashboard.performance.index')->with('error', 'Unable to load system metrics');
        }
    }

    /**
     * الحصول على بيانات المقاييس للتحديث المباشر
     */
    public function getMetrics()
    {
        try {
            $cacheKey = 'system_metrics';
            $cacheDuration = 5; // 5 seconds

            return Cache::remember($cacheKey, $cacheDuration, function () {
                $stats = $this->systemService->getSystemInfo();

                if (!$stats) {
                    throw new \Exception('Failed to retrieve system stats');
                }

                return response()->json([
                    'cpu' => [
                        'usage_percentage' => $stats['cpu']['usage_percentage'] ?? 0,
                        'cores' => $stats['cpu']['cores'] ?? 4,
                        'load_average' => $stats['cpu']['load'][0] ?? 0
                    ],
                    'memory' => [
                        'usage_percentage' => $stats['memory']['usage_percentage'] ?? 0,
                        'used' => $stats['memory']['used'] ?? 0,
                        'total' => $stats['memory']['total'] ?? 0,
                        'free' => $stats['memory']['free'] ?? 0
                    ],
                    'disk' => [
                        'usage_percentage' => $stats['disk']['usage_percentage'] ?? 0,
                        'used' => $stats['disk']['used'] ?? 0,
                        'total' => $stats['disk']['total'] ?? 0,
                        'free' => $stats['disk']['free'] ?? 0
                    ],
                    'last_updated' => now()->toIso8601String()
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Error fetching performance metrics: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch metrics'], 500);
        }
    }

    public function getMetricsData()
    {
        try {
            $stats = $this->systemService->getSystemInfo();
            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Error fetching metrics data: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch metrics data'], 500);
        }
    }
    /**
     * حساب متوسط وقت الاستجابة
     */
    protected function getAverageResponseTime()
    {
        try {
            $key = 'average_response_time';
            $samples = Cache::get($key, []);

            // أخذ عينة جديدة
            $startTime = microtime(true);
            $this->systemService->getSystemStats();
            $endTime = microtime(true);

            $currentSample = ($endTime - $startTime) * 1000; // تحويل إلى ميلي ثانية

            // إضافة العينة الجديدة وحفظ آخر 10 عينات
            array_push($samples, $currentSample);
            if (count($samples) > 10) {
                array_shift($samples);
            }

            Cache::put($key, $samples, now()->addMinutes(5));

            // حساب المتوسط
            return round(array_sum($samples) / count($samples), 2);
        } catch (\Exception $e) {
            Log::error('Error calculating response time: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * حساب نسبة نجاح ذاكرة التخزين المؤقت
     */
    protected function getCacheHitRatio()
    {
        try {
            $hits = Cache::get('cache_hits', 0);
            $misses = Cache::get('cache_misses', 0);

            $total = $hits + $misses;
            if ($total === 0) return 0;

            return round(($hits / $total) * 100, 2);
        } catch (\Exception $e) {
            Log::error('Error calculating cache hit ratio: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * حساب حجم ذاكرة التخزين المؤقت
     */
    protected function getCacheSize()
    {
        try {
            // محاولة الحصول على حجم الذاكرة المؤقتة
            $size = 0;
            if (function_exists('shell_exec')) {
                $cacheDir = storage_path('framework/cache');
                $output = shell_exec("du -sb $cacheDir 2>/dev/null");
                if ($output) {
                    $size = (int) explode("\t", $output)[0];
                }
            }

            return $this->formatBytes($size);
        } catch (\Exception $e) {
            Log::error('Error calculating cache size: ' . $e->getMessage());
            return '0 B';
        }
    }

    /**
     * تنسيق وقت التشغيل
     */
    protected function formatUptime($seconds)
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        $parts = [];
        if ($days > 0) $parts[] = "{$days}d";
        if ($hours > 0) $parts[] = "{$hours}h";
        if ($minutes > 0) $parts[] = "{$minutes}m";

        return implode(' ', $parts) ?: '0m';
    }

    /**
     * تنسيق الحجم بالبايت
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
