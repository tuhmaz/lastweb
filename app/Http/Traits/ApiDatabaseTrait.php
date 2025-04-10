<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Config;

trait ApiDatabaseTrait
{
    protected function switchDatabase($country)
    {
        $validCountries = ['jo', 'sa', 'eg', 'ps'];
        $country = strtolower($country);
        
        if (!in_array($country, $validCountries)) {
            $country = 'jo'; // القيمة الافتراضية
        }

        if (Config::get('database.default') !== $country) {
            Config::set('database.default', $country);
            Config::set('cache.default', $country . '_redis');
            
            // تكوين Redis للدولة
            $cacheStore = $country . '_redis';
            if (!array_key_exists($cacheStore, Config::get('cache.stores', []))) {
                Config::set('cache.stores.' . $cacheStore, [
                    'driver' => 'redis',
                    'connection' => $cacheStore,
                    'lock_connection' => 'default',
                ]);
            }
        }

        return $country;
    }

    protected function getResponseWithDatabase($data, $status = true, $message = null)
    {
        return response()->json([
            'status' => $status,
            'database' => Config::get('database.default'),
            'message' => $message,
            'data' => $data
        ]);
    }
}
