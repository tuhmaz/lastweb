<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use App\Services\OneSignalService;
use App\Notifications\Channels\OneSignalChannel;

class OneSignalServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(OneSignalService::class, function ($app) {
            return new OneSignalService();
        });
    }

    public function boot(): void
    {
        Notification::extend('onesignal', function ($app) {
            return new OneSignalChannel($app->make(OneSignalService::class));
        });
    }
}
