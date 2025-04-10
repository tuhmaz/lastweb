<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CacheControlMiddleware;

use App\Http\Controllers\{
    AuthController,
    DashboardController,
    UserController,
    RoleController,
    PermissionController,
    SchoolClassController,
    SubjectController,
    SemesterController,
    FileController,
    ArticleController,
    ImageUploadController,
    CategoryController,
    NewsController,
    FrontendNewsController,
    KeywordController,
    MessageController,
    SettingsController,
    HomeController,
    NotificationController,
    MonitoringController,
    PerformanceController,
    SecurityLogController,
    RedisController,
    CalendarController,
    CommentController,
    ReactionController,
    LegalController,
    SitemapController,
    GradeOneController,
    FilterController,
    FrontController,
    VerifyEmailController,
    OneSignalSettingsController,
    ActivityController, // Added ActivityController
    RateLimitLogController // Added RateLimitLogController
};

use App\Http\Controllers\language\LanguageController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// الصفحة الرئيسية
Route::get('/', [HomeController::class, 'index'])->name('home');

// تغيير اللغة
Route::post('/set-database', [HomeController::class, 'setDatabase'])->name('setDatabase');
Route::get('/lang/{locale}', [LanguageController::class, 'swap'])->name('dashboard.lang-swap');

// Authentication Routes
Route::middleware(['api.throttle:route'])->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
        Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

        // Google OAuth Routes
        Route::get('login/google', [AuthController::class, 'googleRedirect'])->name('login.google');
        Route::get('login/google/callback', [AuthController::class, 'googleCallback'])->name('login.google.callback');

        // Password Reset Routes
        Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
        Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    });
});

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [AuthController::class, 'showVerificationNotice'])->name('verification.notice');
    Route::post('/email/verification-notification', [AuthController::class, 'verificationResend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');
});

// Upload Routes
Route::prefix('upload')->group(function () {
    Route::post('/image', [ImageUploadController::class, 'upload'])->name('upload.image');
    Route::post('/file', [ImageUploadController::class, 'uploadFile'])->name('upload.file');
});

