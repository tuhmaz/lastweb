<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        Log::info('CustomVerifyEmail notification instance created');
    }

    /**
     * إنشاء رابط التحقق.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        try {
            Log::info('Generating verification URL', [
                'user_id' => $notifiable->getKey(),
                'email' => $notifiable->getEmailForVerification()
            ]);

            if (static::$createUrlCallback) {
                return call_user_func(static::$createUrlCallback, $notifiable);
            }

            $url = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            Log::info('Generated verification URL', [
                'user_id' => $notifiable->getKey(),
                'url_hash' => substr(sha1($url), 0, 8),
                'expires' => now()->addMinutes(Config::get('auth.verification.expire', 60))
            ]);

            return $url;
        } catch (\Exception $e) {
            Log::error('Error generating verification URL', [
                'user_id' => $notifiable->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * بناء رسالة الإشعار.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        try {
            Log::info('Building verification email', [
                'user_id' => $notifiable->id,
                'email' => $notifiable->email
            ]);

            $verificationUrl = $this->verificationUrl($notifiable);

            return (new MailMessage)
                ->subject('تأكيد عنوان البريد الإلكتروني - ' . config('app.name'))
                ->greeting('مرحباً!')
                ->line('شكراً لتسجيلك في ' . config('app.name') . '.')
                ->line('يرجى النقر على الزر أدناه لتأكيد عنوان بريدك الإلكتروني.')
                ->action('تأكيد عنوان البريد الإلكتروني', $verificationUrl)
                ->line('إذا لم تقم بإنشاء حساب، فلا حاجة لاتخاذ أي إجراء آخر.')
                ->salutation('مع تحيات فريق ' . config('app.name'))
                ->theme('default')
                ->from(config('mail.from.address'), config('mail.from.name'));
        } catch (\Exception $e) {
            Log::error('Error building verification email', [
                'user_id' => $notifiable->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
