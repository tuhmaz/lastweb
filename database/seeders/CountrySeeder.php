<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use Carbon\Carbon;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        Country::insert([
            [
                'name' => 'Jordan',
                'code' => 'JO',
                'created_at' => $now,
                'updated_at' => $now
            ],
            
        ]);
    }
}
