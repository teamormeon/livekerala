<?php

namespace App\Traits;

use App\Models\ListingImage;
use App\Models\ListingSeo;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait ListingImportTrait
{
    public function insertBusinessHours($workingDay, $workingStartTime, $workingEndTime, $listing, $purchasePackageId)
    {
        $businessHours = [];
        foreach ($workingDay as $key => $value) {
            $businessHours[] = [
                'listing_id' => $listing->id,
                'purchase_package_id' => $purchasePackageId,
                'working_day' => $value,
                'start_time' => $workingStartTime[$key],
                'end_time' => $workingEndTime[$key],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('business_hours')->insert($businessHours);
    }

    public function insertSocialAndWebsite($socialIcon, $socialUrl, $listing, $purchasePackageId)
    {
        $socialWebsites = [];
        foreach ($socialIcon as $key => $value) {
            $socialWebsites[] = [
                'listing_id' => $listing->id,
                'purchase_package_id' => $purchasePackageId,
                'social_icon' => $value,
                'social_url' => $socialUrl[$key],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('website_and_socials')->insert($socialWebsites);
    }

    public function uploadListingImages($numberOfImgPerListing, $rowImagesUrl, $listing, $purchasePackageId)
    {
        for ($i = 0; $i < $numberOfImgPerListing; $i++) {
            if ($rowImagesUrl[$i]) {
                try {
                    $listingImage = new ListingImage();
                    $listingImage->listing_id = $listing->id;
                    $listingImage->purchase_package_id = $purchasePackageId;
                    $image = $this->fileUpload($rowImagesUrl[$i], config('filelocation.listing_images.path'), null,null, 'webp', 99);
                    if ($image) {
                        $listingImage->listing_image = $image['path'];
                        $listingImage->driver = $image['driver'];
                    }
                    $listingImage->save();
                } catch (\Exception $exp) {
                    continue;
                }
            }
        }
    }



    public function insertAmenitites($numberOfAmenitiesPerListing, $amenitiesIds, $listing, $purchasePackageId)
    {
        $amenities = [];
        for ($i = 0; $i < $numberOfAmenitiesPerListing; $i++) {
            $amenities[] = [
                'listing_id' => $listing->id,
                'purchase_package_id' => $purchasePackageId,
                'amenity_id' => $amenitiesIds[$i],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        DB::table('listing_amenities')->insert($amenities);
    }

    public function insertSEO($listing, $row, $purchasePackageId)
    {
        try {
            $listingSeo = ListingSeo::firstOrNew([
                'listing_id' => $listing->id
            ]);
            $listingSeo->listing_id = $listing->id;
            $listingSeo->purchase_package_id = $purchasePackageId;
            $listingSeo->meta_title = $row[25];
            $listingSeo->meta_description = $row[26];
            $listingSeo->meta_keywords = $row[27];
            if ($row[27]) {
                $image = $this->fileUpload($row[27], config('filelocation.listing_seo.path'), null, null, 'webp', 99, $listingSeo->seo_image, $listingSeo->driver);
                if ($image) {
                    $listingSeo->seo_image = $image['path'];
                    $listingSeo->driver = $image['driver'];
                }
            }
            $listingSeo->save();
        } catch (\Exception $exp) {
        }
    }

}