// Dashboard Routes (Authenticated and Verified Users)
Route::middleware(['auth', 'verified'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {
    // Dashboard Home
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');

    // تطبيق تقييد معدل الطلبات على مسارات المسؤول الحساسة
    Route::middleware(['api.throttle:route'])->group(function () {
        // User Management Routes
        Route::prefix('users')->name('users.')->middleware(['can:manage users'])->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        // Role Management Routes
        Route::prefix('roles')->name('roles.')->middleware(['can:manage roles'])->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        });

        // Permission Management Routes
        Route::prefix('permissions')->name('permissions.')->middleware(['can:manage permissions'])->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('index');
            Route::get('/create', [PermissionController::class, 'create'])->name('create');
            Route::post('/', [PermissionController::class, 'store'])->name('store');
            Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
            Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
            Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
        });

        // Settings Routes
        Route::prefix('settings')->name('settings.')->middleware(['can:manage settings'])->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::post('/', [SettingsController::class, 'update'])->name('update');
            Route::post('/clear-cache', [SettingsController::class, 'clearCache'])->name('clear-cache');
            Route::post('/test-smtp', [SettingsController::class, 'testSMTPConnection'])->name('test-smtp');
            Route::post('/test-email', [SettingsController::class, 'sendTestEmail'])->name('test-email');
            Route::get('/onesignal', [OneSignalSettingsController::class, 'index'])->name('onesignal');
            Route::put('/onesignal', [OneSignalSettingsController::class, 'update'])->name('updateOneSignal');
        });
        
        // مسارات إدارة سجلات تقييد معدل الطلبات
        Route::prefix('security')->name('security.')->middleware(['can:manage security'])->group(function () {
            Route::get('/rate-limit-logs', [RateLimitLogController::class, 'index'])->name('rate-limit-logs.index');
            Route::delete('/rate-limit-logs/{log}', [RateLimitLogController::class, 'destroy'])->name('rate-limit-logs.destroy');
            Route::delete('/rate-limit-logs', [RateLimitLogController::class, 'destroyAll'])->name('rate-limit-logs.destroy-all');
            Route::post('/rate-limit-logs/block-ip', [RateLimitLogController::class, 'blockIp'])->name('rate-limit-logs.block-ip');
        });
    });

    // Message Routes
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/compose', [MessageController::class, 'compose'])->name('compose');
        Route::post('/send', [MessageController::class, 'send'])->name('send');
        Route::get('/sent', [MessageController::class, 'sent'])->name('sent');
        Route::get('/drafts', [MessageController::class, 'drafts'])->name('drafts');
        Route::get('/trash', [MessageController::class, 'trash'])->name('trash');
        Route::get('/received', [MessageController::class, 'received'])->name('received');
        Route::get('/important', [MessageController::class, 'important'])->name('important');
        Route::get('/{id}/reply', [MessageController::class, 'reply'])->name('reply');
        Route::post('/{id}/send-reply', [MessageController::class, 'sendReply'])->name('send-reply');
        Route::post('/{id}/mark-as-read', [MessageController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/{id}/mark-as-unread', [MessageController::class, 'markAsUnread'])->name('mark-as-unread');
        Route::get('/{id}', [MessageController::class, 'show'])->name('show');
        Route::delete('/{id}', [MessageController::class, 'delete'])->name('delete');
    });

    // User Management Routes
    // مسارات خاصة بالمسؤولين فقط
    
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::middleware(['can:admin users'])->group(function () {
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
    
    // مسارات يمكن للمستخدم العادي الوصول إليها (للملف الشخصي)
   
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('can:manage users');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('can:manage users');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('can:manage users');
    
    // مسارات إدارة الأدوار والصلاحيات (للمسؤولين فقط)
    Route::get('users/{user}/permissions-roles', [UserController::class, 'permissions_roles'])
        ->name('users.permissions-roles')->middleware('can:manage roles');
    Route::put('users/{user}/permissions-roles', [UserController::class, 'update_permissions_roles'])
        ->name('users.update-permissions-roles')->middleware('can:manage permissions');

    // Role Management Routes
    Route::resource('roles', RoleController::class)->middleware(['can:manage roles']);

    // Permission Management Routes
    Route::resource('permissions', PermissionController::class)->middleware(['can:manage permissions']);
    // School Class Management Routes
    Route::resource('school-classes', SchoolClassController::class)->middleware(['can:manage school classes']);

    // Subject Management Routes
    Route::resource('subjects', SubjectController::class)->middleware(['can:manage subjects']);

    // Semester Management Routes
    Route::resource('semesters', SemesterController::class)->middleware(['can:manage semesters']);

    // Article Management Routes
    Route::prefix('articles')->name('articles.')->group(function () {
        Route::get('/', [ArticleController::class, 'index'])->name('index')->middleware(['can:manage articles']);
        Route::get('/create', [ArticleController::class, 'create'])->name('create')->middleware(['can:manage articles']);
        Route::post('/', [ArticleController::class, 'store'])->name('store');
        Route::get('/{article}', [ArticleController::class, 'show'])->name('show');
        Route::get('/{article}/edit', [ArticleController::class, 'edit'])->name('edit');
        Route::put('/{article}', [ArticleController::class, 'update'])->name('update');
        Route::delete('/{article}', [ArticleController::class, 'destroy'])->name('destroy');
        Route::post('/{article}/publish', [ArticleController::class, 'publish'])->name('publish');
        Route::post('/{article}/unpublish', [ArticleController::class, 'unpublish'])->name('unpublish');
        Route::post('/upload-file', [ArticleController::class, 'uploadFile'])->name('upload-file');
        Route::post('/remove-file', [ArticleController::class, 'removeFile'])->name('remove-file');
    });

    // News Management Routes
    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', [NewsController::class, 'index'])->name('index')->middleware(['can:manage news']);
        Route::get('/create', [NewsController::class, 'create'])->name('create')->middleware(['can:manage news']);
        Route::post('/', [NewsController::class, 'store'])->name('store');
        Route::get('/{news}/edit', [NewsController::class, 'edit'])->name('edit');
        Route::put('/{news}', [NewsController::class, 'update'])->name('update');
        Route::delete('/{news}', [NewsController::class, 'destroy'])->name('destroy');
        Route::patch('/{news}/toggle-status', [NewsController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/{news}/toggle-featured', [NewsController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::get('/data', [NewsController::class, 'getData'])->name('data');
    });

    // Category Management Routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index')->middleware(['can:manage categories']);
        Route::get('/create', [CategoryController::class, 'create'])->name('create')->middleware(['can:manage categories']);
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::post('/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
    });

    // File Management Routes
    Route::prefix('files')->name('files.')->group(function () {
        Route::get('/', [FileController::class, 'index'])->name('index');
        Route::get('/create', [FileController::class, 'create'])->name('create');
        Route::post('/', [FileController::class, 'store'])->name('store');
        Route::get('/{file}', [FileController::class, 'show'])->name('show');
        Route::get('/{file}/edit', [FileController::class, 'edit'])->name('edit');
        Route::put('/{file}', [FileController::class, 'update'])->name('update');
        Route::delete('/{file}', [FileController::class, 'destroy'])->name('destroy');
        Route::get('/{file}/download', [FileController::class, 'download'])->name('download');
    });

    // Notification Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::get('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
        Route::post('/delete-selected', [NotificationController::class, 'deleteSelected'])->name('delete-selected');
        Route::post('/handle-actions', [NotificationController::class, 'handleActions'])->name('handle-actions');
        Route::get('/delete/{id}', [NotificationController::class, 'delete'])->name('delete');
    });
  

    // مسارات المراقبة
    Route::prefix('monitoring')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/', [MonitoringController::class, 'index'])->name('monitoring')->middleware(['can:manage monitoring']);
        Route::get('/monitorboard', [MonitoringController::class, 'monitorboard'])->name('monitoring.monitorboard')->middleware(['can:manage monitoring']);
        Route::get('/stats', [MonitoringController::class, 'getMonitoringData'])->name('monitoring.stats');
        Route::delete('/errors/{errorId}', [MonitoringController::class, 'deleteError'])->name('api.monitoring.delete-error');

        Route::get('/error-logs', [MonitoringController::class, 'getErrorLogs'])->name('monitoring.error-logs')->middleware(['can:manage monitoring']);
        Route::post('/delete-error', [MonitoringController::class, 'deleteError'])->name('monitoring.delete-error')->middleware(['can:manage monitoring']);
        Route::post('/clear-error-logs', [MonitoringController::class, 'clearErrorLogs'])->name('monitoring.clear-error-logs')->middleware(['can:manage monitoring']);
        
        // إضافة مسار صفحة الزوار النشطين
        Route::get('/active-visitors', [MonitoringController::class, 'activeVisitors'])->name('monitoring.active-visitors')->middleware(['can:manage monitoring']);
        Route::get('/active-visitors/data', [MonitoringController::class, 'getActiveVisitorsData'])->name('monitoring.active-visitors.data')->middleware(['can:manage monitoring']);
    });
    // مسارات تتبع الزوار (بدون مصادقة)
    Route::post('/track-visitor', [MonitoringController::class, 'trackVisitor'])
        ->middleware(['throttle:60,1'])
        ->name('track.visitor');
    Route::post('/update-visitor-activity', [MonitoringController::class, 'updateVisitorActivity'])
        ->middleware(['throttle:120,1'])
        ->name('track.visitor.activity');
    
    // Performance Routes
    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/', [PerformanceController::class, 'index'])->name('index')->middleware(['can:manage performance']);
        Route::get('/metrics', [PerformanceController::class, 'getMetrics'])->name('metrics');
        Route::get('/metrics/data', [PerformanceController::class, 'getMetricsData'])->name('data');
    });

    // Security Routes
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/', [SecurityLogController::class, 'index'])->name('index')->middleware(['can:manage security']);
        Route::get('/logs', [SecurityLogController::class, 'logs'])->name('logs');
        Route::get('/analytics', [SecurityLogController::class, 'analytics'])->name('analytics');
        Route::get('/blocked-ips', [SecurityLogController::class, 'blockedIps'])->name('blocked-ips');
        Route::get('/trusted-ips', [SecurityLogController::class, 'trustedIps'])->name('trusted-ips');
        Route::post('/logs/{log}/resolve', [SecurityLogController::class, 'resolve'])->name('logs.resolve');
        Route::delete('/logs/{log}', [SecurityLogController::class, 'destroy'])->name('logs.destroy');
        Route::post('/blocked-ips', [SecurityLogController::class, 'blockIp'])->name('block-ip');
        Route::post('/trusted-ips', [SecurityLogController::class, 'trustIp'])->name('trust-ip');
        Route::get('/export', [SecurityLogController::class, 'export'])->name('export');
    });

    // Sitemap Routes
    Route::prefix('sitemap')->name('sitemap.')->group(function () {
        Route::get('/', [SitemapController::class, 'index'])->name('index')->middleware('can:manage sitemap');
        Route::get('/manage', [SitemapController::class, 'manageIndex'])->name('manage');
        Route::match(['get', 'post'], '/generate', [SitemapController::class, 'generate'])->name('generate');
        Route::post('/update-inclusion', [SitemapController::class, 'updateResourceInclusion'])->name('update-inclusion');
        Route::delete('/{type}/{database}', [SitemapController::class, 'delete'])->name('delete');
    });

    // Calendar Routes
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar-events', [CalendarController::class, 'getEvents'])->name('calendar.events');
    Route::get('/dashboard/calendar-events', [CalendarController::class, 'getEvents'])->name('dashboard.calendar.events');
    Route::post('/calendar/store', [CalendarController::class, 'store'])->name('calendar.store');
    Route::put('/calendar/{event}', [CalendarController::class, 'update'])->name('calendar.update');
    Route::delete('/calendar/{event}', [CalendarController::class, 'destroy'])->name('calendar.destroy');

    // Redis Routes
    Route::prefix('redis')->name('redis.')->group(function () {
        Route::get('/', [RedisController::class, 'index'])->name('index');
        Route::post('/add', [RedisController::class, 'addKey'])->name('addKey');
        Route::delete('/delete/{key}', [RedisController::class, 'deleteKey'])->name('deleteKey');
        Route::post('/clean-keys', [RedisController::class, 'cleanKeys'])->name('cleanKeys');
        Route::get('/env-settings', [RedisController::class, 'showEnvSettings'])->name('envSettings');
        Route::post('/update-env', [RedisController::class, 'updateEnvSettings'])->name('updateEnv');
        Route::get('/test', [RedisController::class, 'testRedisConnection'])->name('testRedisConnection');

    });

    // Activities routes
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('/activities/load-more', [ActivityController::class, 'loadMore'])->name('activities.load-more');
    Route::get('/activities/clean-old', [ActivityController::class, 'cleanOldActivities'])->name('activities.clean-old')->middleware('can:manage activities');
});

