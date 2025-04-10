<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SchoolClassRequest;

class SchoolClassController extends Controller
{
    /**
     * الحصول على قائمة الدول المتاحة
     */
    private function getAvailableCountries()
    {
        $countries = [
            (object)['id' => 1, 'name' => 'الأردن', 'code' => 'jo'],
            (object)['id' => 2, 'name' => 'السعودية', 'code' => 'sa'],
            (object)['id' => 3, 'name' => 'مصر', 'code' => 'eg'],
            (object)['id' => 4, 'name' => 'فلسطين', 'code' => 'ps'],
        ];
        
        return collect($countries);
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
     * عرض قائمة الصفوف المدرسية
     */
    public function index(Request $request)
    {
        // الحصول على قائمة الدول
        $countries = $this->getAvailableCountries();
        $selectedCountry = $request->input('country_id', '1'); // جعل الأردن هو الاختيار الافتراضي
        
        // تحديد قاعدة البيانات
        $connection = $this->getConnection($selectedCountry);
        
        // استرجاع الصفوف من قاعدة البيانات المحددة
        $classes = SchoolClass::on($connection)
            ->orderBy('grade_level')
            ->orderBy('grade_name')
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'data' => $classes->map(function($class, $index) use ($selectedCountry, $countries) {
                    $country = $countries->firstWhere('id', (int) $selectedCountry);
                    return [
                        'DT_RowIndex' => $index + 1,
                        'id' => $class->id,
                        'grade_name' => $class->grade_name,
                        'grade_level' => $class->grade_level,
                        'country_name' => $country ? $country->name : __('N/A'),
                        'subjects_count' => $class->subjects_count ?? 0,
                        'created_at' => $class->created_at ? $class->created_at->format('Y-m-d') : now()->format('Y-m-d'),
                        'show_url' => route('dashboard.school-classes.show', ['school_class' => $class->id, 'country_id' => $selectedCountry]),
                        'edit_url' => route('dashboard.school-classes.edit', ['school_class' => $class->id, 'country_id' => $selectedCountry]),
                        'delete_url' => route('dashboard.school-classes.destroy', ['school_class' => $class->id, 'country_id' => $selectedCountry])
                    ];
                })
            ]);
        }

        return view('content.dashboard.school-classes.index', compact('classes', 'countries', 'selectedCountry'));
    }

    /**
     * Show the form for creating a new school class.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $countries = $this->getAvailableCountries();
        return view('content.dashboard.school-classes.create', compact('countries'));
    }

    /**
     * Store a newly created school class in storage.
     *
     * @param  \App\Http\Requests\SchoolClassRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SchoolClassRequest $request)
    {
        try {
            DB::beginTransaction();

            // Get the correct database connection based on country
            $connection = $this->getConnection($request->country_id);
            
            // Create the school class with the correct connection
            $class = new SchoolClass();
            $class->setConnection($connection);
            $class->fill([
                'grade_name' => $request->grade_name,
                'grade_level' => $request->grade_level,
            ]);
            $class->save();

            DB::commit();

            return redirect()
                ->route('dashboard.school-classes.show', ['school_class' => $class->id, 'country_id' => $request->country_id])
                ->with('success', __('School class created successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', __('Error creating school class. Please try again.'));
        }
    }

    /**
     * Display the specified school class.
     *
     * @param  \App\Models\SchoolClass  $schoolClass
     * @return \Illuminate\View\View
     */
    public function show(SchoolClass $schoolClass)
    {
        $schoolClass->load(['country', 'subjects', 'semesters']);
        return view('content.dashboard.school-classes.show', [
            'class' => $schoolClass
        ]);
    }

    /**
     * Show the form for editing the specified school class.
     *
     * @param  \App\Models\SchoolClass  $schoolClass
     * @return \Illuminate\View\View
     */
    public function edit(SchoolClass $schoolClass)
    {
        $selectedCountry = request('country_id', '1');
        $connection = $this->getConnection($selectedCountry);
        $schoolClass->setConnection($connection);

        // تحويل مصفوفة الدول إلى كائنات
        $countries = collect($this->getAvailableCountries())->map(function($country) {
            return (object) $country;
        });

        return view('content.dashboard.school-classes.edit', [
            'class' => $schoolClass,
            'countries' => $countries,
            'selectedCountry' => $selectedCountry
        ]);
    }

    /**
     * Update the specified school class in storage.
     *
     * @param  \App\Http\Requests\SchoolClassRequest  $request
     * @param  \App\Models\SchoolClass  $schoolClass
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SchoolClassRequest $request, SchoolClass $schoolClass)
    {
        try {
            DB::beginTransaction();

            $schoolClass->update([
                'grade_name' => $request->grade_name,
                'grade_level' => $request->grade_level,
                'country_id' => $request->country_id,
            ]);

            DB::commit();

            return redirect()
                ->route('dashboard.school-classes.show', $schoolClass)
                ->with('success', __('School class updated successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', __('Error updating school class. Please try again.'));
        }
    }

    /**
     * Remove the specified school class from storage.
     *
     * @param  \App\Models\SchoolClass  $schoolClass
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(SchoolClass $schoolClass)
    {
        try {
            DB::beginTransaction();

            if ($schoolClass->subjects()->exists() || $schoolClass->semesters()->exists()) {
                return back()->with('error', __('Cannot delete class with associated subjects or semesters.'));
            }

            $schoolClass->delete();

            DB::commit();

            return redirect()
                ->route('dashboard.school-classes.index')
                ->with('success', __('School class deleted successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Error deleting school class. Please try again.'));
        }
    }
}
