<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Models\VisitorTracking;
use Carbon\Carbon;

class UpdateUserStatus
{
    public function handleLogin(Login $event)
    {
        $user = $event->user;
        $user->status = 'online';
        $user->last_seen = now();
        $user->save();

        // تحديث سجل الزيارة
        VisitorTracking::updateOrCreate(
            ['user_id' => $user->id],
            ['last_activity' => now()]
        );
    }

    public function handleLogout(Logout $event)
    {
        $user = $event->user;
        if ($user) {
            $user->status = 'offline';
            $user->last_seen = now();
            $user->save();
        }
    }
}