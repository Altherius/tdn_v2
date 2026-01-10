<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use League\ISO3166\ISO3166;
use Locale;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // UK Constituent Nations (not in ISO 3166-1, but have ISO 3166-2 subdivision codes)
        $ukConstituentNations = [
            ['name' => 'Angleterre', 'code' => 'gb-eng'],
            ['name' => 'Écosse', 'code' => 'gb-sct'],
            ['name' => 'Pays de Galles', 'code' => 'gb-wls'],
            ['name' => 'Irlande du Nord', 'code' => 'gb-nir'],
        ];

        foreach ($ukConstituentNations as $nation) {
            Country::create($nation);
        }

        // All ISO 3166-1 countries (except United Kingdom which is replaced by constituent nations)
        $iso = new ISO3166;

        foreach ($iso as $country) {
            // Skip United Kingdom as we use constituent nations instead
            if ($country['alpha2'] === 'GB') {
                continue;
            }

            $frenchName = Locale::getDisplayRegion('-'.$country['alpha2'], 'fr');

            // Fallback to English name if French translation is empty
            if (empty($frenchName)) {
                $frenchName = $country['name'];
            }

            Country::create([
                'name' => $frenchName,
                'code' => strtolower($country['alpha2']),
            ]);
        }
    }
}
