<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\CountryCities;
use App\Models\CountryStates;
use Illuminate\Database\Seeder;

class KeralaDistrictSeeder extends Seeder
{
    /**
     * Seed the application's database with Kerala districts and cities.
     */
    public function run(): void
    {
        $districts = [
            [
                'name' => 'Thiruvananthapuram',
                'iso3' => 'TVM',
                'cities' => [
                    ['name' => 'Thiruvananthapuram', 'latitude' => '8.5241', 'longitude' => '76.9366'],
                    ['name' => 'Neyyattinkara', 'latitude' => '8.3985', 'longitude' => '77.0854'],
                    ['name' => 'Varkala', 'latitude' => '8.7379', 'longitude' => '76.7163'],
                    ['name' => 'Attingal', 'latitude' => '8.6969', 'longitude' => '76.8151'],
                    ['name' => 'Kazhakoottam', 'latitude' => '8.5510', 'longitude' => '76.8751'],
                ],
            ],
            [
                'name' => 'Kollam',
                'iso3' => 'KLM',
                'cities' => [
                    ['name' => 'Kollam', 'latitude' => '8.8932', 'longitude' => '76.6141'],
                    ['name' => 'Karunagappally', 'latitude' => '9.0544', 'longitude' => '76.5353'],
                    ['name' => 'Paravur', 'latitude' => '8.8123', 'longitude' => '76.6685'],
                    ['name' => 'Punalur', 'latitude' => '9.0196', 'longitude' => '76.9226'],
                    ['name' => 'Kottarakkara', 'latitude' => '9.0014', 'longitude' => '76.7769'],
                ],
            ],
            [
                'name' => 'Pathanamthitta',
                'iso3' => 'PTA',
                'cities' => [
                    ['name' => 'Pathanamthitta', 'latitude' => '9.2648', 'longitude' => '76.7870'],
                    ['name' => 'Adoor', 'latitude' => '9.1559', 'longitude' => '76.7319'],
                    ['name' => 'Tiruvalla', 'latitude' => '9.3835', 'longitude' => '76.5740'],
                    ['name' => 'Pandalam', 'latitude' => '9.2367', 'longitude' => '76.6853'],
                    ['name' => 'Konni', 'latitude' => '9.2267', 'longitude' => '76.8548'],
                ],
            ],
            [
                'name' => 'Alappuzha',
                'iso3' => 'ALP',
                'cities' => [
                    ['name' => 'Alappuzha', 'latitude' => '9.4981', 'longitude' => '76.3388'],
                    ['name' => 'Cherthala', 'latitude' => '9.6844', 'longitude' => '76.3356'],
                    ['name' => 'Haripad', 'latitude' => '9.2801', 'longitude' => '76.4644'],
                    ['name' => 'Kayamkulam', 'latitude' => '9.1817', 'longitude' => '76.5043'],
                    ['name' => 'Mavelikara', 'latitude' => '9.2593', 'longitude' => '76.5564'],
                ],
            ],
            [
                'name' => 'Kottayam',
                'iso3' => 'KTM',
                'cities' => [
                    ['name' => 'Kottayam', 'latitude' => '9.5916', 'longitude' => '76.5222'],
                    ['name' => 'Pala', 'latitude' => '9.7116', 'longitude' => '76.6836'],
                    ['name' => 'Changanassery', 'latitude' => '9.4427', 'longitude' => '76.5360'],
                    ['name' => 'Vaikom', 'latitude' => '9.7487', 'longitude' => '76.3956'],
                    ['name' => 'Ettumanoor', 'latitude' => '9.6703', 'longitude' => '76.5640'],
                ],
            ],
            [
                'name' => 'Idukki',
                'iso3' => 'IDK',
                'cities' => [
                    ['name' => 'Painavu', 'latitude' => '9.8983', 'longitude' => '76.9432'],
                    ['name' => 'Thodupuzha', 'latitude' => '9.8963', 'longitude' => '76.7144'],
                    ['name' => 'Munnar', 'latitude' => '10.0889', 'longitude' => '77.0595'],
                    ['name' => 'Nedumkandam', 'latitude' => '9.8467', 'longitude' => '77.1580'],
                    ['name' => 'Kattappana', 'latitude' => '9.7512', 'longitude' => '77.1173'],
                ],
            ],
            [
                'name' => 'Ernakulam',
                'iso3' => 'EKM',
                'cities' => [
                    ['name' => 'Kochi', 'latitude' => '9.9312', 'longitude' => '76.2673'],
                    ['name' => 'Aluva', 'latitude' => '10.1076', 'longitude' => '76.3516'],
                    ['name' => 'Angamaly', 'latitude' => '10.1956', 'longitude' => '76.3871'],
                    ['name' => 'Kothamangalam', 'latitude' => '10.0644', 'longitude' => '76.6294'],
                    ['name' => 'Perumbavoor', 'latitude' => '10.1069', 'longitude' => '76.4737'],
                ],
            ],
            [
                'name' => 'Thrissur',
                'iso3' => 'TSR',
                'cities' => [
                    ['name' => 'Thrissur', 'latitude' => '10.5276', 'longitude' => '76.2144'],
                    ['name' => 'Guruvayur', 'latitude' => '10.5943', 'longitude' => '76.0411'],
                    ['name' => 'Kodungallur', 'latitude' => '10.2326', 'longitude' => '76.1951'],
                    ['name' => 'Chalakudy', 'latitude' => '10.3002', 'longitude' => '76.3375'],
                    ['name' => 'Irinjalakuda', 'latitude' => '10.3424', 'longitude' => '76.2112'],
                ],
            ],
            [
                'name' => 'Palakkad',
                'iso3' => 'PKD',
                'cities' => [
                    ['name' => 'Palakkad', 'latitude' => '10.7867', 'longitude' => '76.6548'],
                    ['name' => 'Ottappalam', 'latitude' => '10.7702', 'longitude' => '76.3776'],
                    ['name' => 'Shoranur', 'latitude' => '10.7618', 'longitude' => '76.2708'],
                    ['name' => 'Mannarkkad', 'latitude' => '10.9922', 'longitude' => '76.4642'],
                    ['name' => 'Pattambi', 'latitude' => '10.8054', 'longitude' => '76.1954'],
                ],
            ],
            [
                'name' => 'Malappuram',
                'iso3' => 'MLP',
                'cities' => [
                    ['name' => 'Malappuram', 'latitude' => '11.0510', 'longitude' => '76.0711'],
                    ['name' => 'Manjeri', 'latitude' => '11.1203', 'longitude' => '76.1191'],
                    ['name' => 'Perinthalmanna', 'latitude' => '10.9716', 'longitude' => '76.2273'],
                    ['name' => 'Tirur', 'latitude' => '10.9167', 'longitude' => '75.9167'],
                    ['name' => 'Ponnani', 'latitude' => '10.7677', 'longitude' => '75.9259'],
                ],
            ],
            [
                'name' => 'Kozhikode',
                'iso3' => 'KOZ',
                'cities' => [
                    ['name' => 'Kozhikode', 'latitude' => '11.2588', 'longitude' => '75.7804'],
                    ['name' => 'Vadakara', 'latitude' => '11.6010', 'longitude' => '75.5924'],
                    ['name' => 'Koyilandy', 'latitude' => '11.4396', 'longitude' => '75.6956'],
                    ['name' => 'Ramanattukara', 'latitude' => '11.1743', 'longitude' => '75.8672'],
                    ['name' => 'Feroke', 'latitude' => '11.1799', 'longitude' => '75.8419'],
                ],
            ],
            [
                'name' => 'Wayanad',
                'iso3' => 'WYD',
                'cities' => [
                    ['name' => 'Kalpetta', 'latitude' => '11.6085', 'longitude' => '76.0834'],
                    ['name' => 'Sulthan Bathery', 'latitude' => '11.6643', 'longitude' => '76.2570'],
                    ['name' => 'Mananthavady', 'latitude' => '11.8020', 'longitude' => '76.0026'],
                    ['name' => 'Panamaram', 'latitude' => '11.7401', 'longitude' => '76.0730'],
                    ['name' => 'Meenangadi', 'latitude' => '11.6347', 'longitude' => '76.1982'],
                ],
            ],
            [
                'name' => 'Kannur',
                'iso3' => 'KNR',
                'cities' => [
                    ['name' => 'Kannur', 'latitude' => '11.8745', 'longitude' => '75.3704'],
                    ['name' => 'Thalassery', 'latitude' => '11.7488', 'longitude' => '75.4929'],
                    ['name' => 'Payyanur', 'latitude' => '12.1043', 'longitude' => '75.2020'],
                    ['name' => 'Taliparamba', 'latitude' => '12.0403', 'longitude' => '75.3593'],
                    ['name' => 'Mattannur', 'latitude' => '11.9302', 'longitude' => '75.5711'],
                ],
            ],
            [
                'name' => 'Kasaragod',
                'iso3' => 'KSD',
                'cities' => [
                    ['name' => 'Kasaragod', 'latitude' => '12.4996', 'longitude' => '74.9869'],
                    ['name' => 'Kanhangad', 'latitude' => '12.3151', 'longitude' => '75.0900'],
                    ['name' => 'Nileshwar', 'latitude' => '12.2598', 'longitude' => '75.1352'],
                    ['name' => 'Uppala', 'latitude' => '12.6744', 'longitude' => '74.8941'],
                    ['name' => 'Manjeshwaram', 'latitude' => '12.7192', 'longitude' => '74.8886'],
                ],
            ],
        ];

        foreach ($districts as $districtData) {
            $district = Country::updateOrCreate(
                ['iso3' => $districtData['iso3']],
                [
                    'name' => $districtData['name'],
                    'iso2' => 'IN',
                    'status' => 1,
                    'phone_code' => 91,
                    'region' => 'Kerala',
                    'subregion' => 'District',
                ]
            );

            $state = CountryStates::updateOrCreate(
                [
                    'country_id' => $district->id,
                    'name' => $districtData['name'],
                ],
                [
                    'status' => 1,
                ]
            );

            foreach ($districtData['cities'] as $cityData) {
                CountryCities::updateOrCreate(
                    [
                        'state_id' => $state->id,
                        'name' => $cityData['name'],
                    ],
                    [
                        'country_id' => $district->id,
                        'country_code' => 'IN',
                        'latitude' => $cityData['latitude'],
                        'longitude' => $cityData['longitude'],
                        'status' => 1,
                    ]
                );
            }
        }
    }
}
