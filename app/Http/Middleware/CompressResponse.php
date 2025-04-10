<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;

class CompressResponse
{
  /**
   * Handle an incoming request.
   */
  public function handle(Request $request, Closure $next): Response
  {
    // التحقق مما إذا كان يجب ضغط الطلب
    if ($this->shouldCompress($request)) {
      $this->configureCompression($request);
    }

    $response = $next($request);

    // التحقق من ضغط المحتوى
    if (!$this->isCompressibleContent($response)) {
      return $response;
    }

    // إضافة رؤوس الأمان، التخزين المؤقت، والتحسينات الأخرى
    $this->addCacheHeaders($response);
    $this->addSecurityHeaders($response);
    $this->addETag($response);
    $this->addPerformanceHeaders($response);

    return $response;
  }

  /**
   * تحديد ما إذا كان يجب ضغط الطلب.
   */
  protected function shouldCompress(Request $request): bool
  {
    if (!Config::get('app.compression.enabled', true)) {
      return false;
    }

    // لا تضغط الطلبات غير GET
    if (!$request->isMethod('GET')) {
      return false;
    }

    // لا تضغط طلبات AJAX أو الطلبات التي تحتوي على رأس X-No-Compression
    if ($request->ajax() || $request->headers->has('X-No-Compression')) {
      return false;
    }

    // لا تضغط الطلبات الصغيرة جدًا
    if ($request->header('Content-Length') && (int)$request->header('Content-Length') < Config::get('app.compression.threshold', 1024)) {
      return false;
    }

    // التحقق من دعم المتصفح للضغط
    $acceptEncoding = $request->header('Accept-Encoding', '');
    return str_contains($acceptEncoding, 'gzip') ||
      str_contains($acceptEncoding, 'deflate') ||
      str_contains($acceptEncoding, 'br');
  }

  /**
   * تكوين إعدادات الضغط بناءً على قدرات المتصفح.
   */
  protected function configureCompression(Request $request): void
  {
    $acceptEncoding = $request->header('Accept-Encoding', '');
    $level = Config::get('app.compression.level', 7); // زيادة مستوى الضغط الافتراضي من 6 إلى 7

    // تحديد أفضل طريقة ضغط متاحة
    if (str_contains($acceptEncoding, 'br') && function_exists('brotli_compress')) {
      // استخدام Brotli إذا كان مدعومًا (أكثر كفاءة من gzip)
      ini_set('brotli.output_compression', 'On');
      ini_set('brotli.output_compression_level', $level);
    } else {
      // استخدام zlib (gzip/deflate) كخيار احتياطي
      // Verificar si ya está habilitada la compresión zlib a nivel de PHP
      if (ini_get('zlib.output_compression') !== 'On') {
        // Si no está habilitada, usamos ob_gzhandler que nos da más control
        if (Config::get('app.compression.handler', 'ob_gzhandler') === 'ob_gzhandler') {
          ob_start('ob_gzhandler');
        } else {
          // Alternativamente, activar la compresión zlib
          ini_set('zlib.output_compression', 'On');
          ini_set('zlib.output_compression_level', $level);
        }
      } else {
        // Si ya está habilitada, solo configuramos el nivel
        ini_set('zlib.output_compression_level', $level);
      }
    }
  }

  /**
   * تحديد ما إذا كان المحتوى قابل للضغط.
   */
  protected function isCompressibleContent(Response $response): bool
  {
    // التحقق من حجم المحتوى
    $content = $response->getContent();
    if (!$content || strlen($content) < Config::get('app.compression.threshold', 1024)) {
      return false;
    }

    // التحقق من نوع المحتوى
    $contentType = $response->headers->get('Content-Type', '');
    $allowedTypes = Config::get('app.compression.types', [
      'text/html', 'text/plain', 'text/css', 'text/javascript',
      'application/javascript', 'application/json', 'application/xml',
      'image/svg+xml'
    ]);

    // التحقق من تطابق نوع المحتوى مع الأنواع المسموح بها
    foreach ($allowedTypes as $type) {
      if (str_starts_with($contentType, $type)) {
        return true;
      }
    }

    return false;
  }

  /**
   * إضافة رؤوس التخزين المؤقت.
   */
  protected function addCacheHeaders(Response $response): void
  {
    // تحديد مدة التخزين المؤقت بناءً على نوع المحتوى
    $contentType = $response->headers->get('Content-Type', '');
    $maxAge = Config::get('app.compression.cache_max_age', 86400); // يوم واحد افتراضيًا

    // تخزين مؤقت أطول للموارد الثابتة
    if (str_contains($contentType, 'image/') || 
        str_contains($contentType, 'font/') || 
        str_contains($contentType, 'text/css') || 
        str_contains($contentType, 'application/javascript')) {
      $maxAge = 604800; // أسبوع واحد
    } 
    // تخزين مؤقت أقصر للمحتوى الديناميكي
    elseif (str_contains($contentType, 'text/html') || 
            str_contains($contentType, 'application/json')) {
      $maxAge = 3600; // ساعة واحدة
    }

    // إعداد رؤوس التخزين المؤقت
    $response->headers->set('Cache-Control', "public, max-age={$maxAge}, must-revalidate");
    $response->headers->set('Vary', 'Accept-Encoding, User-Agent');
    $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
    $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + $maxAge) . ' GMT');
  }

  /**
   * إضافة رؤوس الأمان.
   */
  protected function addSecurityHeaders(Response $response): void
  {
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    
    // إضافة Content-Security-Policy للحماية من هجمات XSS
    if (!$response->headers->has('Content-Security-Policy')) {
      $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:;");
    }

    if ($this->shouldEnableHSTS()) {
      $this->addHSTSHeader($response);
    }
  }

  /**
   * تحقق مما إذا كان يجب تمكين HSTS.
   */
  protected function shouldEnableHSTS(): bool
  {
    $config = Config::get('app.compression.security.hsts', []);
    return ($config['enabled'] ?? false) && request()->secure();
  }

  /**
   * إضافة رأس HSTS.
   */
  protected function addHSTSHeader(Response $response): void
  {
    $config = Config::get('app.compression.security.hsts', []);
    $header = "max-age=" . ($config['max_age'] ?? 31536000);

    if ($config['include_subdomains'] ?? true) {
      $header .= '; includeSubDomains';
    }

    if ($config['preload'] ?? true) {
      $header .= '; preload';
    }

    $response->headers->set('Strict-Transport-Security', $header);
  }

  /**
   * إضافة ETag للتحقق من التغييرات.
   */
  protected function addETag(Response $response): void
  {
    $content = $response->getContent();
    if ($content) {
      $response->headers->set('ETag', '"' . md5($content) . '"');
    }
  }

  /**
   * إضافة رؤوس تحسين الأداء.
   */
  protected function addPerformanceHeaders(Response $response): void
  {
    // إضافة رؤوس تحسين الأداء
    $response->headers->set('X-DNS-Prefetch-Control', 'on');
    
    // تمكين تحميل الموارد مسبقًا
    $contentType = $response->headers->get('Content-Type', '');
    if (str_contains($contentType, 'text/html')) {
      // إضافة رؤوس لتحسين الأداء للصفحات HTML فقط
      // ملاحظة: تم إزالة Feature-Policy لأنه تم استبداله بـ Permissions-Policy
      // نتأكد من عدم تعيين Permissions-Policy مرة أخرى إذا كان موجودًا بالفعل
      if (!$response->headers->has('Permissions-Policy')) {
        $response->headers->set('Permissions-Policy', "camera=(), microphone=(), geolocation=(self)");
      }
    }
  }
}
