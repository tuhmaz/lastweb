<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Country;

class SaudiSchoolClassesSeeder extends Seeder

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

        $saudiArabia = Country::where('code', 'SA')->first()->id;

        // الفصول الدراسية للسعودية
        $saudiGrades = [
            ['grade_name' => 'رياض الأطفال', 'grade_level' => 1, 'country_id' => $saudiArabia, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الأول الابتدائي', 'grade_level' => 2, 'country_id' => $saudiArabia, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الثاني الابتدائي', 'grade_level' => 3, 'country_id' => $saudiArabia, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الثالث الابتدائي', 'grade_level' => 4, 'country_id' => $saudiArabia, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الرابع الابتدائي', 'grade_level' => 5, 'country_id' => $saudiArabia, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الخامس الابتدائي', 'grade_level' => 6, 'country_id' => $saudiArabia, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف السادس الابتدائي', 'grade_level' => 7, 'country_id' => $saudiArabia, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الأول المتوسط', 'grade_level' => 8, 'country_id' => $saudiArabia, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الثاني المتوسط', 'grade_level' => 9, 'country_id' => $saudiArabia, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الثالث المتوسط', 'grade_level' => 10, 'country_id' => $saudiArabia, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الأول الثانوي', 'grade_level' => 11, 'country_id' => $saudiArabia, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الثاني الثانوي', 'grade_level' => 12, 'country_id' => $saudiArabia, 'created_at' => $now, 'updated_at' => $now],
            ['grade_name' => 'الصف الثالث الثانوي', 'grade_level' => 13, 'country_id' => $saudiArabia, 'created_at' => $now, 'updated_at' => $now],
        ];

        // إدخال جميع الفصول الدراسية
        DB::table('school_classes')->insert(array_merge($saudiGrades));
    }
}
