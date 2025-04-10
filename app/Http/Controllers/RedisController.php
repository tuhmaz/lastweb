<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Models\RedisLog;
use Illuminate\Pagination\LengthAwarePaginator;

class RedisController extends Controller
{
    // عرض السجلات
    public function index(Request $request)
    {
        $search = $request->input('search');
        $keys = $search ? Redis::keys("*$search*") : Redis::keys('*');

        $logs = [];
        foreach ($keys as $key) {
            $logs[] = [
                'key' => $key,
                'value' => Redis::get($key),
                'ttl' => Redis::ttl($key),
                'user' => auth()->user()->name ?? 'System',
                'action' => 'Viewed',
                'created_at' => now(),
            ];
        }

        // Pagination
        $perPage = 10; // Items per page
        $currentPage = $request->input('page', 1);
        $totalLogs = count($logs);
        $logs = array_slice($logs, ($currentPage - 1) * $perPage, $perPage);

        $logs = new \Illuminate\Pagination\LengthAwarePaginator(
            $logs,
            $totalLogs,
            $perPage,
            $currentPage,
            ['path' => $request->url()]
        );

        $redisInfo = app('redis')->info();

        return view('content.dashboard.redis.index', compact('logs', 'search', 'redisInfo'));
    }

    // فحص اتصال Redis
    public function testRedisConnection()
    {
        try {
            Redis::ping();
            return response()->json(['status' => 'success', 'message' => 'Redis is connected and working.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to connect to Redis: ' . $e->getMessage()], 500);
        }
    }

    public function cleanKeys()
    {
        $keys = Redis::keys('*');

        foreach ($keys as $key) {
            if (Redis::ttl($key) < 0) { // إذا انتهت صلاحية المفتاح
                Redis::del($key);
            }
        }

        return redirect()->route('dashboard.redis.index')->with('success', __('Expired keys cleaned successfully.'));
    }

    // تسجيل العمليات في قاعدة البيانات
    public function logAction($key, $value, $ttl, $action)
    {
        RedisLog::create([
            'key' => $key,
            'value' => $value,
            'ttl' => $ttl,
            'action' => $action,
            'user' => auth()->user()->name ?? 'System', // تسجيل المستخدم إذا كان موجودًا
        ]);
    }

    // إضافة مفتاح إلى Redis
    public function addKey(Request $request)
    {
        $key = $request->input('key');
        $value = $request->input('value');
        $ttl = $request->input('ttl');

        // إضافة المفتاح إلى Redis
        Redis::set($key, $value);
        if ($ttl) {
            Redis::expire($key, $ttl);
        }

        // تسجيل العملية
        $this->logAction($key, $value, $ttl, 'add');

        return redirect()->back()->with('success', 'Key added successfully.');
    }

    // حذف مفتاح
    public function deleteKey($key)
    {
        $value = Redis::get($key);
        $ttl = Redis::ttl($key);

        Redis::del($key);

        // تسجيل العملية
        $this->logAction($key, $value, $ttl, 'delete');

        return redirect()->back()->with('success', "Key '{$key}' deleted successfully.");
    }

    public function showEnvSettings()
    {
        // قراءة محتويات ملف .env
        $envData = [];
        $path = base_path('.env');
        if (File::exists($path)) {
            $lines = File::lines($path);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false) {
                    [$key, $value] = explode('=', $line, 2);
                    $envData[trim($key)] = trim($value);
                }
            }
        }

        return view('content.dashboard.redis.env-settings', compact('envData'));
    }

    public function updateEnvSettings(Request $request)
    {
        $path = base_path('.env');

        // تحديث القيم المطلوبة
        $envUpdates = $request->only(['REDIS_HOST', 'REDIS_PORT', 'REDIS_PASSWORD', 'REDIS_DB']);
        $envContent = File::get($path);

        foreach ($envUpdates as $key => $value) {
            $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
        }

        // حفظ الملف بعد التحديث
        File::put($path, $envContent);

        // تنظيف ذاكرة التخزين المؤقت
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        return redirect()->back()->with('success', 'Environment settings updated successfully.');
    }
}