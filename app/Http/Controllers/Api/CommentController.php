<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $database, $id)
    {
        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        try {
            // تحديد نوع المحتوى من المسار
            $routeName = $request->route()->getName();
            $modelType = str_contains($routeName, 'news') ? 'App\\Models\\News' : 'App\\Models\\Article';
            
            // إنشاء التعليق في قاعدة البيانات الرئيسية
            $comment = Comment::create([
                'body' => $validated['body'],
                'user_id' => auth()->id(),
                'commentable_id' => $id,
                'commentable_type' => $modelType,
                'database' => $database // نحتفظ بمعلومات قاعدة البيانات الأصلية
            ]);

            return response()->json([
                'message' => 'تم إضافة التعليق بنجاح!',
                'comment' => $comment,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'فشل في إضافة التعليق.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request, $database, $id)
    {
        try {
            // تحديد نوع المحتوى من المسار
            $routeName = $request->route()->getName();
            $modelType = str_contains($routeName, 'news') ? 'App\\Models\\News' : 'App\\Models\\Article';
            
            // جلب التعليقات المرتبطة بالمحتوى من قاعدة البيانات الرئيسية
            $comments = Comment::where([
                'commentable_type' => $modelType,
                'commentable_id' => $id,
                'database' => $database
            ])
            ->with('user')
            ->latest()
            ->get();

            return response()->json([
                'comments' => $comments,
                'total' => $comments->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'comments' => [],
                'total' => 0,
                'message' => 'لا توجد تعليقات.'
            ]);
        }
    }

    public function show($database, $id, Comment $comment)
    {
        try {
            // التحقق من أن التعليق من نفس قاعدة البيانات والمحتوى
            if ($comment->database !== $database || $comment->commentable_id != $id) {
                return response()->json([
                    'message' => 'التعليق غير موجود'
                ], 404);
            }

            return response()->json([
                'comment' => $comment->load('user')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'فشل في جلب التعليق',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($database, $id, Comment $comment)
    {
        try {
            // التحقق من أن التعليق من نفس قاعدة البيانات والمحتوى
            if ($comment->database !== $database || $comment->commentable_id != $id) {
                return response()->json([
                    'message' => 'التعليق غير موجود'
                ], 404);
            }

            // التحقق من أن المستخدم هو صاحب التعليق أو لديه صلاحية الحذف
            if (auth()->id() !== $comment->user_id && !auth()->user()->can('delete comments')) {
                return response()->json([
                    'message' => 'غير مصرح لك بحذف هذا التعليق'
                ], 403);
            }

            $comment->delete();

            return response()->json([
                'message' => 'تم حذف التعليق بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'فشل في حذف التعليق',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}