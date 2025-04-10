<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RateLimiterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // تعريف Rate Limiter للـ API
        $this->app->singleton('rate-limiter:api', function () {
            return function (Request $request) {
                return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
            };
        });

        // تعريف Rate Limiter لمحاولات تسجيل الدخول
        $this->app->singleton('rate-limiter:login', function () {
            return function (Request $request) {
                return Limit::perMinute(5)->by($request->input('email').$request->ip());
            };
        });

        // تعريف Rate Limiter للملفات
        $this->app->singleton('rate-limiter:file-uploads', function () {
            return function (Request $request) {
                return Limit::perHour(100)->by($request->user()?->id ?: $request->ip());
            };
        });
    }
}
