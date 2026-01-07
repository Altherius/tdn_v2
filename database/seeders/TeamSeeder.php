<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            ['name' => 'Real Madrid', 'region' => 'Europe'],
            ['name' => 'FC Barcelona', 'region' => 'Europe'],
            ['name' => 'Manchester United', 'region' => 'Europe'],
            ['name' => 'Bayern Munich', 'region' => 'Europe'],
            ['name' => 'Al-Hilal', 'region' => 'Asie'],
            ['name' => 'Urawa Red Diamonds', 'region' => 'Asie'],
            ['name' => 'Al Ahly', 'region' => 'Afrique'],
            ['name' => 'Wydad AC', 'region' => 'Afrique'],
            ['name' => 'Club América', 'region' => 'Amérique du Nord'],
            ['name' => 'Monterrey', 'region' => 'Amérique du Nord'],
            ['name' => 'Flamengo', 'region' => 'Amérique du Sud'],
            ['name' => 'River Plate', 'region' => 'Amérique du Sud'],
        ];

        foreach ($teams as $teamData) {
            $region = Region::where('name', $teamData['region'])->first();

            Team::create([
                'name' => $teamData['name'],
                'region_id' => $region->id,
            ]);
        }
    }
}
