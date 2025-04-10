<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\SubjectsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GradeController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SocialAuthController;

// تطبيق تقييد معدل الطلبات على جميع مسارات API وحماية API
Route::middleware(['api', 'App\Http\Middleware\ApiProtection'])->group(function () {
    
    // Public routes
    Route::get('{country}/news', [NewsController::class, 'index']);
    Route::get('{country}/news/{id}', [NewsController::class, 'show']);
    Route::get('{country}/categories', [NewsController::class, 'getCategories']);
    
    // Social Auth Routes
    Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle']);
    Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
    
    // Lesson Routes (Public)
    Route::get('{country}/lesson', [GradeController::class, 'index']);
    Route::get('{country}/lesson/{id}', [GradeController::class, 'show']);
    Route::get('{country}/lesson/subjects/{id}', [GradeController::class, 'showSubject']);
    Route::get('{country}/lesson/subjects/{subject}/articles/{semester}/{category}', [GradeController::class, 'subjectArticles']);
    Route::get('{country}/lesson/articles/{id}', [GradeController::class, 'showArticle']);
    Route::get('{country}/lesson/files/{id}/download', [GradeController::class, 'downloadFile']);
    
    // Auth routes
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);

        // Comments Routes with throttle middleware
Route::middleware(['api', 'throttle:60,1'])->prefix('{database}')->group(function () {
    // مسارات التعليقات للأخبار
    Route::get('/news/{id}/comments', [App\Http\Controllers\Api\CommentController::class, 'index'])->name('api.comments.news.index');
    Route::get('/news/{id}/comments/{comment}', [App\Http\Controllers\Api\CommentController::class, 'show'])->name('api.comments.news.show');
    Route::post('/news/{id}/comments', [App\Http\Controllers\Api\CommentController::class, 'store'])->middleware('auth:sanctum')->name('api.comments.news.store');
    Route::delete('/news/{id}/comments/{comment}', [App\Http\Controllers\Api\CommentController::class, 'destroy'])->middleware('auth:sanctum')->name('api.comments.news.destroy');
    
    // مسارات التعليقات للمقالات
    Route::get('/lesson/articles/{id}/comments', [App\Http\Controllers\Api\CommentController::class, 'index'])->name('api.comments.articles.index');
    Route::get('/lesson/articles/{id}/comments/{comment}', [App\Http\Controllers\Api\CommentController::class, 'show'])->name('api.comments.articles.show');
    Route::post('/lesson/articles/{id}/comments', [App\Http\Controllers\Api\CommentController::class, 'store'])->middleware('auth:sanctum')->name('api.comments.articles.store');
    Route::delete('/lesson/articles/{id}/comments/{comment}', [App\Http\Controllers\Api\CommentController::class, 'destroy'])->middleware('auth:sanctum')->name('api.comments.articles.destroy');

    // مسارات التفاعلات للتعليقات
    Route::get('/comments/{comment}/reactions', [App\Http\Controllers\Api\ReactionController::class, 'index'])->name('api.reactions.index');
    Route::post('/comments/{comment}/reactions', [App\Http\Controllers\Api\ReactionController::class, 'store'])->middleware('auth:sanctum')->name('api.reactions.store');
    Route::get('/comments/{comment}/reactions/{reaction}', [App\Http\Controllers\Api\ReactionController::class, 'show'])->name('api.reactions.show');
    Route::delete('/comments/{comment}/reactions/{reaction}', [App\Http\Controllers\Api\ReactionController::class, 'destroy'])->middleware('auth:sanctum')->name('api.reactions.destroy');
});
        
       

        // Subjects Routes
        Route::get('{country}/subjects', [SubjectsController::class, 'index']);
        Route::get('{country}/subjects/{id}', [SubjectsController::class, 'show']);
        
        // Comments Routes
        Route::get('/{country}/comments/{type}/{id}', [CommentController::class, 'index']);
        Route::post('/{country}/comments', [CommentController::class, 'store']);
        Route::delete('/{country}/comments/{id}', [CommentController::class, 'destroy']);
        
        // User Route
        Route::get('{country}/user', function (Request $request) {
            return response()->json([
                'status' => true,
                'database' => config('database.default'),
                'user' => $request->user()
            ]);
        });

        // مسارات لوحة التحكم
        Route::prefix('dashboard')->group(function () {
            // الإحصائيات والسجلات
            Route::get('/statistics', [DashboardController::class, 'statistics']);
            Route::get('/activity-logs', [DashboardController::class, 'activityLogs']);

            // مسارات الإشعارات
            Route::prefix('notifications')->group(function () {
                Route::get('/', [NotificationController::class, 'index']);
                Route::patch('{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
                Route::post('read-all', [NotificationController::class, 'markAllAsRead']);
                Route::delete('{id}', [NotificationController::class, 'delete']);
                Route::post('handle-actions', [NotificationController::class, 'handleActions']);
                Route::delete('selected', [NotificationController::class, 'deleteSelected']);
            });

            // مسارات التفاعلات
            Route::prefix('reactions')->group(function () {
                Route::get('/', [App\Http\Controllers\Api\ReactionController::class, 'index']);
                Route::post('/', [App\Http\Controllers\Api\ReactionController::class, 'store']);
                Route::get('/{id}', [App\Http\Controllers\Api\ReactionController::class, 'show']);
                Route::delete('/{id}', [App\Http\Controllers\Api\ReactionController::class, 'destroy']);
                Route::get('/by-comment/{commentId}', [App\Http\Controllers\Api\ReactionController::class, 'getReactionsByComment']);
                Route::get('/user-reaction/{commentId}', [App\Http\Controllers\Api\ReactionController::class, 'getUserReaction']);
            });

            // مسارات المستخدمين
            Route::prefix('users')->group(function () {
                Route::get('/', [UserController::class, 'index']);
                Route::post('/', [UserController::class, 'store']);
                Route::get('{id}', [UserController::class, 'show']);
                Route::put('{id}', [UserController::class, 'updateProfile']);
                Route::post('{id}/update-profile-photo', [UserController::class, 'updateProfilePhoto']);
            });

            Route::get('/roles', [UserController::class, 'getRoles']);

            // Messages Routes
        Route::prefix('messages')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\MessageController::class, 'index'])
                ->name('api.dashboard.messages.index');
            Route::post('/', [App\Http\Controllers\Api\MessageController::class, 'store'])
                ->name('api.dashboard.messages.store');
            Route::get('/sent', [App\Http\Controllers\Api\MessageController::class, 'sent'])
                ->name('api.dashboard.messages.sent');
            Route::get('/{id}', [App\Http\Controllers\Api\MessageController::class, 'show'])
                ->name('api.dashboard.messages.show');
            Route::post('/{id}/reply', [App\Http\Controllers\Api\MessageController::class, 'reply'])
                ->name('api.dashboard.messages.reply');
            Route::post('/{id}/mark-as-read', [App\Http\Controllers\Api\MessageController::class, 'markAsRead'])
                ->name('api.dashboard.messages.mark-as-read');
            Route::post('/{id}/toggle-important', [App\Http\Controllers\Api\MessageController::class, 'toggleImportant'])
                ->name('api.dashboard.messages.toggle-important');
            Route::delete('/{id}', [App\Http\Controllers\Api\MessageController::class, 'delete'])
                ->name('api.dashboard.messages.delete');
            Route::post('/delete-selected', [App\Http\Controllers\Api\MessageController::class, 'deleteSelected'])
                ->name('api.dashboard.messages.delete-selected');
        });
        });
    });
});
