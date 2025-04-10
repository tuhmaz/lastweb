<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        // جلب الإشعارات مع التصفية والتقسيم
        $notifications = auth()->user()->notifications()->orderBy('created_at', 'desc')->paginate(10);
        
        // تنسيق البيانات
        $formattedNotifications = $notifications->map(function($notification) {
            return [
                'id' => (string)$notification->id,
                'type' => $notification->type,
                'notifiable_type' => $notification->notifiable_type,
                'notifiable_id' => (int)$notification->notifiable_id,
                'data' => $notification->data,
                'read_at' => $notification->read_at ? $notification->read_at->format('Y-m-d H:i:s') : null,
                'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $notification->updated_at->format('Y-m-d H:i:s')
            ];
        });

        return response()->json([
            'status' => true,
            'message' => null,
            'data' => $formattedNotifications,
            'current_page' => (int)$notifications->currentPage(),
            'last_page' => (int)$notifications->lastPage(),
            'per_page' => (int)$notifications->perPage(),
            'total' => (int)$notifications->total()
        ]);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
            return response()->json([
                'status' => true,
                'message' => 'تم تحديث حالة الإشعار بنجاح',
                'data' => [
                    'id' => (string)$notification->id,
                    'type' => $notification->type,
                    'notifiable_type' => $notification->notifiable_type,
                    'notifiable_id' => (int)$notification->notifiable_id,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at->format('Y-m-d H:i:s'),
                    'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $notification->updated_at->format('Y-m-d H:i:s')
                ]
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'الإشعار غير موجود',
            'data' => null
        ], 404);
    }

    public function markAllAsRead()
    {
        $notifications = auth()->user()->unreadNotifications;
        $notifications->markAsRead();

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث حالة جميع الإشعارات بنجاح',
            'data' => null
        ]);
    }

    public function deleteSelected(Request $request)
    {
        $request->validate([
            'selected_notifications' => 'required|array',
        ]);

        // الحصول على إشعارات المستخدم الحالي
        $user = auth()->user();
        $user->notifications()->whereIn('id', $request->selected_notifications)->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف الإشعارات المحددة بنجاح',
            'data' => null
        ]);
    }

    public function handleActions(Request $request)
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'action' => 'required|string|in:delete,mark_as_read',
        ]);

        $user = auth()->user();
        $action = $request->input('action');

        if ($action == 'delete') {
            $user->notifications()->whereIn('id', $request->notification_ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'تم حذف الإشعارات المحددة بنجاح',
                'data' => null
            ]);
        }

        if ($action == 'mark_as_read') {
            $user->notifications()->whereIn('id', $request->notification_ids)->update(['read_at' => now()]);
            return response()->json([
                'status' => true,
                'message' => 'تم تحديث حالة الإشعارات المحددة بنجاح',
                'data' => null
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'إجراء غير صالح',
            'data' => null
        ], 400);
    }

    public function delete($id)
    {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->delete();
            return response()->json([
                'status' => true,
                'message' => 'تم حذف الإشعار بنجاح',
                'data' => null
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'الإشعار غير موجود',
            'data' => null
        ], 404);
    }
}