// Frontend Routes
Route::prefix('{database}')->group(function () {
    Route::prefix('lesson')->group(function () {
        Route::get('/', [GradeOneController::class, 'index'])->name('frontend.lesson.index');
        Route::get('/{id}', [GradeOneController::class, 'show'])->name('frontend.lesson.show');
        Route::get('subjects/{subject}', [GradeOneController::class, 'showSubject'])->name('frontend.subjects.show');
        Route::get('subjects/{subject}/articles/{semester}/{category}', [GradeOneController::class, 'subjectArticles'])->name('frontend.subject.articles');
        Route::get('/articles/{article}', [GradeOneController::class, 'showArticle'])->name('frontend.articles.show');
        Route::get('files/download/{id}', [FileController::class, 'downloadFile'])->name('files.download');
    });

    // Keywords for the frontend
    Route::get('/keywords', [KeywordController::class, 'index'])->name('frontend.keywords.index');
    Route::get('/keywords/{keywords}', [KeywordController::class, 'indexByKeyword'])->name('keywords.indexByKeyword');

    // News Routes for the frontend
    Route::prefix('news')->group(function () {
        Route::get('/', [FrontendNewsController::class, 'index'])->name('content.frontend.news.index');
        Route::get('/category/{category}', [FrontendNewsController::class, 'category'])->name('content.frontend.news.category');
        Route::get('/filter', [FrontendNewsController::class, 'filterNewsByCategory'])->name('content.frontend.news.filter');
        Route::get('/{id}', [FrontendNewsController::class, 'show'])->name('content.frontend.news.show');

        // Comments & Reactions Routes
        Route::middleware(['auth'])->group(function () {
            Route::post('/comments', [CommentController::class, 'store'])->name('frontend.comments.store');
            Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('frontend.comments.destroy');
            Route::post('/reactions', [ReactionController::class, 'store'])->name('frontend.reactions.store');
        });
    });
});

