<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        try {
            $url = Socialite::driver('google')
                ->stateless()
                ->redirect()
                ->getTargetUrl();

            return response()->json([
                'status' => true,
                'redirect_url' => $url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء محاولة الاتصال بـ Google'
            ], 500);
        }
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            // التحقق من وجود رمز المصادقة
            if (!$request->has('code')) {
                return response()->json([
                    'status' => false,
                    'message' => 'رمز المصادقة غير موجود'
                ], 400);
            }

            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();

            // البحث عن المستخدم أو إنشاء مستخدم جديد
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make(Str::random(16)),
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'profile_photo_path' => $googleUser->avatar
                ]);
            }

            // إنشاء رمز المصادقة
            $token = $user->createToken('google_auth')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'job_title' => $user->job_title,
                        'gender' => $user->gender,
                        'country' => $user->country,
                        'bio' => $user->bio,
                        'social_links' => $user->social_links,
                        'avatar' => $user->profile_photo_url,
                        'status' => $user->status,
                        'last_activity' => $user->last_activity ? $user->last_activity->format('Y-m-d H:i:s') : null
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء تسجيل الدخول عبر Google'
            ], 500);
        }
    }
}
