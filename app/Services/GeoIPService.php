<?php

namespace App\Services;

use GeoIp2\Database\Reader;

class GeoIPService
{
    protected $reader;

    public function __construct()
    {
        // مسار قاعدة البيانات
        $this->reader = new Reader(storage_path('geoip/GeoLite2-City.mmdb'));
    }

    public function getGeoData($ip)
    {
        try {
            $record = $this->reader->city($ip);

            return [
                'country' => $record->country->name ?? 'Unknown',
                'city' => $record->city->name ?? 'Unknown',
                'lat' => $record->location->latitude ?? null,
                'lon' => $record->location->longitude ?? null,
            ];
        } catch (\Exception $e) {
            \Log::error('Error fetching geolocation for IP: ' . $ip . '. Error: ' . $e->getMessage());
            return [
                'country' => 'Unknown',
                'city' => 'Unknown',
                'lat' => null,
                'lon' => null,
            ];
        }
    }
}
