<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ReactionController extends Controller
{
    /**
     * عرض قائمة التفاعلات
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // الحصول على قاعدة البيانات من الطلب أو استخدام القيمة الافتراضية
            $database = $request->header('X-Database', 'jo');
            
            // الحصول على معلمات التصفية
            $perPage = $request->input('per_page', 15);
            $commentId = $request->input('comment_id');
            $type = $request->input('type');
            $userId = $request->input('user_id');
            
            // بناء الاستعلام
            $query = Reaction::with(['user:id,name,email,profile_photo_path']);
            
            // تطبيق عوامل التصفية إذا تم توفيرها
            if ($commentId) {
                $query->where('comment_id', $commentId);
            }
            
            if ($type) {
                $query->where('type', $type);
            }
            
            if ($userId) {
                $query->where('user_id', $userId);
            }
            
            // الحصول على النتائج مع التقسيم إلى صفحات
            $reactions = $query->paginate($perPage);
            
            return response()->json([
                'status' => true,
                'message' => 'تم الحصول على التفاعلات بنجاح',
                'data' => $reactions
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في الحصول على التفاعلات: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء الحصول على التفاعلات'
            ], 500);
        }
    }

    /**
     * إضافة أو تحديث تفاعل
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'comment_id' => 'required|exists:comments,id',
                'type' => 'required|string|in:like,love,haha,wow,sad,angry',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'errors' => $validator->errors()
                ], 422);
            }

            // الحصول على قاعدة البيانات من الطلب أو استخدام القيمة الافتراضية
            $database = $request->header('X-Database', 'jo');

            // التحقق من وجود المستخدم
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'يجب تسجيل الدخول لإضافة تفاعل'
                ], 401);
            }

            // تحقق مما إذا كان المستخدم قد أضاف تفاعلًا بالفعل على هذا التعليق
            $existingReaction = Reaction::where('user_id', $user->id)
                                        ->where('comment_id', $request->comment_id)
                                        ->first();

            if ($existingReaction) {
                // إذا كان نوع التفاعل نفسه، نقوم بحذفه
                if ($existingReaction->type === $request->type) {
                    $existingReaction->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'تم إزالة التفاعل',
                        'data' => null
                    ]);
                }
                
                // إذا كان التفاعل موجودًا بالفعل، حدث نوعه
                $existingReaction->update([
                    'type' => $request->type,
                    'database' => $database
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'تم تحديث التفاعل بنجاح',
                    'data' => $existingReaction->load('user')
                ]);
            } else {
                // إذا لم يكن التفاعل موجودًا، قم بإنشائه
                $reaction = Reaction::create([
                    'user_id' => $user->id,
                    'comment_id' => $request->comment_id,
                    'type' => $request->type,
                    'database' => $database
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'تم إضافة التفاعل بنجاح',
                    'data' => $reaction->load('user')
                ], 201);
            }
        } catch (\Exception $e) {
            Log::error('خطأ في إضافة تفاعل: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء إضافة التفاعل'
            ], 500);
        }
    }

    /**
     * عرض تفاعل محدد
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // البحث عن التفاعل
            $reaction = Reaction::with(['user:id,name,email,profile_photo_path'])->find($id);
            
            if (!$reaction) {
                return response()->json([
                    'status' => false,
                    'message' => 'التفاعل غير موجود'
                ], 404);
            }
            
            return response()->json([
                'status' => true,
                'message' => 'تم الحصول على التفاعل بنجاح',
                'data' => $reaction
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في الحصول على التفاعل: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء الحصول على التفاعل'
            ], 500);
        }
    }

    /**
     * حذف تفاعل
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // البحث عن التفاعل
            $reaction = Reaction::find($id);
            
            if (!$reaction) {
                return response()->json([
                    'status' => false,
                    'message' => 'التفاعل غير موجود'
                ], 404);
            }
            
            // التحقق من أن المستخدم هو صاحب التفاعل
            if ($reaction->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
                return response()->json([
                    'status' => false,
                    'message' => 'غير مصرح لك بحذف هذا التفاعل'
                ], 403);
            }
            
            // حذف التفاعل
            $reaction->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'تم حذف التفاعل بنجاح'
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في حذف تفاعل: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء حذف التفاعل'
            ], 500);
        }
    }

    /**
     * الحصول على تفاعلات تعليق معين
     *
     * @param  int  $commentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReactionsByComment($commentId)
    {
        try {
            // التحقق من وجود التعليق
            $comment = Comment::find($commentId);
            if (!$comment) {
                return response()->json([
                    'status' => false,
                    'message' => 'التعليق غير موجود'
                ], 404);
            }

            // الحصول على جميع التفاعلات للتعليق مع بيانات المستخدمين
            $reactions = Reaction::where('comment_id', $commentId)
                                ->with(['user:id,name,email,profile_photo_path'])
                                ->get();

            // تجميع التفاعلات حسب النوع
            $reactionsByType = $reactions->groupBy('type');
            $reactionCounts = [];

            foreach ($reactionsByType as $type => $typeReactions) {
                $reactionCounts[$type] = [
                    'count' => $typeReactions->count(),
                    'users' => $typeReactions->pluck('user')
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'تم الحصول على التفاعلات بنجاح',
                'data' => [
                    'total_count' => $reactions->count(),
                    'reactions' => $reactionCounts
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في الحصول على تفاعلات التعليق: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء الحصول على تفاعلات التعليق'
            ], 500);
        }
    }

    /**
     * الحصول على تفاعل المستخدم الحالي على تعليق معين
     *
     * @param  int  $commentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserReaction($commentId)
    {
        try {
            // التحقق من وجود المستخدم
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'يجب تسجيل الدخول للحصول على تفاعل المستخدم'
                ], 401);
            }

            // التحقق من وجود التعليق
            $comment = Comment::find($commentId);
            if (!$comment) {
                return response()->json([
                    'status' => false,
                    'message' => 'التعليق غير موجود'
                ], 404);
            }

            // الحصول على تفاعل المستخدم على التعليق
            $reaction = Reaction::where('user_id', $user->id)
                               ->where('comment_id', $commentId)
                               ->first();

            if (!$reaction) {
                return response()->json([
                    'status' => true,
                    'message' => 'لا يوجد تفاعل للمستخدم على هذا التعليق',
                    'data' => null
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'تم الحصول على تفاعل المستخدم بنجاح',
                'data' => $reaction
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في الحصول على تفاعل المستخدم: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء الحصول على تفاعل المستخدم'
            ], 500);
        }
    }
}