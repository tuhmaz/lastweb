<?php

return [
    /*
    |--------------------------------------------------------------------------
    | مفاتيح API
    |--------------------------------------------------------------------------
    |
    | هذا الملف يحتوي على مفاتيح API المستخدمة في التطبيق
    | يمكن تعديل هذه المفاتيح في ملف .env
    |
    */

    // مفتاح API الرئيسي للتطبيق
    'key' => env('API_KEY', 'gfOTaGfOcVZigVyN3Go5ZHwr606mmzlPs6gfet0Nsd6d5wBykGGsI9rf1zZ0UYsZ'),
    
    // قائمة بالعملاء المسموح لهم بالوصول إلى API
    'allowed_clients' => [
        'Flutter',
        'Dart',
        'Mobile',
        'Android',
        'iPhone',
        'iPad',
        'Windows Phone',
        'PostmanRuntime', // للاختبار فقط
    ],
    
    // إعدادات الأمان الإضافية
    'security' => [
        // هل يتم تسجيل محاولات الوصول غير المصرح بها
        'log_unauthorized_attempts' => true,
        
        // هل يتم التحقق من نوع العميل
        'check_client_type' => true,
    ],
];
