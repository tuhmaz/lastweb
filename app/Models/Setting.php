<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'site_name',
        'site_email',
        'site_logo',
        'site_favicon',
        'site_description',
        'site_keywords',
        'site_status',
        'site_message',
        'footer_text',
        'footer_link',
        'google_analytics',
        'facebook_pixel',
        'recaptcha_site_key',
        'recaptcha_secret_key',
        'onesignal_app_id',
        'onesignal_rest_api_key',
        'onesignal_user_auth_key',
    ];

    protected $casts = [
        'site_status' => 'boolean',
    ];

    public $timestamps = false;

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        return Cache::rememberForever('settings', function () {
            return self::pluck('value', 'key')->toArray();
        })[$key] ?? $default;
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget('settings');

        return $setting;
    }

    /**
     * Delete a setting
     */
    public static function remove($key)
    {
        self::where('key', $key)->delete();
        Cache::forget('settings');
    }

    public static function getSettings()
    {
        return self::first();
    }
}
