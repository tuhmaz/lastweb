<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;
use App\Services\ImageOptimizationService;

class ImageUploadController extends Controller
{
    /**
     * خدمة تحسين الصور
     */
    protected $imageOptimizer;

    /**
     * إنشاء مثيل جديد من وحدة التحكم
     */
    public function __construct(ImageOptimizationService $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
    }

    /**
     * رفع وتحسين الصور
     */
    public function upload(Request $request)
    {
        try {
            // التحقق من وجود ملف في الطلب
            if (!$request->hasFile('file')) {
                Log::error('No file uploaded in the request');
                return response()->json(['error' => 'No file uploaded.'], 400);
            }

            // الحصول على الملف المرفوع
            $file = $request->file('file');

            // التحقق من أن الملف صورة صالحة
            if (!$file->isValid() || !in_array($file->extension(), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'webp'])) {
                Log::error('Invalid image file uploaded', ['extension' => $file->extension()]);
                return response()->json(['error' => 'Invalid image file.'], 400);
            }

            // إنشاء اسم عشوائي للملف
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $tempPath = 'temp/' . $filename;
            
            // تخزين الملف مؤقتًا
            Storage::disk('public')->put($tempPath, file_get_contents($file->getRealPath()));

            try {
                // الحصول على خيارات التحسين من الطلب
                $options = [
                    'max_width' => $request->input('width', 1920),
                    'quality' => $request->input('quality', 85),
                    'convert_to_webp' => $request->input('convert_to_webp', true),
                ];

                // تحسين الصورة وإنشاء نسخ متجاوبة إذا تم طلب ذلك
                if ($request->input('create_responsive', false)) {
                    // تحديد أحجام النسخ المتجاوبة
                    $options['sizes'] = [
                        ['width' => 1280, 'height' => null, 'suffix' => 'lg'],
                        ['width' => 768, 'height' => null, 'suffix' => 'md'],
                        ['width' => 480, 'height' => null, 'suffix' => 'sm']
                    ];
                    
                    // تحسين الصورة وإنشاء نسخ متجاوبة
                    $imagePaths = $this->imageOptimizer->optimizeAndCreateResponsive('temp/' . $filename, 'public', $options);
                    
                    // نقل الصور المحسنة إلى مجلد الصور
                    $urls = [];
                    foreach ($imagePaths as $path) {
                        $newPath = str_replace('temp/', 'images/', $path);
                        Storage::disk('public')->move($path, $newPath);
                        $urls[] = Storage::url($newPath);
                    }
                    
                    // الحصول على معلومات الصورة الرئيسية
                    $mainImagePath = str_replace('temp/', 'images/', $imagePaths[0]);
                    $mainImageUrl = Storage::url($mainImagePath);
                    
                    // قراءة أبعاد الصورة
                    $image = Image::make(Storage::disk('public')->get($mainImagePath));
                    
                    Log::info('Image uploaded and optimized with responsive versions', [
                        'main_image' => $mainImagePath,
                        'responsive_versions' => count($urls) - 1
                    ]);
                    
                    return response()->json([
                        'url' => $mainImageUrl,
                        'width' => $image->width(),
                        'height' => $image->height(),
                        'responsive_urls' => $urls,
                        'optimized' => true
                    ]);
                } else {
                    // تحسين الصورة فقط
                    $optimizedPath = $this->imageOptimizer->optimize('temp/' . $filename, 'public', $options);
                    
                    // نقل الصورة المحسنة إلى مجلد الصور
                    $newPath = str_replace('temp/', 'images/', $optimizedPath);
                    Storage::disk('public')->move($optimizedPath, $newPath);
                    
                    // قراءة أبعاد الصورة
                    $image = Image::make(Storage::disk('public')->get($newPath));
                    
                    Log::info('Image uploaded and optimized', [
                        'path' => $newPath,
                        'width' => $image->width(),
                        'height' => $image->height(),
                        'quality' => $options['quality']
                    ]);
                    
                    return response()->json([
                        'url' => Storage::url($newPath),
                        'width' => $image->width(),
                        'height' => $image->height(),
                        'optimized' => true
                    ]);
                }
            } catch (\Exception $e) {
                // حذف الملف المؤقت في حالة حدوث خطأ
                Storage::disk('public')->delete($tempPath);
                
                Log::error('Error processing image with ImageOptimizationService', [
                    'error' => $e->getMessage(),
                    'file' => $file->getClientOriginalName()
                ]);
                
                return response()->json(['error' => 'Error processing image: ' . $e->getMessage()], 500);
            }
        } catch (\Exception $e) {
            Log::error('Unexpected error in image upload', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    /**
     * رفع الملفات العامة (مثل PDF و DOCX)
     */
    public function uploadFile(Request $request)
    {
        // التحقق من وجود ملف في الطلب
        if ($request->hasFile('file')) {
            // الحصول على الملف المرفوع
            $file = $request->file('file');

            // إنشاء اسم عشوائي للملف باستخدام الامتداد الأصلي
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();

            // تخزين الملف في مجلد 'public/files' والحصول على مسار التخزين
            $path = $file->storeAs('public/files', $filename);

            // إرجاع الرابط إلى الملف المرفوع
            return response()->json(['url' => Storage::url('files/' . $filename)]);
        }

        // إرجاع خطأ إذا لم يتم العثور على ملف في الطلب
        return response()->json(['error' => 'No file uploaded.'], 400);
    }
}
