<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        $settings = [
            // General Settings
            'site_name' => Setting::get('site_name', config('settings.site_name')),
            'site_email' => Setting::get('site_email', config('settings.admin_email')),
            'site_description' => Setting::get('site_description', config('settings.site_description')),
            'site_logo' => Setting::get('site_logo', config('settings.site_logo')),
            'site_favicon' => Setting::get('site_favicon', config('settings.site_favicon')),
            'site_language' => Setting::get('site_language', config('settings.site_language')),
            'timezone' => Setting::get('timezone', config('settings.timezone')),

            // Appearance Settings
            'primary_color' => Setting::get('primary_color', '#696cff'),
            'secondary_color' => Setting::get('secondary_color', '#8592a3'),

            // SEO Settings
            'meta_title' => Setting::get('meta_title', config('settings.meta_title')),
            'meta_description' => Setting::get('meta_description', config('settings.meta_description')),
            'meta_keywords' => Setting::get('meta_keywords', config('settings.meta_keywords')),
            'robots_txt' => Setting::get('robots_txt', config('settings.robots_txt')),
            'sitemap_url' => Setting::get('sitemap_url', config('settings.sitemap_url')),
            'google_analytics_id' => Setting::get('google_analytics_id', config('settings.google_analytics_id')),
            'facebook_pixel_id' => Setting::get('facebook_pixel_id', config('settings.facebook_pixel_id')),
            'canonical_url' => Setting::get('canonical_url', config('settings.canonical_url')),

            // Contact Settings
            'contact_email' => Setting::get('contact_email', config('settings.admin_email')),
            'contact_phone' => Setting::get('contact_phone', ''),
            'contact_address' => Setting::get('contact_address', ''),
            'social_facebook' => Setting::get('social_facebook', config('settings.facebook')),
            'social_twitter' => Setting::get('social_twitter', config('settings.twitter')),
            'social_linkedin' => Setting::get('social_linkedin', config('settings.linkedin')),
            'social_whatsapp' => Setting::get('social_whatsapp', config('settings.whatsapp')),
            'social_tiktok' => Setting::get('social_tiktok', config('settings.tiktok')),

            // Mail Settings
            'mail_mailer' => Setting::get('mail_mailer', config('settings.mail_mailer')),
            'mail_host' => Setting::get('mail_host', config('settings.mail_host')),
            'mail_port' => Setting::get('mail_port', config('settings.mail_port')),
            'mail_username' => Setting::get('mail_username', config('settings.mail_username')),
            'mail_password' => Setting::get('mail_password', config('settings.mail_password')),
            'mail_encryption' => Setting::get('mail_encryption', config('settings.mail_encryption')),
            'mail_from_address' => Setting::get('mail_from_address', config('settings.mail_from_address')),
            'mail_from_name' => Setting::get('mail_from_name', config('settings.mail_from_name')),

            // Notification Settings
            'notification_email' => Setting::get('notification_email', config('settings.notification_email')),
            'notification_sms' => Setting::get('notification_sms', config('settings.notification_sms')),
            'notification_push' => Setting::get('notification_push', config('settings.notification_push')),

            // Security Settings
            'two_factor_auth' => Setting::get('two_factor_auth', config('settings.two_factor_auth')),
            'auto_lock_time' => Setting::get('auto_lock_time', config('settings.auto_lock_time')),

            // ASD Settings
            'google_ads_desktop_home' => Setting::get('google_ads_desktop_home', config('settings.google_ads_desktop_home')),
            'google_ads_desktop_home_2' => Setting::get('google_ads_desktop_home_2', config('settings.google_ads_desktop_home_2')),
            'google_ads_mobile_home' => Setting::get('google_ads_mobile_home', config('settings.google_ads_mobile_home')),
            'google_ads_mobile_home_2' => Setting::get('google_ads_mobile_home_2', config('settings.google_ads_mobile_home_2')),
            'google_ads_desktop_classes' => Setting::get('google_ads_desktop_classes', config('settings.google_ads_desktop_classes')),
            'google_ads_desktop_classes_2' => Setting::get('google_ads_desktop_classes_2', config('settings.google_ads_desktop_classes_2')),
            'google_ads_desktop_subject' => Setting::get('google_ads_desktop_subject', config('settings.google_ads_desktop_subject')),
            'google_ads_desktop_subject_2' => Setting::get('google_ads_desktop_subject_2', config('settings.google_ads_desktop_subject_2')),
            'google_ads_desktop_article' => Setting::get('google_ads_desktop_article', config('settings.google_ads_desktop_article')),
            'google_ads_desktop_article_2' => Setting::get('google_ads_desktop_article_2', config('settings.google_ads_desktop_article_2')),
            'google_ads_desktop_news' => Setting::get('google_ads_desktop_news', config('settings.google_ads_desktop_news')),
            'google_ads_desktop_news_2' => Setting::get('google_ads_desktop_news_2', config('settings.google_ads_desktop_news_2')),
            'google_ads_desktop_download' => Setting::get('google_ads_desktop_download', config('settings.google_ads_desktop_download')),
            'google_ads_desktop_download_2' => Setting::get('google_ads_desktop_download_2', config('settings.google_ads_desktop_download_2')),
            'google_ads_mobile_classes' => Setting::get('google_ads_mobile_classes', config('settings.google_ads_mobile_classes')),
            'google_ads_mobile_classes_2' => Setting::get('google_ads_mobile_classes_2', config('settings.google_ads_mobile_classes_2')),
            'google_ads_mobile_subject' => Setting::get('google_ads_mobile_subject', config('settings.google_ads_mobile_subject')),
            'google_ads_mobile_subject_2' => Setting::get('google_ads_mobile_subject_2', config('settings.google_ads_mobile_subject_2')),
            'google_ads_mobile_article' => Setting::get('google_ads_mobile_article', config('settings.google_ads_mobile_article')),
            'google_ads_mobile_article_2' => Setting::get('google_ads_mobile_article_2', config('settings.google_ads_mobile_article_2')),
            'google_ads_mobile_news' => Setting::get('google_ads_mobile_news', config('settings.google_ads_mobile_news')),
            'google_ads_mobile_news_2' => Setting::get('google_ads_mobile_news_2', config('settings.google_ads_mobile_news_2')),
            'google_ads_mobile_download' => Setting::get('google_ads_mobile_download', config('settings.google_ads_mobile_download')),
            'google_ads_mobile_download_2' => Setting::get('google_ads_mobile_download_2', config('settings.google_ads_mobile_download_2')),
        ];

        return view('content.dashboard.settings.index', compact('settings'));
    }

    /**
     * Update the settings
     */
    public function update(Request $request)
    {
        try {
            $data = $request->except('_token', '_method');
            $envUpdates = [];

            foreach ($data as $key => $value) {
                if ($request->hasFile($key)) {
                    // Delete old file if exists
                    $oldValue = Setting::get($key);
                    if ($oldValue && Storage::disk('public')->exists($oldValue)) {
                        Storage::disk('public')->delete($oldValue);
                    }

                    // Store new file
                    $value = $request->file($key)->store('settings', 'public');
                }

                // Update database setting
                Setting::set($key, $value);

                // Handle language change
                if ($key === 'site_language' && in_array($value, ['en', 'ar'])) {
                    app()->setLocale($value);
                    session(['locale' => $value]);
                }

                // Add to env updates if it exists in settings config
                if (config()->has("settings.{$key}")) {
                    $envKey = $this->getEnvKey($key);
                    if ($envKey) {
                        $envUpdates[$envKey] = $value;
                    }
                }
            }

            // Update .env file if needed
            if (!empty($envUpdates)) {
                $this->updateEnvFile($envUpdates);
            }

            if (isset($data['robots_txt'])) {
                $this->updateRobotsTxt($data['robots_txt']);
            }

            // Clear config cache if mail settings were updated
            if ($this->mailSettingsWereUpdated($data)) {
                \Artisan::call('config:clear');
            }

            return response()->json([
                'success' => true,
                'message' => __('Settings updated successfully!'),
                'reload' => isset($data['site_language']) // Reload page if language was changed
            ]);

        } catch (\Exception $e) {
            Log::error('Settings update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('Error updating settings: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the corresponding ENV key for a setting
     */
    protected function getEnvKey($key)
    {
        $mappings = [
            // General Settings
            'site_name' => 'APP_NAME',
            'site_url' => 'APP_URL',
            'site_email' => 'ADMIN_EMAIL',
            
            // Mail Settings
            'mail_mailer' => 'MAIL_MAILER',
            'mail_host' => 'MAIL_HOST',
            'mail_port' => 'MAIL_PORT',
            'mail_username' => 'MAIL_USERNAME',
            'mail_password' => 'MAIL_PASSWORD',
            'mail_encryption' => 'MAIL_ENCRYPTION',
            'mail_from_address' => 'MAIL_FROM_ADDRESS',
            'mail_from_name' => 'MAIL_FROM_NAME',

            // Analytics and Tracking
            'google_analytics_id' => 'GOOGLE_ANALYTICS_ID',
            'facebook_pixel_id' => 'FACEBOOK_PIXEL_ID',
        ];

        return $mappings[$key] ?? null;
    }

    /**
     * Update the .env file
     */
    protected function updateEnvFile($updates)
    {
        if (empty($updates)) {
            return;
        }

        $envFile = base_path('.env');
        $content = File::get($envFile);

        foreach ($updates as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            // Escape any quotes
            $value = str_replace('"', '\"', $value);

            // Wrap value in quotes if it contains spaces
            if (strpos($value, ' ') !== false) {
                $value = '"' . $value . '"';
            }

            // Replace or append the env setting
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
        }

        File::put($envFile, $content);
    }

    /**
     * Check if mail settings were updated
     */
    protected function mailSettingsWereUpdated($data)
    {
        $mailSettings = [
            'mail_mailer', 'mail_host', 'mail_port', 'mail_username',
            'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'
        ];

        foreach ($mailSettings as $setting) {
            if (array_key_exists($setting, $data)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Update robots.txt file
     */
    protected function updateRobotsTxt($content)
    {
        try {
            $robotsPath = public_path('robots.txt');
            file_put_contents($robotsPath, $content);
        } catch (\Exception $e) {
            Log::error('Error updating robots.txt: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Test SMTP Connection
     */
    public function testSMTPConnection()
    {
        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = Setting::get('mail_host');
            $mail->SMTPAuth = true;
            $mail->Username = Setting::get('mail_username');
            $mail->Password = Setting::get('mail_password');
            $mail->SMTPSecure = Setting::get('mail_encryption');
            $mail->Port = Setting::get('mail_port');

            $mail->smtpConnect();

            return response()->json([
                'success' => true,
                'message' => __('SMTP connection successful')
            ]);

        } catch (Exception $e) {
            Log::error('SMTP test failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('SMTP connection failed: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send test email
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        try {
            $testEmail = $request->input('test_email');

            Mail::raw(__('This is a test email to verify SMTP settings.'), function($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject(__('SMTP Settings Test'));
            });

            return response()->json([
                'success' => true,
                'message' => __('Test email sent successfully to ') . $testEmail
            ]);

        } catch (\Exception $e) {
            Log::error('Test email failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Failed to send test email: ') . $e->getMessage()
            ], 500);
        }
    }
}
