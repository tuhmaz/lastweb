<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Boot the route service provider.
     *
     * @return void
     */
    public function boot(): void
    {
        // Define rate limiting for API routes
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Define all routes
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        // Register custom middlewares
        Route::aliasMiddleware('track.visitor', \App\Http\Middleware\TrackVisitor::class);
        Route::aliasMiddleware('dashboard.security.headers', \App\Http\Middleware\DashboardSecurityHeaders::class);
        Route::aliasMiddleware('cache.control', \App\Http\Middleware\CacheControlMiddleware::class);

        // Apply global middleware to web routes
        Route::pushMiddlewareToGroup('web', \App\Http\Middleware\TrackVisitor::class);
        Route::pushMiddlewareToGroup('web', \App\Http\Middleware\CacheControlMiddleware::class);
    }

    // app/Providers/RouteServiceProvider.php
protected function configureRateLimiting()
{
    RateLimiter::for('tracking', function (Request $request) {
        return Limit::perMinute(60)->by($request->ip());
    });
}
}
