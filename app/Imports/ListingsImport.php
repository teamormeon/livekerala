<?php

namespace App\Imports;

use App\Models\AmenityDetails;
use App\Models\Country;
use App\Models\CountryCities;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Models\ListingCategoryDetails;
use App\Models\PurchasePackage;
use App\Traits\ListingImportTrait;
use App\Traits\Upload;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListingsImport implements ToModel, WithHeadingRow, WithChunkReading
{
    use ListingImportTrait, Upload;

    public $user;
    public $purchasePackage;

    public function __construct($purchasePackage)
    {
        $this->user = Auth::user();
        $this->purchasePackage = $purchasePackage;
    }

    public function model(array $row)
    {
        DB::beginTransaction();
        try {
            $listingCategories = ListingCategoryDetails::all();
            $rowCategories = explode(',', $row[2]);
            $categoryIds = [];
            foreach ($rowCategories as $categoryItem) {
                $existingCategory = $listingCategories->firstWhere(function ($cat) use ($categoryItem) {
                    return strtolower($cat->name) == strtolower(trim($categoryItem));
                });
                if ($existingCategory) {
                    $categoryIds[] = $existingCategory->listing_category_id;
                }
            }

            $countryId = null;
            $stateId = null;
            $cityId = null;

            $countries = Country::select('id', 'name')->where('status', 1)->orderBy('name', 'ASC')->toBase()->get();
            $existingCountry = $countries->firstWhere(function($country) use ($row) {
                return strtolower($country->name) == strtolower($row[6]);
            });
            if ($existingCountry) {
                $countryId = $existingCountry->id;
            }

            $cities = CountryCities::select('id','country_id','state_id','name','latitude','longitude')->where('status', 1)->orderBy('name', 'ASC')->toBase()->get();
            $existingCity = $cities->firstWhere(function($city) use ($row) {
                return strtolower($city->name) == strtolower($row[7]);
            });
            if ($existingCity) {
                $stateId = $existingCity->state_id;
                $cityId = $existingCity->id;
            }

            $amenities = AmenityDetails::all();
            $rowAmenities = explode(',', $row[19]);
            $amenitiesIds = [];
            foreach ($rowAmenities as $amenityItem) {
                $existingAmenity = $amenities->firstWhere(function ($amenity) use ($amenityItem) {
                    return strtolower($amenity->title) == strtolower(trim($amenityItem));
                });
                if ($existingAmenity) {
                    $amenitiesIds[] = $existingAmenity->amenity_id;
                }
            }

            $rowWorkingDay = explode(',', $row[11]);
            $rowWorkingStartTime = explode(',', $row[12]);
            $rowWorkingEndTime = explode(',', $row[13]);
            $rowSocialIcon = explode(',', $row[14]);
            $rowSocialUrl = explode(',', $row[15]);
            $rowImagesUrl = explode(',', $row[18]);
            $listing = new Listing();

            if (isset($row[17])) {
                try {
                    $thumbnailImage = $this->fileUpload($row[17], config('filelocation.listing_thumbnail.path'), null,null, 'webp', 99);
                    if ($thumbnailImage) {
                        $listing->thumbnail = $thumbnailImage['path'];
                        $listing->thumbnail_driver = $thumbnailImage['driver'];
                    }
                }catch (\Exception $e) {
                    return back()->with('error', __("Thumbnail could not be uploaded."));
                }
            }

            $numberOfCategoriesPerListing = min(count($categoryIds), $this->purchasePackage->no_of_categories_per_listing ?? 1);

            $slug = $row[1];
            if (Listing::where('slug', $slug)->exists()) {
                $slug = $row[1] . '-' . rand(1000, 9999);
                while (Listing::where('slug', $slug)->exists()) {
                    $slug = $row[1] . '-' . rand(1000, 9999);
                }
            }

            $listing->user_id = $this->user->id;
            $listing->category_id = array_slice($categoryIds, 0, $numberOfCategoriesPerListing);
            $listing->purchase_package_id = $this->purchasePackage->id;
            $listing->country_id = $countryId ?? ($cityId ? $existingCity->country_id : null);
            $listing->state_id = $stateId;
            $listing->city_id = $cityId;
            $listing->title = $row[0];
            $listing->slug = $slug;
            $listing->email = $row[4];
            $listing->phone = $row[5];
            $listing->description = $row[3];
            $listing->address = $row[8];
            $listing->lat = $row[9];
            $listing->long = $row[10];
            $listing->status = basicControl()->listing_approval;
            $listing->youtube_video_id = $row[16];

            if($this->purchasePackage->is_messenger == 1){
                $listing->fb_app_id = $row[20];
                $listing->fb_page_id = $row[21];
            }
            if($this->purchasePackage->is_whatsapp == 1){
                $listing->whatsapp_number = $row[22];
                $listing->replies_text = $row[23];
                $listing->body_text = $row[24];
            }
            $listing->save();

            if ($this->purchasePackage->is_business_hour && !empty($rowWorkingDay)) {
                $this->insertBusinessHours($rowWorkingDay, $rowWorkingStartTime, $rowWorkingEndTime, $listing, $this->purchasePackage->id);
            }

            if (!empty($rowSocialIcon)) {
                $this->insertSocialAndWebsite($rowSocialIcon, $rowSocialUrl, $listing, $this->purchasePackage->id);
            }

            if ($this->purchasePackage->is_image && !empty($rowImagesUrl)) {
                $numberOfImgPerListing = min(count($rowImagesUrl), $this->purchasePackage->no_of_img_per_listing ?? 500);
                $this->uploadListingImages($numberOfImgPerListing, $rowImagesUrl, $listing, $this->purchasePackage->id);
            }

            if ($this->purchasePackage->is_amenities && !empty($amenitiesIds)) {
                $numberOfAmenitiesPerListing = min(count($amenitiesIds), $this->purchasePackage->no_of_amenities_per_listing ?? 500);
                $this->insertAmenitites($numberOfAmenitiesPerListing, $amenitiesIds, $listing, $this->purchasePackage->id);
            }

            if ($this->purchasePackage->seo && (isset($row[25]) || isset($row[26]))) {
                $this->insertSEO($listing, $row, $this->purchasePackage->id);
            }

            if ($this->purchasePackage->no_of_listing != null) {
                $this->purchasePackage->update([
                    'no_of_listing' => $this->purchasePackage->no_of_listing - 1,
                ]);
            }
            DB::commit();
            return $listing;
        } catch (\Exception $e) {
            DB::rollBack();
            return null;
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }

}
