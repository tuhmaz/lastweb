<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EgyptCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('countries')->insert([
            'id' => 3, // تعيين ID = 3 يدويًا
            'name' => 'Egypt',
            'code' => 'EG',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
