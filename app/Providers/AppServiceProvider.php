<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;
use Laravel\Passport\Passport;
use App\Models\Article;
use App\Observers\ArticleObserver;
use App\Models\News;
use App\Observers\NewsObserver;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    // ...

    public function boot(): void
    {
        // تفعيل HTTPS لجميع الروابط في بيئة الإنتاج
        if (App::environment('production') && Config::get('secure-connections.force_https', true)) {
            URL::forceScheme('https');
            
            // تعيين ملفات الكوكيز لتكون آمنة في بيئة الإنتاج
            Config::set('session.secure', true);
            Config::set('session.http_only', true);
            Config::set('session.same_site', 'lax');
            
            // تعيين ملفات الكوكيز الخاصة بالمصادقة لتكون آمنة
            Config::set('sanctum.middleware.encrypt_cookies', true);
        }

        // Configure VarDumper
        VarDumper::setHandler(function ($var) {
            $dumper = new HtmlDumper();
            $dumper->setStyles([
                'default' => 'background-color:#fff; color:#222; line-height:1.2em; font-weight:normal; font:12px Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:100000',
                'search-input' => 'id:dump-search; name:dump-search;'
            ]);
            $cloner = new VarCloner();
            $dumper->dump($cloner->cloneVar($var));
        });

        // تجنب الوصول إلى قاعدة البيانات خلال عملية الترحيل
        if ($this->app->runningInConsole() && 
            (strpos($this->app['request']->server('argv')[1] ?? '', 'migrate') !== false)) {
            return;
        }

        try {
            // تأكد من وجود الجداول المطلوبة قبل تنفيذ التعليمات البرمجية التالية
            if (Schema::hasTable('settings')) {
                // Load settings from database
                $settings = \DB::table('settings')->get();
                foreach ($settings as $setting) {
                    Config::set($setting->key, $setting->value);
                }

                // Set application locale from session or settings
                $locale = config('app.locale', 'ar');
                $locale = session('locale', function() {
                    return config('settings.default_language', 'ar');
                });
                
                if (in_array($locale, ['en', 'ar'])) {
                    app()->setLocale($locale);
                }
            }

            // Load Passport keys
            Passport::loadKeysFrom(__DIR__.'/../secrets/oauth');

            // Custom Vite styles
            Vite::useStyleTagAttributes(function (?string $src, string $url, ?array $chunk, ?array $manifest) {
                if ($src !== null) {
                    return [
                        'class' => preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?core)-?.*/i", $src) ? 'template-customizer-core-css' :
                                  (preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?theme)-?.*/i", $src) ? 'template-customizer-theme-css' : '')
                    ];
                }
                return [];
            });

            // Register observers
            Article::observe(ArticleObserver::class);
            News::observe(NewsObserver::class);

        } catch (\Exception $e) {
            // Log the error but don't stop the application
            \Log::error('Error in AppServiceProvider boot: ' . $e->getMessage());
        }
    }

    // ...
}
