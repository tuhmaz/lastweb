<?php

return [
    /*
    |--------------------------------------------------------------------------
    | إعدادات الاتصالات الآمنة
    |--------------------------------------------------------------------------
    |
    | هذا الملف يحتوي على إعدادات لتفعيل HTTPS وتأمين الاتصالات في التطبيق.
    |
    */

    /*
    | تفعيل HTTPS في بيئة الإنتاج
    | عند تعيين هذه القيمة إلى true، سيتم توجيه جميع الطلبات إلى HTTPS في بيئة الإنتاج.
    */
    'force_https' => env('FORCE_HTTPS', true),

    /*
    | مدة صلاحية رأس Strict-Transport-Security بالثواني
    | القيمة الافتراضية هي سنة واحدة (31536000 ثانية)
    */
    'hsts_max_age' => env('HSTS_MAX_AGE', 31536000),

    /*
    | تضمين النطاقات الفرعية في رأس Strict-Transport-Security
    */
    'hsts_include_subdomains' => env('HSTS_INCLUDE_SUBDOMAINS', true),

    /*
    | تفعيل رأس Content-Security-Policy
    */
    'enable_csp' => env('ENABLE_CSP', true),

    /*
    | تكوين رأس Content-Security-Policy
    | يمكن تعديل هذه القيم حسب احتياجات التطبيق
    */
    'csp_directives' => [
        'default-src' => ["'self'"],
        'script-src' => ["'self'", "'unsafe-inline'", "'unsafe-eval'"],
        'style-src' => ["'self'", "'unsafe-inline'"],
        'img-src' => ["'self'", "data:", "https:"],
        'font-src' => ["'self'"],
        'connect-src' => ["'self'"],
        'media-src' => ["'self'"],
        'object-src' => ["'none'"],
        'frame-src' => ["'self'"],
        'base-uri' => ["'self'"],
        'form-action' => ["'self'"],
    ],

    /*
    | تفعيل رأس X-Content-Type-Options
    */
    'enable_content_type_options' => env('ENABLE_CONTENT_TYPE_OPTIONS', true),

    /*
    | تفعيل رأس X-XSS-Protection
    */
    'enable_xss_protection' => env('ENABLE_XSS_PROTECTION', true),

    /*
    | تفعيل رأس X-Frame-Options
    */
    'enable_frame_options' => env('ENABLE_FRAME_OPTIONS', true),

    /*
    | تفعيل رأس Referrer-Policy
    */
    'enable_referrer_policy' => env('ENABLE_REFERRER_POLICY', true),
    'referrer_policy' => env('REFERRER_POLICY', 'strict-origin-when-cross-origin'),
];
