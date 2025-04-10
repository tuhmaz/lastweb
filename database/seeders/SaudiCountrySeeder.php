<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaudiCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('countries')->insert([
            'id' => 2, // تعيين ID = 2 يدويًا
            'name' => 'Saudi Arabia',
            'code' => 'SA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
