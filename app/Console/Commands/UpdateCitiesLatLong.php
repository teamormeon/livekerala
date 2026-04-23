<?php

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Nnjeim\World\World;

class UpdateCitiesLatLong extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cities:update-latlong';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate latitude and longitude for cities';

    /**
     * Execute the console command.
     */

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
//        $cities = CountryCities::whereNull('latitude')->get();
        $cities = Country::where('id', '>', 0)->get();
        foreach ($cities as $city) {
            if (empty($city->latitude) || empty($city->longitude)) {

                $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'address' => $city->name,
                    'key' => basicControl()->google_map_app_key,
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['results'][0]['geometry']['location'])) {
                        $latitude = $data['results'][0]['geometry']['location']['lat'];
                        $longitude = $data['results'][0]['geometry']['location']['lng'];

                        $city->update([
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                        ]);

                        $this->info("Updated {$city->name} with Latitude: {$latitude} and Longitude: {$longitude}");
                    } else {
                        $this->warn("Could not retrieve lat/long for {$city->name}");
                    }
                } else {
                    $this->error("API call failed for {$city->name}. Error: " . $response->body());
                }
            } else {
                $this->info("Skipping {$city->name} as it already has latitude and longitude.");
            }
        }
        $this->info('Cities have been updated with latitude and longitude!');
    }
}
