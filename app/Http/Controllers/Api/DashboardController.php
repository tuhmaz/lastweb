<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\News;
use App\Models\Subject;
use App\Models\User;
use App\Models\Comment;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function statistics()
    {
        try {
            $totalUsers = User::count();
            $totalArticles = Article::count();
            $totalNews = News::count();
            $totalComments = Comment::count();
            $totalSubjects = Subject::count();
            $totalClasses = SchoolClass::count();

            // إحصائيات المقالات حسب الشهر
            $articlesPerMonth = Article::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

            // إحصائيات الأخبار حسب الشهر
            $newsPerMonth = News::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

            // آخر 5 مستخدمين
            $latestUsers = User::latest()
                ->take(5)
                ->get(['id', 'name', 'email', 'created_at', 'avatar']);

            // آخر 5 تعليقات
            $latestComments = Comment::with(['user:id,name,avatar'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'body' => $comment->body,
                        'user' => [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name,
                            'avatar' => $comment->user->avatar
                        ],
                        'created_at' => $comment->created_at->format('Y-m-d H:i:s')
                    ];
                });

            // تحضير مصفوفة الأشهر
            $months = range(1, 12);
            $chartData = [
                'articles' => array_map(function($month) use ($articlesPerMonth) {
                    return $articlesPerMonth[$month] ?? 0;
                }, $months),
                'news' => array_map(function($month) use ($newsPerMonth) {
                    return $newsPerMonth[$month] ?? 0;
                }, $months)
            ];

            return response()->json([
                'status' => true,
                'message' => null,
                'data' => [
                    'counts' => [
                        'users' => $totalUsers,
                        'articles' => $totalArticles,
                        'news' => $totalNews,
                        'comments' => $totalComments,
                        'subjects' => $totalSubjects,
                        'classes' => $totalClasses
                    ],
                    'chart_data' => $chartData,
                    'latest_users' => $latestUsers->map(function($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'avatar' => $user->avatar,
                            'created_at' => $user->created_at->format('Y-m-d H:i:s')
                        ];
                    }),
                    'latest_comments' => $latestComments
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء جلب إحصائيات لوحة التحكم',
                'data' => null
            ], 500);
        }
    }

    public function activityLogs()
    {
        try {
            $activities = DB::table('activity_log')
                ->join('users', 'activity_log.causer_id', '=', 'users.id')
                ->select(
                    'activity_log.id',
                    'activity_log.log_name',
                    'activity_log.description',
                    'activity_log.created_at',
                    'users.name as user_name',
                    'users.avatar'
                )
                ->orderBy('activity_log.created_at', 'desc')
                ->paginate(15);

            return response()->json([
                'status' => true,
                'message' => null,
                'data' => [
                    'activities' => $activities->map(function($activity) {
                        return [
                            'id' => $activity->id,
                            'log_name' => $activity->log_name,
                            'description' => $activity->description,
                            'user' => [
                                'name' => $activity->user_name,
                                'avatar' => $activity->avatar
                            ],
                            'created_at' => date('Y-m-d H:i:s', strtotime($activity->created_at))
                        ];
                    }),
                    'pagination' => [
                        'current_page' => $activities->currentPage(),
                        'last_page' => $activities->lastPage(),
                        'per_page' => $activities->perPage(),
                        'total' => $activities->total()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء جلب سجلات النشاط',
                'data' => null
            ], 500);
        }
    }
}
