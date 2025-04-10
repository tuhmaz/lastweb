<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Article;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Traits\ApiDatabaseTrait;

class GradeController extends Controller
{
    use ApiDatabaseTrait;

    public function index(Request $request, $country)
    {
        try {
            $this->switchDatabase($country);

            $grades = SchoolClass::with(['subjects' => function($query) {
                $query->select('id', 'subject_name', 'grade_level');
            }])
            ->select('id', 'grade_name', 'grade_level')
            ->get();

            // تحويل البيانات إلى التنسيق المتوقع
            $formattedGrades = $grades->map(function($grade) {
                return [
                    'id' => $grade->id,
                    'grade_name' => $grade->grade_name,
                    'grade_level' => $grade->grade_level,
                    'subjects' => $grade->subjects->map(function($subject) {
                        return [
                            'id' => $subject->id,
                            'subject_name' => $subject->subject_name,
                            'grade_level' => $subject->grade_level
                        ];
                    })
                ];
            });

            return response()->json([
                'status' => true,
                'database' => $country,
                'message' => null,
                'data' => [
                    'status' => true,
                    'grades' => $formattedGrades
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'database' => $country,
                'message' => 'Error fetching grades: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function show(Request $request, $country, $id)
    {
        try {
            $this->switchDatabase($country);

            $grade = SchoolClass::with(['subjects' => function($query) {
                $query->select('id', 'subject_name', 'grade_level');
            }])
            ->select('id', 'grade_name', 'grade_level')
            ->findOrFail($id);

            // تحويل البيانات إلى التنسيق المتوقع من قبل SubjectModel
            $subjects = $grade->subjects->map(function($subject) {
                return [
                    'id' => $subject->id,
                    'subject_name' => $subject->subject_name,
                    'grade_level' => $subject->grade_level
                ];
            });

            return response()->json([
                'status' => true,
                'database' => $country,
                'message' => null,
                'data' => [
                    'status' => true,
                    'subjects' => $subjects
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'database' => $country,
                'message' => 'Error fetching subjects: ' . $e->getMessage(),
                'data' => null
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }

    public function showSubject(Request $request, $country, $id)
    {
        try {
            $this->switchDatabase($country);

            $subject = Subject::with(['schoolClass:id,grade_name,grade_level'])
                ->select('id', 'subject_name', 'grade_level')
                ->findOrFail($id);

            $semesters = Semester::where('grade_level', $subject->grade_level)
                ->select('id', 'semester_name', 'grade_level')
                ->orderBy('semester_name')
                ->get();

            // تحويل البيانات مع معالجة القيم الفارغة
            $formattedSemesters = $semesters->map(function($semester) {
                return [
                    'id' => $semester->id ?? 0,
                    'semesterName' => $semester->semester_name ?? '',  // استخدام نص فارغ بدلاً من null
                    'gradeLevel' => $semester->grade_level ?? 0  // استخدام 0 بدلاً من null
                ];
            })->filter(function($semester) {
                // تصفية السجلات التي تحتوي على قيم فارغة في الحقول المهمة
                return !empty($semester['semesterName']);
            })->values();  // إعادة ترتيب المصفوفة

            $formattedSubject = [
                'id' => $subject->id ?? 0,
                'subject_name' => $subject->subject_name ?? '',
                'grade_level' => $subject->grade_level ?? 0,
                'school_class' => $subject->schoolClass ? [
                    'id' => $subject->schoolClass->id ?? 0,
                    'grade_name' => $subject->schoolClass->grade_name ?? '',
                    'grade_level' => $subject->schoolClass->grade_level ?? 0
                ] : null
            ];

            return response()->json([
                'status' => true,
                'database' => $country,
                'message' => null,
                'data' => [
                    'status' => true,
                    'subject' => $formattedSubject,
                    'semesters' => $formattedSemesters
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'database' => $country,
                'message' => 'Error fetching subject: ' . $e->getMessage(),
                'data' => null
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }

    public function subjectArticles(Request $request, $country, $subject, $semester, $category)
    {
        try {
            $this->switchDatabase($country);

            $subjectModel = Subject::findOrFail($subject);
            $semesterModel = Semester::findOrFail($semester);

            $articles = Article::with([
                'files' => function ($query) use ($category) {
                    $query->where('file_category', $category);
                },
                'subject:id,subject_name,grade_level',
                'semester:id,semester_name,grade_level'
            ])
            ->where('subject_id', $subject)
            ->where('semester_id', $semester)
            ->whereHas('files', function ($query) use ($category) {
                $query->where('file_category', $category);
            })
            ->select('id', 'title', 'content', 'subject_id', 'semester_id', 'visit_count', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

            return response()->json([
                'status' => true,
                'database' => $country,
                'message' => null,
                'data' => [
                    'status' => true,
                    'articles' => $articles->items(),
                    'pagination' => [
                        'current_page' => $articles->currentPage(),
                        'last_page' => $articles->lastPage(),
                        'per_page' => $articles->perPage(),
                        'total' => $articles->total()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'database' => $country,
                'message' => 'Error fetching articles: ' . $e->getMessage(),
                'data' => null
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }

    public function showArticle(Request $request, $country, $id)
    {
        $this->switchDatabase($country);

        $article = Article::with([
            'subject.schoolClass',
            'semester',
            'keywords',
            'files'
        ])->findOrFail($id);

        $article->increment('visit_count');

        // جلب المؤلف من قاعدة البيانات الرئيسية
        $author = User::on('jo')->find($article->author_id);
        $article->author = $author;

        // معالجة الكلمات المفتاحية
        $article->content = $this->processKeywords($article->content, $article->keywords, $country);

        return response()->json([
            'status' => true,
            'database' => $country,
            'message' => null,
            'data' => [
                'status' => true,
                'item' => $article->toArray()
            ]
        ]);
    }

    private function processKeywords($content, $keywords, $country)
    {
        $keywordsArray = $keywords->pluck('keyword')->toArray();
        
        foreach ($keywordsArray as $keyword) {
            $keyword = trim($keyword);
            $content = preg_replace(
                '/\b' . preg_quote($keyword, '/') . '\b/u',
                '[' . $keyword . ']', // استبدال الروابط بعلامات خاصة ليتم معالجتها في التطبيق
                $content
            );
        }

        return $content;
    }

    public function downloadFile(Request $request, $country, $id)
    {
        $this->switchDatabase($country);

        $file = File::findOrFail($id);
        $file->increment('download_count');

        $filePath = storage_path('app/public/' . $file->file_path);
        
        if (!file_exists($filePath)) {
            return response()->json([
                'status' => false,
                'database' => $country,
                'message' => 'File not found',
                'data' => null
            ], 404);
        }

        // إرجاع معلومات الملف
        return response()->json([
            'status' => true,
            'database' => $country,
            'message' => null,
            'data' => [
                'file' => $file->toArray(),
                'download_url' => url('storage/' . $file->file_path)
            ]
        ]);
    }
}
