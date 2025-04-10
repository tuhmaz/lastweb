<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;

class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(Router $router): void
    {
        // تسجيل الـ middleware
        $router->aliasMiddleware('check.file.access', \App\Http\Middleware\CheckFileAccess::class);
        $router->aliasMiddleware('https', \App\Http\Middleware\HttpsProtocol::class);
        $router->aliasMiddleware('api.throttle', \App\Http\Middleware\ApiRateLimiter::class);
        
        // تطبيق middleware الـ HTTPS على جميع الطلبات في بيئة الإنتاج
        if ($this->app->environment('production')) {
            $this->app->make(Kernel::class)->pushMiddleware(\App\Http\Middleware\HttpsProtocol::class);
        }
    }
}
