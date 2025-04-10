<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\TrackUserActivity;

class MonitoringServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // تكوين السجلات
        $this->app->singleton('monitoring.logger', function ($app) {
            return Log::build([
                'driver' => 'daily',
                'path' => storage_path('logs/monitoring.log'),
                'level' => 'debug',
                'days' => 14,
            ]);
        });
    }

    public function boot(): void
    {
        // تسجيل الـ Middleware
        Route::middleware('web')->group(function () {
            Route::pushMiddlewareToGroup('web', TrackUserActivity::class);
        });

        // تسجيل مسارات المراقبة
        Route::middleware(['web', 'auth'])->group(function () {
            Route::get('/monitoring', [\App\Http\Controllers\MonitoringController::class, 'index'])
                ->name('monitoring.index')
                ->middleware('can:view monitoring');

            Route::get('/monitoring/stats', [\App\Http\Controllers\MonitoringController::class, 'getStats'])
                ->name('monitoring.stats')
                ->middleware('can:view monitoring');

            Route::post('/api/monitoring/clear-cache', [\App\Http\Controllers\MonitoringController::class, 'clearCache'])
                ->name('monitoring.clear-cache')
                ->middleware('can:manage cache');
        });
    }
}
