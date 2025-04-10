<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Exception;

class ImageOptimizationService
{
    /**
     * أقصى عرض للصور (بالبكسل)
     */
    protected $maxWidth;

    /**
     * جودة الصور (1-100)
     */
    protected $quality;

    /**
     * تحويل الصور إلى WebP
     */
    protected $convertToWebP;

    /**
     * إنشاء نسخ متجاوبة
     */
    protected $createResponsive;

    /**
     * مجلد الملفات المؤقتة
     */
    protected $tempDirectory;

    /**
     * القرص المستخدم للتخزين
     */
    protected $storageDisk;

    /**
     * أحجام النسخ المتجاوبة
     */
    protected $responsiveSizes;

    /**
     * تنسيقات الصور المدعومة
     */
    protected $supportedFormats;

    /**
     * إنشاء مثيل جديد من الخدمة
     */
    public function __construct()
    {
        // تحميل الإعدادات من ملف التكوين
        $this->loadConfig();
    }

    /**
     * تحميل الإعدادات من ملف التكوين
     */
    protected function loadConfig(): void
    {
        $this->maxWidth = Config::get('app.image_optimization.max_width', 1920);
        $this->quality = Config::get('app.image_optimization.quality', 85);
        $this->convertToWebP = Config::get('app.image_optimization.convert_to_webp', true);
        $this->createResponsive = Config::get('app.image_optimization.create_responsive', true);
        $this->tempDirectory = Config::get('app.image_optimization.temp_directory', 'temp');
        $this->storageDisk = Config::get('app.image_optimization.storage_disk', 'public');
        $this->responsiveSizes = Config::get('app.image_optimization.responsive_sizes', [
            'lg' => ['width' => 1280, 'height' => null],
            'md' => ['width' => 768, 'height' => null],
            'sm' => ['width' => 480, 'height' => null],
        ]);
        $this->supportedFormats = Config::get('app.image_optimization.formats', [
            'jpg', 'jpeg', 'png', 'gif', 'webp'
        ]);
    }

