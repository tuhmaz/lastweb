<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class LocaleServiceProvider extends ServiceProvider
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
    public function boot(): void
    {
        // تعيين اللغة الافتراضية
        App::setLocale(Config::get('app.locale'));

        // تعيين اتجاه النص للغة العربية
        if (App::isLocale('ar')) {
            app()->setLocale('ar');
            session(['locale' => 'ar']);
            session(['dir' => 'rtl']);
        }
    }
}
