<?php
return [
    'paths' => ['api/*'], // تطبيق CORS على مسارات API فقط
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'], // السماح فقط بالطرق المطلوبة
    'allowed_origins' => ['https://alemedu.com'], // تحديد النطاقات الموثوقة
    'allowed_origins_patterns' => [], // لا حاجة لنماذج نمطية إضافية
    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'], // السماح فقط بالرؤوس المطلوبة
    'exposed_headers' => [], // لا حاجة لرؤوس مكشوفة
    'max_age' => 3600, // تمكين التخزين المؤقت لإعدادات CORS لمدة ساعة
    'supports_credentials' => true, // السماح بالمصادقة عبر الطلبات المتقاطعة
];
