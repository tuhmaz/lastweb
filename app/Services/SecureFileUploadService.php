<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Exception;

class SecureFileUploadService
{
    /**
     * القائمة البيضاء لأنواع الملفات المسموح بها
     */
    protected $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    /**
     * الحد الأقصى لحجم الملف (بالبايت) - 5 ميجابايت
     */
    protected $maxFileSize = 5 * 1024 * 1024;

    /**
     * تخزين الملف بشكل آمن
     *
     * @param UploadedFile $file الملف المرفوع
     * @param string $directory المجلد الذي سيتم تخزين الملف فيه
     * @param bool $processImage هل يجب معالجة الملف كصورة
     * @param string|null $customFilename اسم مخصص للملف (اختياري)
     * @return string مسار الملف المخزن
     * @throws Exception في حالة وجود خطأ أمني
     */
    public function securelyStoreFile(UploadedFile $file, string $directory, bool $processImage = true, string $customFilename = null): string
    {
        // التحقق من أن الملف موجود وغير فارغ
        if (!$file || !$file->isValid()) {
            throw new Exception('الملف غير صالح');
        }

        // التحقق من نوع الملف
        if (!$this->isAllowedMimeType($file)) {
            throw new Exception('نوع الملف غير مسموح به');
        }

        // التحقق من حجم الملف
        if ($file->getSize() > $this->maxFileSize) {
            throw new Exception('حجم الملف أكبر من الحد المسموح به');
        }

        // فحص محتوى الملف للتأكد من أنه آمن
        if (!$this->scanFileContent($file)) {
            throw new Exception('محتوى الملف غير آمن');
        }

        // إنشاء اسم آمن للملف
        $safeFilename = $customFilename ? $this->sanitizeFilename($customFilename) : $this->generateSafeFilename($file);

        // معالجة الصور إذا كان الملف صورة
        if ($processImage && $this->isImage($file)) {
            return $this->processAndStoreImage($file, $directory, $safeFilename);
        }

        // تخزين الملف العادي
        $path = $file->storeAs($directory, $safeFilename, 'public');
        return $path;
    }

    /**
     * تحسين اسم الملف المخصص
     */
    protected function sanitizeFilename(string $filename): string
    {
        // إزالة الرموز الخاصة
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        // إضافة الامتداد إذا لم يكن موجودًا
        if (strpos($filename, '.') === false) {
            $extension = $this->isImage(new UploadedFile($filename, $filename)) ? 'webp' : 'txt';
            $filename .= '.' . $extension;
        }
        
        return $filename;
    }

    /**
     * التحقق من أن نوع الملف مسموح به
     */
    protected function isAllowedMimeType(UploadedFile $file): bool
    {
        // التحقق من نوع MIME الحقيقي للملف وليس الامتداد فقط
        $mimeType = $file->getMimeType();
        return in_array($mimeType, $this->allowedMimeTypes);
    }

    /**
     * فحص محتوى الملف للتأكد من أنه آمن
     */
    protected function scanFileContent(UploadedFile $file): bool
    {
        // قراءة أول 1024 بايت من الملف للتحقق من وجود أكواد PHP أو JavaScript ضارة
        $content = file_get_contents($file->getRealPath(), false, null, 0, 1024);
        
        // البحث عن أكواد PHP
        if (strpos($content, '<?php') !== false || strpos($content, '<?=') !== false) {
            return false;
        }
        
        // البحث عن أكواد JavaScript ضارة
        $suspiciousPatterns = [
            '<script',
            'javascript:',
            'eval(',
            'document.cookie',
            'onerror=',
            'onload=',
            'onclick=',
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * إنشاء اسم آمن للملف
     */
    protected function generateSafeFilename(UploadedFile $file): string
    {
        $extension = $this->isImage($file) ? 'webp' : $file->getClientOriginalExtension();
        return Str::random(10) . '_' . time() . '.' . $extension;
    }

    /**
     * التحقق من أن الملف هو صورة
     */
    protected function isImage(UploadedFile $file): bool
    {
        $imageMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        return in_array($file->getMimeType(), $imageMimeTypes);
    }

    /**
     * معالجة وتخزين الصورة
     */
    protected function processAndStoreImage(UploadedFile $file, string $directory, string $filename): string
    {
        // معالجة الصورة وتحويلها إلى WebP مع ضغط 75%
        $image = Image::make($file);
        
        // التحقق من أن الملف هو صورة حقيقية
        if (!$image->width() || !$image->height()) {
            throw new Exception('الملف ليس صورة صالحة');
        }
        
        // تحويل الصورة إلى WebP
        $image->encode('webp', 75);
        
        // المسار الكامل للملف
        $fullPath = $directory . '/' . $filename;
        
        // حفظ الصورة في التخزين العام
        Storage::disk('public')->put($fullPath, (string) $image);
        
        return $fullPath;
    }
}
