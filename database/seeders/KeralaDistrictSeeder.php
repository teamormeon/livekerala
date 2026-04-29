<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class KeralaDistrictSeeder extends Seeder
{
    /**
     * Seed the application's database with Kerala districts.
     */
    public function run(): void
    {
        $districts = [
            ['name' => 'Thiruvananthapuram', 'iso2' => 'TV', 'iso3' => 'TVM'],
            ['name' => 'Kollam', 'iso2' => 'KL', 'iso3' => 'KLM'],
            ['name' => 'Pathanamthitta', 'iso2' => 'PT', 'iso3' => 'PTA'],
            ['name' => 'Alappuzha', 'iso2' => 'AL', 'iso3' => 'ALP'],
            ['name' => 'Kottayam', 'iso2' => 'KT', 'iso3' => 'KTM'],
            ['name' => 'Idukki', 'iso2' => 'ID', 'iso3' => 'IDK'],
            ['name' => 'Ernakulam', 'iso2' => 'EK', 'iso3' => 'EKM'],
            ['name' => 'Thrissur', 'iso2' => 'TS', 'iso3' => 'TSR'],
            ['name' => 'Palakkad', 'iso2' => 'PK', 'iso3' => 'PKD'],
            ['name' => 'Malappuram', 'iso2' => 'MP', 'iso3' => 'MLP'],
            ['name' => 'Kozhikode', 'iso2' => 'KZ', 'iso3' => 'KOZ'],
            ['name' => 'Wayanad', 'iso2' => 'WY', 'iso3' => 'WYD'],
            ['name' => 'Kannur', 'iso2' => 'KN', 'iso3' => 'KNR'],
            ['name' => 'Kasaragod', 'iso2' => 'KS', 'iso3' => 'KSD'],
        ];

        foreach ($districts as $district) {
            Country::updateOrCreate(
                ['iso3' => $district['iso3']],
                [
                    'name' => $district['name'],
                    'iso2' => $district['iso2'],
                    'status' => 1,
                    'phone_code' => 91,
                    'region' => 'Kerala',
                    'subregion' => 'District',
                ]
            );
        }
    }
}
