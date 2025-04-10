<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class OneSignalSettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::first();
        return view('content.dashboard.settings.onesignal-settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'onesignal_app_id' => 'required|string',
            'onesignal_rest_api_key' => 'required|string',
            'onesignal_user_auth_key' => 'required|string',
        ]);

        try {
            $settings = Setting::first();
            if (!$settings) {
                $settings = new Setting();
            }

            $settings->onesignal_app_id = $request->onesignal_app_id;
            $settings->onesignal_rest_api_key = $request->onesignal_rest_api_key;
            $settings->onesignal_user_auth_key = $request->onesignal_user_auth_key;
            $settings->save();

            // تحديث ملف .env
            $this->updateEnvFile([
                'ONESIGNAL_APP_ID' => $request->onesignal_app_id,
                'ONESIGNAL_REST_API_KEY' => $request->onesignal_rest_api_key,
                'ONESIGNAL_USER_AUTH_KEY' => $request->onesignal_user_auth_key,
            ]);

            // مسح الكاش
            Cache::forget('settings');

            return redirect()->back()->with('success', __('OneSignal settings updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', __('An error occurred while updating settings.'))
                           ->withInput();
        }
    }

    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            if (strpos($envContent, $key) !== false) {
                $envContent = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $envContent
                );
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envFile, $envContent);
    }
}
