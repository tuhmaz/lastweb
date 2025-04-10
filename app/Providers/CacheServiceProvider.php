<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Cache\Repository;

class CacheServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // تكوين التخزين المؤقت المتقدم
        Cache::extend('advanced-redis', function ($app) {
            $config = $app['config']->get('database.redis.cache');
            
            $prefix = $app['config']->get('cache.prefix') . ':';
            
            $redis = $app['redis']->connection('cache');
            
            // تكوين خيارات Redis المتقدمة
            $redis->setOption(\Redis::OPT_PREFIX, $prefix);
            $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_JSON);
            
            return new Repository(new \Illuminate\Cache\RedisStore(
                $redis,
                $prefix,
                config('cache.ttl', 3600)
            ));
        });
    }
}
