<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\VisitorService;
use App\Models\VisitorTracking;
use Illuminate\Support\Facades\Log;

class VisitorTrackingMiddleware
{
    protected $visitorService;

    public function __construct(VisitorService $visitorService)
    {
        $this->visitorService = $visitorService;
    }

    public function handle($request, Closure $next)
    {
        $startTime = microtime(true);
        $response = $next($request);
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;

        try {
            $ip = $request->ip() ?? '127.0.0.1';
            $userAgent = $request->header('User-Agent', 'Unknown');

            // تحليل الـUser-Agent
            $analysis = $this->visitorService->analyzeUserAgent($userAgent);

            // جلب بيانات الموقع الجغرافي
            $geoData = $this->visitorService->getGeoDataFromIP($ip);

            // تحديد نوع المتصفح ونظام التشغيل
            $browser = $analysis['isBot']
                ? ($analysis['botInfo']['name'] ?? 'Bot')
                : ($analysis['client']['name'] ?? 'Unknown');

            $os = $analysis['isBot']
                ? 'Bot/Spider'
                : ($analysis['os']['name'] ?? 'Unknown');

            // حفظ أو تحديث سجل الزائر
            VisitorTracking::updateOrCreate(
                [
                    'ip_address' => $ip,
                    'user_id'    => auth()->id(),
                ],
                [
                    'user_agent'    => $userAgent, // إضافة حقل user_agent
                    'url'           => $request->fullUrl(),
                    'country'       => $geoData['country'],
                    'city'          => $geoData['city'],
                    'browser'       => $browser,
                    'os'            => $os,
                    'latitude'      => $geoData['lat'],
                    'longitude'     => $geoData['lon'],
                    'device'        => $analysis['device'] ?? null,
                    'brand'         => $analysis['brand'] ?? null,
                    'model'         => $analysis['model'] ?? null,
                    'last_activity' => now(),
                    'response_time' => $responseTime,
                    'status_code'   => $response->getStatusCode(), // إضافة كود الاستجابة
                ]
            );
        } catch (\Exception $e) {
            Log::error('Visitor tracking error: ' . $e->getMessage());
        }

        return $response;
    }
}