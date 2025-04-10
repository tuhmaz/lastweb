<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class SwitchDatabase
{

    public function handle($request, Closure $next)
    {
        // تحقق من وجود country في الجلسة أولاً
        $selectedCountry = $request->input('country', session('country', 'jo'));

        // تجنب تغيير الاتصال إذا كان نفس القيمة الحالية
        if (Config::get('database.default') !== $selectedCountry) {
            switch ($selectedCountry) {
                case 'sa':
                case 'eg':
                case 'ps':
                    Config::set('database.default', $selectedCountry);
                    Config::set('cache.default', $selectedCountry . '_redis');
                    session(['country' => $selectedCountry]);
                    break;
                default:
                    Config::set('database.default', 'jo');
                    Config::set('cache.default', 'jo_redis');
                    session(['country' => 'jo']);
                    $selectedCountry = 'jo';
                    break;
            }
        }

        // تأكد من أن إعدادات Redis للدولة المحددة موجودة
        $cacheStore = $selectedCountry . '_redis';
        
        // تكوين Redis لكل دولة (إذا لم يكن موجودًا بالفعل)
        if (!array_key_exists($cacheStore, Config::get('cache.stores', []))) {
            // إضافة متجر التخزين المؤقت إذا لم يكن موجودًا
            Config::set('cache.stores.' . $cacheStore, [
                'driver' => 'redis',
                'connection' => $cacheStore,
                'lock_connection' => 'default',
            ]);
        }

        // تكوين اتصال Redis للدولة المحددة (إذا لم يكن موجودًا بالفعل)
        if (!array_key_exists($cacheStore, Config::get('database.redis', []))) {
            $redisPrefix = $selectedCountry . ':';
            Config::set('database.redis.' . $cacheStore, [
                'url' => env('REDIS_URL'),
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'password' => env('REDIS_PASSWORD'),
                'port' => env('REDIS_PORT', '6379'),
                'database' => env('REDIS_DB', '0'),
                'prefix' => $redisPrefix,
            ]);
        }

        // إعادة تعيين ذاكرة التخزين المؤقت لضمان استخدام الإعدادات الجديدة
        Cache::forgetDriver($cacheStore);

        return $next($request);
    }
}
