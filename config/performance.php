<?php

return [
    /*
    |--------------------------------------------------------------------------
    | إعدادات تحسين الأداء
    |--------------------------------------------------------------------------
    |
    | هذا الملف يحتوي على إعدادات تحسين أداء التطبيق
    |
    */

    'cache' => [
        // مدة التخزين المؤقت بالثواني (أسبوع واحد)
        'duration' => 604800,

        // أنواع المحتوى القابلة للتخزين المؤقت
        'cacheable_types' => [
            'text/html',
            'text/css',
            'text/javascript',
            'application/javascript',
            'application/json',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
        ],
    ],

    'compression' => [
        // تمكين ضغط المحتوى
        'enabled' => true,

        // مستوى الضغط (1-9)
        'level' => 6,

        // الحد الأدنى لحجم الملف للضغط (بالبايت)
        'min_size' => 1024,

        // أنواع المحتوى القابلة للضغط
        'compressible_types' => [
            'text/html',
            'text/css',
            'text/javascript',
            'application/javascript',
            'application/json',
            'text/plain',
            'application/xml',
            'text/xml',
        ],
    ],

    'security' => [
        // مدة صلاحية HSTS بالثواني (سنة واحدة)
        'hsts_max_age' => 31536000,

        // تمكين HSTS للنطاقات الفرعية
        'hsts_include_subdomains' => true,

        // تمكين HSTS preload
        'hsts_preload' => true,

        // قائمة النطاقات المسموح بها في CSP
        'csp_domains' => [
            'fonts.googleapis.com',
            'fonts.gstatic.com',
            'cdn.jsdelivr.net',
            '*.fontawesome.com',
        ],
    ],

    'optimization' => [
        // تمكين تحسين الصور تلقائياً
        'auto_optimize_images' => true,

        // تمكين تصغير CSS/JS
        'minify_assets' => true,

        // تمكين تحميل الموارد مسبقاً
        'preload_resources' => true,

        // الموارد التي سيتم تحميلها مسبقاً
        'preload_resources' => [
            'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css',
            'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css',
            'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js',
            'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/lang/summernote-ar-AR.js',
         

        ],

        // تمكين التحميل الكسول للصور
        'lazy_loading' => true,
    ],
];
