<?php

namespace App\Providers;

use GeoIp2\Database\Reader;
use Illuminate\Support\ServiceProvider;

class GeoIPServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('geoip', function () {
            return new Reader(storage_path('geoip/GeoLite2-City.mmdb'));
        });
    }

    public function boot()
    {
        //
    }
}