<?php

namespace Tests\Unit;

use App\Services\SecureFileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecureFileUploadServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function it_stores_valid_image_files()
    {
        // إنشاء خدمة التحميل الآمن
        $service = new SecureFileUploadService();
        
        // إنشاء ملف صورة وهمي
        $file = UploadedFile::fake()->image('test.jpg', 400, 400);
        
        // تخزين الملف
        $path = $service->securelyStoreFile($file, 'images/test', true);
        
        // التأكد من أن الملف تم تخزينه
        Storage::disk('public')->assertExists($path);
        
        // التأكد من أن الملف تم تحويله إلى WebP
        $this->assertStringEndsWith('.webp', $path);
    }
    
    /** @test */
    public function it_rejects_invalid_mime_types()
    {
        // إنشاء خدمة التحميل الآمن
        $service = new SecureFileUploadService();
        
        // إنشاء ملف PHP وهمي
        $file = UploadedFile::fake()->create('malicious.php', 100, 'application/x-php');
        
        // توقع استثناء عند محاولة تخزين ملف غير آمن
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('نوع الملف غير مسموح به');
        
        // محاولة تخزين الملف
        $service->securelyStoreFile($file, 'uploads', false);
    }
    
    /** @test */
    public function it_rejects_oversized_files()
    {
        // إنشاء خدمة التحميل الآمن
        $service = new SecureFileUploadService();
        
        // تعديل الخاصية maxFileSize لتكون 1KB للاختبار
        $reflection = new \ReflectionClass($service);
        $property = $reflection->getProperty('maxFileSize');
        $property->setAccessible(true);
        $property->setValue($service, 1024); // 1KB
        
        // إنشاء ملف كبير (2KB)
        $file = UploadedFile::fake()->create('large.jpg', 2);
        
        // توقع استثناء عند محاولة تخزين ملف كبير
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('حجم الملف أكبر من الحد المسموح به');
        
        // محاولة تخزين الملف
        $service->securelyStoreFile($file, 'uploads', false);
    }
    
    /** @test */
    public function it_rejects_files_with_malicious_content()
    {
        // إنشاء خدمة التحميل الآمن
        $service = new SecureFileUploadService();
        
        // إنشاء ملف صورة وهمي مع محتوى ضار
        $file = UploadedFile::fake()->createWithContent(
            'malicious.jpg',
            '<?php echo "Hacked!"; ?>'
        );
        
        // توقع استثناء عند محاولة تخزين ملف ضار
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('محتوى الملف غير آمن');
        
        // محاولة تخزين الملف
        $service->securelyStoreFile($file, 'uploads', false);
    }
}
