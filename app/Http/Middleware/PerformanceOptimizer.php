<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class PerformanceOptimizer
{
    /**
     * معالجة الطلب
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // تطبيق التحسينات فقط على استجابات HTML
        if ($this->isHtmlResponse($response)) {
            $this->optimizeResponse($response);
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
     * تحسين الاستجابة
     */
    protected function optimizeResponse($response): void
    {
        if (!$response->headers->has('Content-Type') || 
            !str_contains($response->headers->get('Content-Type'), 'text/html')) {
            return;
        }

        $content = $response->getContent();

        // تحسين الأداء عن طريق إضافة headers مناسبة
        $response->headers->set('X-DNS-Prefetch-Control', 'on');
        
        // تحسين Cache-Control بشكل أكثر دقة
        if (!str_contains($response->headers->get('Content-Type', ''), 'text/html')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000');
        } else {
            $response->headers->set('Cache-Control', 'no-cache, must-revalidate');
        }

        // إضافة preload للموارد المهمة فقط للصفحات HTML
        if (Config::get('performance.optimization.preload_resources', true)) {
            $this->addPreloadHeaders($response);
        }

        // تطبيق التحميل الكسول للصور بشكل انتقائي
        if (Config::get('performance.optimization.lazy_loading', true)) {
            $content = $this->addLazyLoading($content);
        }

        // تحسين HTML بشكل أكثر فعالية
        $content = $this->optimizeHtml($content);

        $response->setContent($content);
    }

    /**
     * إضافة preload headers
     */
    protected function addPreloadHeaders($response): void
    {
        // تحديد الأصول الحيوية مع التحقق من وجودها
        $criticalAssets = [
            [
                'path' => '/build/assets/jquery-CED9k22g.js',
                'as' => 'script'
            ],
            [
                'path' => '/build/assets/bootstrap-D9chKi5b.js',
                'as' => 'script'
            ],
            [
                'path' => '/build/assets/fontawesome-BtP3or3R.css',
                'as' => 'style'
            ]
        ];

        // التحقق من وجود علامات script وlink في المحتوى
        $content = $response->getContent();
        $modified = false;
        
        // إزالة أي رؤوس Link موجودة مسبقًا للتحميل المسبق
        $response->headers->remove('Link');
        
        foreach ($criticalAssets as $asset) {
            // التحقق من أن الملف موجود
            $assetPath = ltrim($asset['path'], '/');
            if (file_exists(public_path($assetPath))) {
                // تحقق مما إذا كان الأصل مستخدمًا في الصفحة
                if (strpos($content, $asset['path']) === false && 
                    strpos($content, $assetPath) === false) {
                    
                    $headEndPos = strpos($content, '</head>');
                    if ($headEndPos !== false) {
                        // إضافة العلامة المناسبة بناءً على نوع الملف
                        if ($asset['as'] === 'style') {
                            $tag = '<link rel="stylesheet" href="' . $asset['path'] . '">';
                        } else {
                            $tag = '<script src="' . $asset['path'] . '" defer></script>';
                        }
                        
                        $content = substr_replace($content, $tag, $headEndPos, 0);
                        $modified = true;
                    }
                }
            }
        }
        
        // تحديث المحتوى إذا تم إجراء تعديلات
        if ($modified) {
            $response->setContent($content);
        }
    }

    /**
     * إضافة التحميل الكسول للصور
     */
    protected function addLazyLoading(string $content): string
    {
        // استثناء الصور المهمة من التحميل الكسول (الشعار والصور الحيوية)
        $content = preg_replace(
            '/<img((?!loading=|class="(logo|critical|navbar-brand-img)")[^>]*)>/i',
            '<img$1 loading="lazy">',
            $content
        );

        // إضافة native lazy loading للiframes
        $content = preg_replace(
            '/<iframe((?!loading=)[^>]*)>/i',
            '<iframe$1 loading="lazy">',
            $content
        );

        return $content;
    }

    /**
     * تحسين HTML
     */
    protected function optimizeHtml(string $content): string
    {
        // إزالة التعليقات غير الضرورية مع الحفاظ على التعليقات المهمة
        $content = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $content);

        // تحسين المسافات بشكل ذكي
        $content = preg_replace('/>\s+</s', '><', $content);
        $content = preg_replace('/\s{2,}/s', ' ', $content);

        // تحسين meta tags
        $content = str_replace(
            '<meta name="viewport"',
            '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"',
            $content
        );

        return trim($content);
    }
}
