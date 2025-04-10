<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Services\OneSignalService;

class OneSignalChannel
{
    protected $oneSignalService;

    public function __construct(OneSignalService $oneSignalService)
    {
        $this->oneSignalService = $oneSignalService;
    }

    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toOneSignal')) {
            return $notification->toOneSignal($notifiable);
        }
    }
}
