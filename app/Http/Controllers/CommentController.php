<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\News;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function store(Request $request, $database)
    {
        $request->validate([
            'body' => 'required',
            'commentable_id' => 'required|integer',
            'commentable_type' => 'required|string|in:App\Models\News,App\Models\Article',
        ]);

        try {
            DB::connection($database)->beginTransaction();
            
            // جلب المحتوى (الخبر أو المقال) من قاعدة البيانات الصحيحة
            $contentModel = app($request->commentable_type)::on($database)
                ->find($request->commentable_id);

            if (!$contentModel) {
                return redirect()->back()->with('error', 'المحتوى غير موجود');
            }

            // إنشاء التعليق مع تحديد العلاقات
            $comment = new Comment();
            $comment->setConnection($database);
            $comment->fill([
                'body' => $request->body,
                'user_id' => auth()->id(),
                'commentable_id' => $request->commentable_id,
                'commentable_type' => $request->commentable_type,
                'database' => $database,
            ]);
            
            Log::info('Saving comment', [
                'database' => $database,
                'comment_data' => $comment->toArray()
            ]);

            $comment->save();
            
            DB::connection($database)->commit();

            return redirect()->back()->with('success', 'تم إضافة التعليق بنجاح!');
            
        } catch (\Exception $e) {
            DB::connection($database)->rollBack();
            Log::error('Error saving comment: ' . $e->getMessage(), [
                'database' => $database,
                'error' => $e,
                'request_data' => $request->all()
            ]);
            return redirect()->back()->with('error', 'حدث خطأ أثناء حفظ التعليق');
        }
    }

    public function destroy(Request $request, $database, $id)
    {
        try {
            DB::connection($database)->beginTransaction();
            
            Log::info('Attempting to delete comment', [
                'comment_id' => $id,
                'database' => $database,
                'user_id' => auth()->id()
            ]);

            // تمكين تسجيل الاستعلامات للتصحيح
            DB::connection($database)->enableQueryLog();
            
            // البحث عن التعليق في قاعدة البيانات الصحيحة
            $comment = Comment::on($database)
                ->where('database', $database)
                ->findOrFail($id);

            Log::info('Found comment', [
                'comment' => $comment->toArray()
            ]);

            // التحقق من أن المستخدم هو صاحب التعليق
            if (auth()->id() !== $comment->user_id) {
                return redirect()->back()->with('error', 'غير مصرح لك بحذف هذا التعليق');
            }

            // حذف ردود الأفعال المرتبطة بالتعليق أولاً
            if ($comment->reactions()->count() > 0) {
                Log::info('Deleting reactions', [
                    'reactions_count' => $comment->reactions()->count()
                ]);
                $comment->reactions()->delete();
            }
            
            // حذف التعليق
            $deleted = $comment->delete();
            
            DB::connection($database)->commit();
            
            Log::info('Comment deletion result', [
                'deleted' => $deleted,
                'queries' => DB::connection($database)->getQueryLog()
            ]);

            if (!$deleted) {
                throw new \Exception('Failed to delete comment');
            }

            return redirect()->back()->with('success', 'تم حذف التعليق بنجاح');

        } catch (\Exception $e) {
            DB::connection($database)->rollBack();
            
            Log::error('Comment deletion error: ' . $e->getMessage(), [
                'comment_id' => $id,
                'database' => $database,
                'exception' => $e,
                'queries' => DB::connection($database)->getQueryLog() ?? [],
                'request_data' => $request->all()
            ]);
            return redirect()->back()->with('error', 'فشل في حذف التعليق');
        }
    }
}