    /**
     * تحسين صورة
     *
     * @param string $path مسار الصورة
     * @param string $disk القرص المستخدم (public, local, etc.)
     * @param array $options خيارات إضافية
     * @return string|null مسار الصورة المحسنة
     */
    public function optimize(string $path, string $disk = null, array $options = []): ?string
    {
        // استخدام القرص المحدد في الإعدادات إذا لم يتم تحديده
        $disk = $disk ?? $this->storageDisk;

        try {
            // تطبيق الخيارات
            $this->applyOptions($options);

            // التحقق من وجود الصورة
            if (!Storage::disk($disk)->exists($path)) {
                Log::warning("Image not found: {$path} on disk {$disk}");
                return null;
            }

            // قراءة الصورة
            $image = Image::make(Storage::disk($disk)->get($path));
            
            // تغيير حجم الصورة إذا كانت أكبر من الحد الأقصى
            if ($image->width() > $this->maxWidth) {
                $image->resize($this->maxWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // إذا كان التحويل إلى WebP مفعل وكانت المكتبة تدعم WebP
            if ($this->convertToWebP && function_exists('imagewebp')) {
                // تغيير امتداد الملف إلى .webp
                $newPath = $this->changeExtension($path, 'webp');
                
                // حفظ الصورة بتنسيق WebP
                $image->encode('webp', $this->quality);
                Storage::disk($disk)->put($newPath, $image->stream());
                
                // إذا تم طلب الاحتفاظ بالصورة الأصلية، لا نقوم بحذفها
                if (!($options['keep_original'] ?? false)) {
                    Storage::disk($disk)->delete($path);
                }
                
                return $newPath;
            } else {
                // تحسين الصورة بدون تغيير التنسيق
                $image->encode(null, $this->quality);
                Storage::disk($disk)->put($path, $image->stream());
                
                return $path;
            }
        } catch (Exception $e) {
            Log::error("Error optimizing image {$path}: " . $e->getMessage());
            return $path; // إرجاع المسار الأصلي في حالة حدوث خطأ
        }
    }

    /**
     * تحسين صورة من URL
     *
     * @param string $url رابط الصورة
     * @param string $savePath مسار الحفظ
     * @param string $disk القرص المستخدم
     * @param array $options خيارات إضافية
     * @return string|null مسار الصورة المحسنة
     */
    public function optimizeFromUrl(string $url, string $savePath, string $disk = null, array $options = []): ?string
    {
        // استخدام القرص المحدد في الإعدادات إذا لم يتم تحديده
        $disk = $disk ?? $this->storageDisk;

        try {
            // تنزيل الصورة
            $imageContent = file_get_contents($url);
            if (!$imageContent) {
                Log::warning("Could not download image from URL: {$url}");
                return null;
            }

            // التأكد من وجود المجلد المؤقت
            $tempPath = $this->tempDirectory . '/' . basename($savePath);
            if (!Storage::disk($disk)->exists($this->tempDirectory)) {
                Storage::disk($disk)->makeDirectory($this->tempDirectory);
            }

            // حفظ الصورة مؤقتًا
            Storage::disk($disk)->put($tempPath, $imageContent);

            // تحسين الصورة
            $optimizedPath = $this->optimize($tempPath, $disk, $options);

            // نقل الصورة من المجلد المؤقت إلى المسار المطلوب
            if ($optimizedPath && $optimizedPath !== $tempPath) {
                $finalPath = str_replace($this->tempDirectory . '/', '', $optimizedPath);
                Storage::disk($disk)->move($optimizedPath, $savePath);
                return $savePath;
            }

            return $optimizedPath;
        } catch (Exception $e) {
            Log::error("Error optimizing image from URL {$url}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * تحسين جميع الصور في مجلد
     *
     * @param string $directory المجلد المراد تحسين صوره
     * @param string $disk القرص المستخدم
     * @param array $options خيارات إضافية
     * @return array قائمة بمسارات الصور المحسنة
     */
    public function optimizeDirectory(string $directory, string $disk = null, array $options = []): array
    {
        // استخدام القرص المحدد في الإعدادات إذا لم يتم تحديده
        $disk = $disk ?? $this->storageDisk;

        $optimizedPaths = [];

        try {
            // الحصول على قائمة الملفات في المجلد
            $files = Storage::disk($disk)->files($directory);

            foreach ($files as $file) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                
                // تحسين الصور فقط
                if (in_array($extension, $this->supportedFormats)) {
                    $optimizedPath = $this->optimize($file, $disk, $options);
                    if ($optimizedPath) {
                        $optimizedPaths[] = $optimizedPath;
                    }
                }
            }

            // تحسين الصور في المجلدات الفرعية
            $directories = Storage::disk($disk)->directories($directory);
            foreach ($directories as $subDirectory) {
                $subOptimizedPaths = $this->optimizeDirectory($subDirectory, $disk, $options);
                $optimizedPaths = array_merge($optimizedPaths, $subOptimizedPaths);
            }
        } catch (Exception $e) {
            Log::error("Error optimizing directory {$directory}: " . $e->getMessage());
        }

        return $optimizedPaths;
    }

    /**
     * تغيير امتداد الملف
     *
     * @param string $path مسار الملف
     * @param string $newExtension الامتداد الجديد
     * @return string المسار الجديد
     */
    protected function changeExtension(string $path, string $newExtension): string
    {
        $pathInfo = pathinfo($path);
        return $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $newExtension;
    }

    /**
     * تطبيق الخيارات
     *
     * @param array $options الخيارات
     * @return void
     */
    protected function applyOptions(array $options): void
    {
        if (isset($options['max_width'])) {
            $this->maxWidth = (int) $options['max_width'];
        }

        if (isset($options['quality'])) {
            $this->quality = (int) $options['quality'];
        }

        if (isset($options['convert_to_webp'])) {
            $this->convertToWebP = (bool) $options['convert_to_webp'];
        }

        if (isset($options['create_responsive'])) {
            $this->createResponsive = (bool) $options['create_responsive'];
        }
    }

    /**
     * إنشاء نسخ مختلفة الأحجام للصورة
     *
     * @param string $path مسار الصورة
     * @param array $sizes الأحجام المطلوبة [['width' => 800, 'height' => null, 'suffix' => 'md']]
     * @param string $disk القرص المستخدم
     * @return array مسارات النسخ المختلفة
     */
    public function createResponsiveImages(string $path, array $sizes = null, string $disk = null): array
    {
        // استخدام القرص المحدد في الإعدادات إذا لم يتم تحديده
        $disk = $disk ?? $this->storageDisk;

        // استخدام الأحجام المحددة في الإعدادات إذا لم يتم تحديدها
        if ($sizes === null) {
            $sizes = [];
            foreach ($this->responsiveSizes as $suffix => $dimensions) {
                $sizes[] = array_merge($dimensions, ['suffix' => $suffix]);
            }
        }

        $paths = [];

        try {
            // التحقق من وجود الصورة
            if (!Storage::disk($disk)->exists($path)) {
                Log::warning("Image not found: {$path} on disk {$disk}");
                return $paths;
            }

            // قراءة الصورة
            $image = Image::make(Storage::disk($disk)->get($path));
            $pathInfo = pathinfo($path);

            foreach ($sizes as $size) {
                $width = $size['width'] ?? null;
                $height = $size['height'] ?? null;
                $suffix = $size['suffix'] ?? "w{$width}";

                // تجاهل الأحجام غير الصالحة
                if (!$width && !$height) {
                    continue;
                }

                // إنشاء نسخة بالحجم المطلوب
                $resizedImage = clone $image;
                $resizedImage->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                // إنشاء مسار الملف الجديد
                $newPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '-' . $suffix;
                
                // تحديد التنسيق والامتداد
                $extension = $this->convertToWebP && function_exists('imagewebp') ? 'webp' : $pathInfo['extension'];
                $newPath .= '.' . $extension;

                // حفظ الصورة
                $resizedImage->encode($extension, $this->quality);
                Storage::disk($disk)->put($newPath, $resizedImage->stream());
                
                $paths[] = $newPath;
            }
        } catch (Exception $e) {
            Log::error("Error creating responsive images for {$path}: " . $e->getMessage());
        }

        return $paths;
    }

    /**
     * تحسين صورة وإنشاء نسخ متجاوبة
     *
     * @param string $path مسار الصورة
     * @param string $disk القرص المستخدم
     * @param array $options خيارات إضافية
     * @return array مسارات الصور المحسنة
     */
    public function optimizeAndCreateResponsive(string $path, string $disk = null, array $options = []): array
    {
        // استخدام القرص المحدد في الإعدادات إذا لم يتم تحديده
        $disk = $disk ?? $this->storageDisk;

        $paths = [];

        // تحسين الصورة الأصلية
        $optimizedPath = $this->optimize($path, $disk, array_merge($options, ['keep_original' => true]));
        if ($optimizedPath) {
            $paths[] = $optimizedPath;

            // إنشاء نسخ متجاوبة إذا كان مطلوبًا
            if ($this->createResponsive || ($options['create_responsive'] ?? false)) {
                // استخدام الأحجام المحددة في الخيارات أو الإعدادات
                $sizes = $options['sizes'] ?? null;
                $responsivePaths = $this->createResponsiveImages($optimizedPath, $sizes, $disk);
                $paths = array_merge($paths, $responsivePaths);
            }
        }

        return $paths;
    }

    /**
     * تصحيح مسارات الصور في محتوى HTML
     *
     * @param string $html محتوى HTML
     * @return string محتوى HTML مع مسارات صور مصححة
     */
    public function correctImagePathsInHtml(string $html): string
    {
        // تصحيح مسارات الصور التي تحتوي على تكرار لـ "images/"
        $html = preg_replace('/(src|srcset)="([^"]*images\/images\/[^"]*)"/i', '$1="$2"', $html);
        $html = str_replace('images/images/', 'images/', $html);
        
        // تصحيح مسارات الصور التي تستخدم Storage::url
        $html = preg_replace('/(src|srcset)="([^"]*\/storage\/storage\/[^"]*)"/i', '$1="$2"', $html);
        $html = str_replace('/storage/storage/', '/storage/', $html);
        
        return $html;
    }

    /**
     * إضافة سمات تحميل كسول للصور في محتوى HTML
     *
     * @param string $html محتوى HTML
     * @return string محتوى HTML مع سمات تحميل كسول
     */
    public function addLazyLoadingToHtml(string $html): string
    {
        // استثناء الصور المهمة من التحميل الكسول (الشعار والصور الحيوية)
        return preg_replace(
            '/<img((?!loading=|class="(logo|critical|navbar-brand-img)")[^>]*)>/i',
            '<img$1 loading="lazy" decoding="async">',
            $html
        );
    }
}
