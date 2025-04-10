<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use App\Services\ImageOptimizationService;

class ImageOptimizer
{
    /**
     * خدمة تحسين الصور
     */
    protected $imageOptimizer;

    /**
     * إنشاء مثيل جديد من الميدلوير
     */
    public function __construct(ImageOptimizationService $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
    }

    /**
     * تحسين الصور في الاستجابة
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // التحقق مما إذا كان تحسين الصور مفعل
        if (!Config::get('app.image_optimization.enabled', true)) {
            return $response;
        }

        // تطبيق التحسينات فقط على استجابات HTML
        if ($this->isHtmlResponse($response)) {
            $this->optimizeImages($response);
        }

        return $response;
    }

    /**
     * التحقق مما إذا كانت الاستجابة HTML
     */
    protected function isHtmlResponse($response): bool
    {
        $contentType = $response->headers->get('Content-Type');
        return strpos($contentType, 'text/html') !== false;
    }

    /**
     * تحسين الصور في محتوى HTML
     */
    protected function optimizeImages($response): void
    {
        $content = $response->getContent();
        if (!$content) {
            return;
        }

        // إضافة سمات تحسين الصور
        $content = $this->addLazyLoading($content);
        $content = $this->addSrcsetAttributes($content);
        $content = $this->addSizeAttributes($content);
        $content = $this->correctImagePaths($content);

        $response->setContent($content);
    }

    /**
     * إضافة التحميل الكسول للصور
     */
    protected function addLazyLoading(string $content): string
    {
        return $this->imageOptimizer->addLazyLoadingToHtml($content);
    }

    /**
     * إضافة سمة srcset للصور للعرض المتجاوب
     */
    protected function addSrcsetAttributes(string $content): string
    {
        // تحديد نمط للبحث عن الصور التي تحتاج إلى srcset
        $pattern = '/<img([^>]*) src="([^"]+\.(jpg|jpeg|png|webp))"/i';
        
        // استبدال الصور بإضافة srcset
        $content = preg_replace_callback($pattern, function($matches) {
            $attributes = $matches[1];
            $src = $matches[2];
            $extension = $matches[3];
            
            // تجاهل الصور التي تحتوي بالفعل على srcset
            if (strpos($attributes, 'srcset') !== false) {
                return $matches[0];
            }
            
            // تجاهل الصور الخارجية
            if (Str::startsWith($src, 'http://') || Str::startsWith($src, 'https://')) {
                return $matches[0];
            }
            
            // تحديد ما إذا كانت الصورة موجودة في المجلد العام
            $publicPath = public_path(ltrim($src, '/'));
            if (!file_exists($publicPath)) {
                return $matches[0];
            }
            
            // إنشاء srcset للصورة
            return "<img{$attributes} src=\"{$src}\" srcset=\"{$src} 1x, {$src}?dpr=2 2x\"";
        }, $content);
        
        return $content;
    }

    /**
     * إضافة سمات الحجم للصور لتجنب تغيير تخطيط الصفحة
     */
    protected function addSizeAttributes(string $content): string
    {
        // تحديد نمط للبحث عن الصور التي تحتاج إلى أبعاد
        $pattern = '/<img([^>]*) src="([^"]+)"/i';
        
        // استبدال الصور بإضافة أبعاد
        $content = preg_replace_callback($pattern, function($matches) {
            $attributes = $matches[1];
            $src = $matches[2];
            
            // تجاهل الصور التي تحتوي بالفعل على أبعاد
            if (strpos($attributes, 'width=') !== false || strpos($attributes, 'height=') !== false) {
                return $matches[0];
            }
            
            // تجاهل الصور الخارجية
            if (Str::startsWith($src, 'http://') || Str::startsWith($src, 'https://')) {
                return $matches[0];
            }
            
            // تحديد ما إذا كانت الصورة موجودة في المجلد العام
            $publicPath = public_path(ltrim($src, '/'));
            if (!file_exists($publicPath)) {
                return $matches[0];
            }
            
            // الحصول على أبعاد الصورة
            try {
                $dimensions = getimagesize($publicPath);
                if ($dimensions && isset($dimensions[0]) && isset($dimensions[1])) {
                    return "<img{$attributes} src=\"{$src}\" width=\"{$dimensions[0]}\" height=\"{$dimensions[1]}\"";
                }
            } catch (\Exception $e) {
                // تجاهل الأخطاء
            }
            
            return $matches[0];
        }, $content);
        
        return $content;
    }

    /**
     * تصحيح مسارات الصور لتجنب مشكلة تكرار "images/"
     */
    protected function correctImagePaths(string $content): string
    {
        return $this->imageOptimizer->correctImagePathsInHtml($content);
    }
}
