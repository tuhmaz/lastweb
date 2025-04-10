<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PalestineCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('countries')->insert([
            'id' => 4,
            'name' => 'Palestine',
            'code' => 'PS',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
