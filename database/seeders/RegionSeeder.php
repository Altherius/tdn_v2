<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            'Europe',
            'Asie',
            'Afrique',
            'Amérique du Nord',
            'Amérique du Sud',
            'Océanie',
        ];

        foreach ($regions as $name) {
            Region::create(['name' => $name]);
        }
    }
}
