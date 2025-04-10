<?php

namespace App\Services;

use OneSignal;

class OneSignalService
{
    public function sendNotification($title, $message, $url = null, $data = [], $segments = ['All'])
    {
        try {
            $params = [
                'headings' => ['en' => $title],
                'contents' => ['en' => $message],
                'included_segments' => $segments,
                'url' => $url,
                'data' => $data,
                'chrome_web_icon' => asset('storage/' . config('settings.site_logo')),
                'chrome_web_badge' => asset('storage/' . config('settings.site_logo'))
            ];

            return OneSignal::sendNotificationCustom($params);
        } catch (\Exception $e) {
            \Log::error('OneSignal Notification Error: ' . $e->getMessage());
            return false;
        }
    }
}
