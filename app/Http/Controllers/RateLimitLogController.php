<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RateLimitLog;
use Illuminate\Support\Facades\Auth;

class RateLimitLogController extends Controller
{
    /**
     * عرض قائمة سجلات تقييد معدل الطلبات
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // التحقق من صلاحيات المستخدم
        if (!Auth::user()->can('manage security')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        // الحصول على المعلمات من الطلب
        $filter = $request->input('filter', 'all');
        $search = $request->input('search');
        $perPage = $request->input('per_page', 15);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // بناء الاستعلام
        $query = RateLimitLog::query();

        // تطبيق الفلتر
        if ($filter === 'blocked') {
            $query->where('blocked_until', '>', now());
        } elseif ($filter === 'expired') {
            $query->where('blocked_until', '<', now());
        }

        // تطبيق البحث
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhere('route', 'like', "%{$search}%")
                  ->orWhere('user_agent', 'like', "%{$search}%");
            });
        }

        // تطبيق الترتيب
        $query->orderBy($sortBy, $sortOrder);

        // الحصول على النتائج مع علاقة المستخدم
        $logs = $query->with('user')->paginate($perPage);

        return view('content.dashboard.security.rate-limit-logs', [
            'logs' => $logs,
            'filter' => $filter,
            'search' => $search,
            'perPage' => $perPage,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    /**
     * حذف سجل تقييد معدل الطلبات
     *
     * @param  \App\Models\RateLimitLog  $log
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(RateLimitLog $log)
    {
        // التحقق من صلاحيات المستخدم
        if (!Auth::user()->can('manage security')) {
            abort(403, 'غير مصرح لك بحذف هذا السجل');
        }

        $log->delete();

        return redirect()->route('dashboard.security.rate-limit-logs.index')
            ->with('success', 'تم حذف السجل بنجاح');
    }

    /**
     * حذف جميع سجلات تقييد معدل الطلبات
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyAll(Request $request)
    {
        // التحقق من صلاحيات المستخدم
        if (!Auth::user()->can('manage security')) {
            abort(403, 'غير مصرح لك بحذف السجلات');
        }

        // الحصول على المعلمات من الطلب
        $filter = $request->input('filter', 'all');

        // بناء الاستعلام
        $query = RateLimitLog::query();

        // تطبيق الفلتر
        if ($filter === 'blocked') {
            $query->where('blocked_until', '>', now());
        } elseif ($filter === 'expired') {
            $query->where('blocked_until', '<', now());
        }

        // حذف السجلات
        $count = $query->count();
        $query->delete();

        return redirect()->route('dashboard.security.rate-limit-logs.index')
            ->with('success', "تم حذف {$count} سجل بنجاح");
    }

    /**
     * حظر عنوان IP
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function blockIp(Request $request)
    {
        // التحقق من صلاحيات المستخدم
        if (!Auth::user()->can('manage security')) {
            abort(403, 'غير مصرح لك بحظر عناوين IP');
        }

        // التحقق من صحة البيانات
        $validated = $request->validate([
            'ip_address' => 'required|ip',
            'duration' => 'required|integer|min:1',
            'duration_unit' => 'required|in:minutes,hours,days',
        ]);

        // حساب وقت انتهاء الحظر
        $blockedUntil = now();
        switch ($validated['duration_unit']) {
            case 'minutes':
                $blockedUntil = $blockedUntil->addMinutes($validated['duration']);
                break;
            case 'hours':
                $blockedUntil = $blockedUntil->addHours($validated['duration']);
                break;
            case 'days':
                $blockedUntil = $blockedUntil->addDays($validated['duration']);
                break;
        }

        // إنشاء سجل حظر
        RateLimitLog::create([
            'ip_address' => $validated['ip_address'],
            'method' => 'MANUAL',
            'attempts' => 999,
            'limit' => 0,
            'blocked_until' => $blockedUntil,
            'route' => 'manual-block',
            'user_agent' => 'Blocked by admin: ' . Auth::user()->name,
        ]);

        return redirect()->route('dashboard.security.rate-limit-logs.index')
            ->with('success', "تم حظر عنوان IP {$validated['ip_address']} بنجاح حتى {$blockedUntil->format('Y-m-d H:i:s')}");
    }
}
