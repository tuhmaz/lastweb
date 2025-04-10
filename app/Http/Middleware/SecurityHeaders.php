<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class SecurityHeaders
{
    /**
     * رؤوس الأمان الافتراضية
     */
    protected $securityHeaders = [
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(self), payment=(), usb=(), screen-wake-lock=(), accelerometer=(), gyroscope=(), magnetometer=(), midi=()',
        // تغيير من 'require-corp' إلى 'credentialless' لحل مشكلة COEP
        'Cross-Origin-Embedder-Policy' => 'credentialless',
        'Cross-Origin-Opener-Policy' => 'same-origin',
        // تغيير من 'same-origin' إلى 'cross-origin' لسماح بتحميل الموارد من مصادر خارجية
        'Cross-Origin-Resource-Policy' => 'cross-origin',
    ];

    /**
     * معالجة الطلب
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // التحقق مما إذا كان الطلب لصفحة المراقبة
        $isMonitoringPage = $this->isMonitoringPage($request);

        // إضافة رؤوس الأمان الأساسية
        foreach ($this->securityHeaders as $header => $value) {
            // إذا كانت صفحة المراقبة، تخطي بعض رؤوس الأمان التي تسبب مشاكل مع الخرائط
            if ($isMonitoringPage && in_array($header, ['Cross-Origin-Embedder-Policy', 'Cross-Origin-Resource-Policy'])) {
                continue;
            }
            $response->headers->set($header, $value);
        }

        // تكوين سياسة CSP المحسنة
        $response->headers->set('Content-Security-Policy', $this->getEnhancedCSP($isMonitoringPage));

        // تحسين إعدادات ملفات تعريف الارتباط
        if ($response->headers->has('Set-Cookie')) {
            $cookies = $response->headers->getCookies();
            $response->headers->remove('Set-Cookie');

            foreach ($cookies as $cookie) {
                $response->withCookie(
                    cookie(
                        $cookie->getName(),
                        $cookie->getValue(),
                        $cookie->getExpiresTime(),
                        $cookie->getPath(),
                        $cookie->getDomain(),
                        true, // secure
                        true, // httpOnly
                        true, // raw
                        'strict' // sameSite
                    )
                );
            }
        }

        // إضافة رؤوس CORS إذا كان ضرورياً
        if ($this->shouldAllowCORS($request) || $isMonitoringPage) {
            $frontendUrl = config('app.frontend_url', '*');
            $response->headers->set('Access-Control-Allow-Origin', $frontendUrl);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }

        // إضافة HSTS في بيئة الإنتاج
        if (App::environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // إضافة رؤوس أمان إضافية
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('X-Download-Options', 'noopen');

        return $response;
    }

    /**
     * الحصول على سياسة CSP المحسنة
     */
    protected function getEnhancedCSP(bool $isMonitoringPage = false): string
    {
        $csp = [
            "default-src" => ["'self'"],
            "script-src" => ["'self'", "'unsafe-inline'", "'unsafe-eval'", "https:", "http:"],
            "style-src" => ["'self'", "'unsafe-inline'", "https:", "http:"],
            "img-src" => ["'self'", "data:", "https:", "http:", "blob:"],
            "font-src" => ["'self'", "data:", "https:", "http:"],
            "frame-src" => ["'self'"],
            "connect-src" => ["'self'", "wss:", "https:", "http:"],
            "media-src" => ["'self'"],
            "object-src" => ["'none'"],
            "base-uri" => ["'self'"],
            "form-action" => ["'self'"],
            "frame-ancestors" => ["'self'"],
        ];

        // إذا كانت صفحة المراقبة، نضيف المزيد من السماحات للصور والخرائط
        if ($isMonitoringPage) {
            $csp["img-src"] = ["'self'", "data:", "https:", "http:", "blob:", "*"];
            $csp["connect-src"] = ["'self'", "wss:", "https:", "http:", "*"];
        }

        return $this->buildCSPString($csp);
    }

    protected function buildCSPString(array $csp): string
    {
        return implode('; ', array_map(function ($key, $values) {
            return $key . ' ' . implode(' ', $values);
        }, array_keys($csp), $csp));
    }

    /**
     * تحديد ما إذا كان يجب السماح بـ CORS للطلب
     */
    protected function shouldAllowCORS(Request $request): bool
    {
        return $request->headers->has('Origin') &&
               $request->headers->get('Origin') !== $request->getSchemeAndHttpHost();
    }

    /**
     * التحقق مما إذا كان الطلب لصفحة المراقبة
     */
    protected function isMonitoringPage(Request $request): bool
    {
        $path = $request->path();
        return strpos($path, 'dashboard/monitoring') !== false;
    }
}
