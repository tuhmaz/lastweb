<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class HttpsProtocol
{
    /**
     * توجيه جميع الطلبات إلى HTTPS في بيئة الإنتاج
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // تطبيق HTTPS فقط في بيئة الإنتاج وعندما يكون force_https مفعل
        if (!$request->secure() && App::environment('production') && Config::get('secure-connections.force_https', true)) {
            // إعادة توجيه الطلب إلى HTTPS
            return redirect()->secure($request->getRequestUri());
        }

        // تعيين رؤوس HTTP الأمنية
        $response = $next($request);
        
        // تعيين رؤوس HTTP الأمنية لجميع الاستجابات
        
        // تعيين رأس Strict-Transport-Security
        $hstsMaxAge = Config::get('secure-connections.hsts_max_age', 31536000);
        $hstsIncludeSubdomains = Config::get('secure-connections.hsts_include_subdomains', true);
        $hstsHeader = "max-age={$hstsMaxAge}";
        if ($hstsIncludeSubdomains) {
            $hstsHeader .= '; includeSubDomains';
        }
        $response->headers->set('Strict-Transport-Security', $hstsHeader);
        
        // تعيين رأس X-Content-Type-Options
        if (Config::get('secure-connections.enable_content_type_options', true)) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        }
        
        // تعيين رأس X-XSS-Protection
        if (Config::get('secure-connections.enable_xss_protection', true)) {
            $response->headers->set('X-XSS-Protection', '1; mode=block');
        }
        
        // تعيين رأس X-Frame-Options
        if (Config::get('secure-connections.enable_frame_options', true)) {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        }
        
        // تعيين رأس Referrer-Policy
        if (Config::get('secure-connections.enable_referrer_policy', true)) {
            $referrerPolicy = Config::get('secure-connections.referrer_policy', 'strict-origin-when-cross-origin');
            $response->headers->set('Referrer-Policy', $referrerPolicy);
        }
        
        // تعيين رأس Content-Security-Policy
        if (Config::get('secure-connections.enable_csp', true)) {
            $cspDirectives = [];
            $configDirectives = Config::get('secure-connections.csp_directives', []);
            
            foreach ($configDirectives as $directive => $sources) {
                $cspDirectives[] = $directive . ' ' . implode(' ', $sources);
            }
            
            if (!empty($cspDirectives)) {
                $response->headers->set('Content-Security-Policy', implode('; ', $cspDirectives));
            }
        }
        
        return $response;
    }
}
