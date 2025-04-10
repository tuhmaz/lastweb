<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Country;

class PalestineSchoolClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        // الحصول على معرفات الدول

        $palestine = Country::where('code', 'PS')->first()->id;

        // الفصول الدراسية لفلسطين
        $palestineGrades = [
            ['grade_name' => 'الصف الأول', 'grade_level' => 1, 'country_id' => $palestine, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الثاني', 'grade_level' => 2, 'country_id' => $palestine, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الثالث', 'grade_level' => 3, 'country_id' => $palestine, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الرابع', 'grade_level' => 4, 'country_id' => $palestine, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الخامس', 'grade_level' => 5, 'country_id' => $palestine, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف السادس', 'grade_level' => 6, 'country_id' => $palestine, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف السابع', 'grade_level' => 7, 'country_id' => $palestine, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الثامن', 'grade_level' => 8, 'country_id' => $palestine, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف التاسع', 'grade_level' => 9, 'country_id' => $palestine, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف العاشر', 'grade_level' => 10, 'country_id' => $palestine, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الحادي عشر', 'grade_level' => 11, 'country_id' => $palestine, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الثاني عشر', 'grade_level' => 12, 'country_id' => $palestine, 'created_at' => $now, 'updated_at' => $now],
        ];

        // إدخال جميع الفصول الدراسية
        DB::table('school_classes')->insert(array_merge($palestineGrades));
    }
}
