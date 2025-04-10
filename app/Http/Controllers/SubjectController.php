<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Country;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    /**
     * الحصول على قائمة الدول المتاحة
     */
    private function getAvailableCountries(): array
    {
        return [
            ['id' => 1, 'name' => 'الأردن', 'code' => 'jo'],
            ['id' => 2, 'name' => 'السعودية', 'code' => 'sa'],
            ['id' => 3, 'name' => 'مصر', 'code' => 'eg'],
            ['id' => 4, 'name' => 'فلسطين', 'code' => 'ps'],
        ];
    }

    /**
     * تحديد اتصال قاعدة البيانات بناءً على معرف الدولة
     */
    private function getConnection(?string $countryId = null): string
    {
        if (!$countryId) {
            return 'jo'; // الأردن كقيمة افتراضية
        }

        return match ($countryId) {
            '2' => 'sa',  // السعودية
            '3' => 'eg',  // مصر
            '4' => 'ps',  // فلسطين
            default => 'jo', // الأردن
        };
    }

    /**
     * عرض قائمة المواد الدراسية
     */
    public function index(Request $request)
    {
        // الحصول على قائمة الدول
        $countries = collect($this->getAvailableCountries());
        $selectedCountry = $request->input('country_id', '1'); // جعل الأردن هو الاختيار الافتراضي
        
        // تحديد قاعدة البيانات
        $connection = $this->getConnection($selectedCountry);
        
        try {
            // جلب الصفوف للدولة المحددة
            $classes = SchoolClass::on($connection)
                ->where('country_id', $selectedCountry)
                ->orderBy('grade_level')
                ->get()
                ->keyBy('grade_level');

            // استرجاع المواد من قاعدة البيانات المحددة
            $subjects = Subject::on($connection)
                ->orderBy('grade_level')
                ->orderBy('subject_name')
                ->get();

            // تجميع المواد حسب الصف
            $groupedSubjects = $subjects->groupBy('grade_level')
                ->map(function ($subjects, $gradeLevel) use ($classes) {
                    $class = $classes->get($gradeLevel);
                    if (!$class) {
                        return null; // تجاهل المواد التي ليس لها صف مقابل
                    }
                    return [
                        'grade_level' => $gradeLevel,
                        'grade_name' => $class->grade_name,
                        'subjects' => $subjects
                    ];
                })
                ->filter() // إزالة القيم الفارغة
                ->sortBy('grade_level')
                ->values();

            if ($request->ajax()) {
                return response()->json([
                    'data' => $subjects->map(function($subject, $index) use ($selectedCountry, $countries, $classes) {
                        $country = $countries->firstWhere('id', (int) $selectedCountry);
                        $class = $classes->get($subject->grade_level);
                        return [
                            'DT_RowIndex' => $index + 1,
                            'id' => $subject->id,
                            'subject_name' => $subject->subject_name,
                            'grade_name' => $class ? $class->grade_name : '',
                            'country_name' => $country ? $country['name'] : __('N/A'),
                            'created_at' => $subject->created_at->format('Y-m-d'),
                            'show_url' => route('dashboard.subjects.show', ['subject' => $subject->id, 'country_id' => $selectedCountry]),
                            'edit_url' => route('dashboard.subjects.edit', ['subject' => $subject->id, 'country_id' => $selectedCountry]),
                            'delete_url' => route('dashboard.subjects.destroy', ['subject' => $subject->id, 'country_id' => $selectedCountry])
                        ];
                    })
                ]);
            }

            // إعداد البيانات للعرض
            $viewData = [
                'groupedSubjects' => $groupedSubjects,
                'countries' => $countries,
                'selectedCountry' => $selectedCountry,
                'totalSubjects' => $subjects->count(),
                'hasError' => false,
                'errorMessage' => null
            ];

        } catch (\Exception $e) {
            // في حالة حدوث خطأ في الاتصال بقاعدة البيانات
            $viewData = [
                'groupedSubjects' => collect(),
                'countries' => $countries,
                'selectedCountry' => $selectedCountry,
                'totalSubjects' => 0,
                'hasError' => true,
                'errorMessage' => $selectedCountry == '1' ? 
                    null : // لا نظهر رسالة خطأ للأردن
                    __('Database connection not available for the selected country.')
            ];
        }

        return view('content.dashboard.subjects.index', $viewData);
    }

    /**
     * عرض نموذج إنشاء مادة دراسية جديدة
     */
    public function create()
    {
        $countries = collect($this->getAvailableCountries());
        $selectedCountry = request('country_id', '1'); // الأردن كاختيار افتراضي
        $connection = $this->getConnection($selectedCountry);

        // جلب الصفوف للدولة المحددة
        $classes = SchoolClass::on($connection)
            ->where('country_id', $selectedCountry)
            ->orderBy('grade_level')
            ->get();

        return view('content.dashboard.subjects.create', compact('countries', 'selectedCountry', 'classes'));
    }

    /**
     * حفظ مادة دراسية جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject_name' => 'required|string|max:255',
            'grade_level' => 'required|integer|min:1|max:12',
            'country_id' => 'required|string'
        ]);

        $connection = $this->getConnection($request->country_id);
        
        $subject = new Subject();
        $subject->setConnection($connection);
        $subject->subject_name = $request->subject_name;
        $subject->grade_level = $request->grade_level;
        $subject->save();

        return redirect()
            ->route('dashboard.subjects.index', ['country_id' => $request->country_id])
            ->with('success', __('Subject created successfully'));
    }

    /**
     * عرض تفاصيل مادة دراسية
     */
    public function show(Subject $subject)
    {
        $selectedCountry = request('country_id', '1');
        $connection = $this->getConnection($selectedCountry);
        $subject->setConnection($connection);

        return view('content.dashboard.subjects.show', compact('subject', 'selectedCountry'));
    }

    /**
     * عرض نموذج تعديل مادة دراسية
     */
    public function edit(Subject $subject)
    {
        $selectedCountry = request('country_id', '1');
        $connection = $this->getConnection($selectedCountry);
        $subject->setConnection($connection);
        
        $countries = collect($this->getAvailableCountries());
        
        // جلب الصفوف للدولة المحددة
        $classes = SchoolClass::on($connection)
            ->where('country_id', $selectedCountry)
            ->orderBy('grade_level')
            ->get();

        return view('content.dashboard.subjects.edit', compact('subject', 'countries', 'selectedCountry', 'classes'));
    }

    /**
     * تحديث مادة دراسية
     */
    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'subject_name' => 'required|string|max:255',
            'grade_level' => 'required|integer|min:1|max:12',
            'country_id' => 'required|string'
        ]);

        $connection = $this->getConnection($request->country_id);
        $subject->setConnection($connection);
        
        $subject->subject_name = $request->subject_name;
        $subject->grade_level = $request->grade_level;
        $subject->save();

        return redirect()
            ->route('dashboard.subjects.index', ['country_id' => $request->country_id])
            ->with('success', __('Subject updated successfully'));
    }

    /**
     * حذف مادة دراسية
     */
    public function destroy(Subject $subject)
    {
        $selectedCountry = request('country_id', '1');
        $connection = $this->getConnection($selectedCountry);
        $subject->setConnection($connection);
        
        $subject->delete();

        if (request()->ajax()) {
            return response()->json(['message' => __('Subject deleted successfully')]);
        }

        return redirect()
            ->route('dashboard.subjects.index', ['country_id' => $selectedCountry])
            ->with('success', __('Subject deleted successfully'));
    }
}
