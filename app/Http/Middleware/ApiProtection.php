<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ApiProtection
{
    /**
     * حماية واجهة API من الوصول غير المصرح به
     * يتحقق من وجود مفتاح API صالح ويسمح فقط للتطبيقات المحمولة بالوصول
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $userAgent = $request->header('User-Agent');
        $apiKey = $request->header('X-API-KEY');
        
        // الحصول على مفتاح API من ملف التكوين
        $expectedApiKey = Config::get('api_keys.key');
        
        // الحصول على قائمة العملاء المسموح بهم من ملف التكوين
        $allowedClients = Config::get('api_keys.allowed_clients', [
            'Flutter', 'Dart', 'Mobile', 'Android', 'iPhone', 'iPad', 'Windows Phone', 'PostmanRuntime'
        ]);
        
        // التحقق مما إذا كان يجب تسجيل محاولات الوصول غير المصرح بها
        $logUnauthorizedAttempts = Config::get('api_keys.security.log_unauthorized_attempts', true);
        
        // التحقق مما إذا كان يجب التحقق من نوع العميل
        $checkClientType = Config::get('api_keys.security.check_client_type', true);

        // التحقق من API Key
        if (!$apiKey || $apiKey !== $expectedApiKey) {
            if ($logUnauthorizedAttempts) {
                Log::warning('محاولة وصول غير مصرح بها إلى API - مفتاح API غير صالح', [
                    'ip' => $request->ip(),
                    'user_agent' => $userAgent,
                    'path' => $request->path()
                ]);
            }
            
            return response()->json([
                'status' => false,
                'message' => 'مفتاح API غير صالح'
            ], 403);
        }

        // إذا كان التحقق من نوع العميل غير مفعل، نسمح بالطلب
        if (!$checkClientType) {
            return $next($request);
        }

        // إذا كان الطلب من تطبيق Flutter أو Dart، نسمح به مباشرة
        if (strpos($userAgent, 'Flutter') !== false || strpos($userAgent, 'Dart') !== false) {
            return $next($request);
        }
        
        // السماح لـ Postman للاختبار
        if (strpos($userAgent, 'PostmanRuntime') !== false) {
            return $next($request);
        }

        // التحقق من باقي العملاء المسموح بهم
        $isAllowedClient = false;
        foreach ($allowedClients as $client) {
            if (stripos($userAgent, $client) !== false) {
                $isAllowedClient = true;
                break;
            }
        }

        // إذا لم يكن العميل مسموحًا به، نرفض الطلب
        if (!$isAllowedClient) {
            if ($logUnauthorizedAttempts) {
                Log::warning('محاولة وصول غير مصرح بها إلى API - عميل غير مسموح به', [
                    'ip' => $request->ip(),
                    'user_agent' => $userAgent,
                    'path' => $request->path()
                ]);
            }
            
            return response()->json([
                'status' => false,
                'message' => 'الوصول مسموح فقط من التطبيقات المحمولة المعتمدة'
            ], 403);
        }

        return $next($request);
    }
}
