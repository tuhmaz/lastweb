<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // You can register bindings or services here if needed.
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // تجنب الوصول إلى قاعدة البيانات خلال عملية الترحيل
        if ($this->app->runningInConsole() && 
            (strpos($this->app['request']->server('argv')[1] ?? '', 'migrate') !== false)) {
            return;
        }

        try {
            // Check if the settings table exists before attempting to load settings
            if (Schema::hasTable('settings')) {
                // Load settings from the database
                $settings = Setting::all();
                
                foreach ($settings as $setting) {
                    config([$setting->key => $setting->value]);
                }
            }
        } catch (\Exception $e) {
            // Log the error but don't stop the application
            Log::error('Error in SettingsServiceProvider boot: ' . $e->getMessage());
        }
    }
}