// Front Pages Routes
Route::get('/about-us', [FrontController::class, 'aboutUs'])->name('about.us');
Route::get('/contact-us', [FrontController::class, 'contactUs'])->name('contact.us');
Route::post('/contact-us', [FrontController::class, 'submitContact'])->name('contact.submit');

// Categories for the frontend (خارج مجموعة database)
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('content.frontend.categories.show');

// Legal Routes
Route::get('privacy-policy', [LegalController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('terms-of-service', [LegalController::class, 'termsOfService'])->name('terms-of-service');
Route::get('cookie-policy', [LegalController::class, 'cookiePolicy'])->name('cookie-policy');
Route::get('disclaimer', [LegalController::class, 'disclaimer'])->name('disclaimer');

// Filter and API Routes
Route::get('/filter-files', [FilterController::class, 'index'])->name('files.filter');
Route::get('/api/subjects/{classId}', [FilterController::class, 'getSubjectsByClass']);
Route::get('/api/semesters/{subjectId}', [FilterController::class, 'getSemestersBySubject']);
Route::get('/api/files/{semesterId}', [FilterController::class, 'getFileTypesBySemester']);

// Calendar Events Route
Route::get('/app-calendar-events', [CalendarController::class, 'getEvents']);

// File Download Routes
Route::get('/download/{file}', [FileController::class, 'showDownloadPage'])->name('download.page');
Route::get('/download-wait/{file}', [FileController::class, 'processDownload'])->name('download.wait');
