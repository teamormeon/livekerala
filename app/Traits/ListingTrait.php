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

trait ListingTrait
{
    public function insertBusinessHours(Request $request, $listing, $id)
    {
        $businessHours = [];
        foreach ($request->working_day as $key => $value) {
            $businessHours[] = [
                'listing_id' => $listing->id,
                'purchase_package_id' => $id,
                'working_day' => $value,
                'start_time' => $request->start_time[$key],
                'end_time' => $request->end_time[$key],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('business_hours')->insert($businessHours);
    }

    public function insertSocialAndWebsite(Request $request, $listing, $id)
    {
        $socialWebsites = [];
        foreach ($request->social_icon as $key => $value) {
            $socialWebsites[] = [
                'listing_id' => $listing->id,
                'purchase_package_id' => $id,
                'social_icon' => $request->social_icon[$key],
                'social_url' => $request->social_url[$key],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('website_and_socials')->insert($socialWebsites);
    }

    public function uploadListingImages($numberOfImgPerListing, Request $request, $listing, $id)
    {
        for ($i = 0; $i < $numberOfImgPerListing; $i++) {
            if ($request->hasFile("listing_image.$i") && $request->file("listing_image.$i")->getSize() <= 5242880) {
                try {
                    $listingImage = new ListingImage();
                    $listingImage->listing_id = $listing->id;
                    $listingImage->purchase_package_id = $id;
                    $image = $this->fileUpload($request->listing_image[$i], config('filelocation.listing_images.path'), null,null, 'webp', 99);
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


    public function uploadProducts($request, $listing, $numberOfProductsPerListing, $isCreate = true)
    {
        for ($i = 0; $i < $numberOfProductsPerListing; $i++) {
            try {
                $product = Product::where('listing_id', $listing->id)->firstOrNew([
                    'id' => $request->product_id[$i] ?? null
                ]);
                $product->user_id = Auth::id();
                $product->listing_id = $listing->id;
                $product->purchase_package_id = $listing->purchase_package_id;
                $product->product_title = $request->product_title[$i];
                $product->product_price = $request->product_price[$i];
                $product->product_description = $request->product_description[$i];
                if ($request->hasFile("product_thumbnail.$i") && $request->file("product_thumbnail.$i")->getSize() <= 5242880) {
                    $image = $this->fileUpload($request->product_thumbnail[$i], config('filelocation.product_thumbnail.path'), null, null, 'webp', 99, $product->product_thumbnail, $product->driver);
                    if ($image) {
                        $product->product_thumbnail = $image['path'];
                        $product->driver = $image['driver'];
                    }
                }
                $product->save();
            } catch (\Exception $exp) {
                continue;
            }

            $oldProductImages = $request->old_product_image[$product->id] ?? [];
            if (!$isCreate) {
                $shouldBeDaletedProductImages = ProductImage::where('product_id', $product->id)->whereNotIn('id', $oldProductImages)->get();
                foreach ($shouldBeDaletedProductImages as $image) {
                    $this->fileDelete($image->driver, $image->product_image);
                    $image->delete();
                }
            }

            if (isset($request->product_image[$product->id]) || isset($request->product_image[$i + 1])) {
                $numberOfImagePerProduct = optional($listing->get_package)->no_of_img_per_product ?? 500;
                $imageCount = min(count($request->product_image[$product->id] ?? $request->product_image[$i + 1]), ($numberOfImagePerProduct - count($oldProductImages)));
                $newKey = $i + 1;
                for ($j = 0; $j < $imageCount; $j++) {
                    if ($request->hasFile("product_image.$newKey.$j")) {
                        try {
                            throw_if($request->file("product_image.$newKey.$j")->getSize() > 5242880, __('Max file size 5 MB.'));
                            $product_image = new ProductImage();
                            $product_image->product_id = $product->id;
                            $image = $this->fileUpload($request->product_image[$newKey][$j], config('filelocation.product_images.path'), null, null, 'webp', 99, $product_image->product_image, $product_image->driver);
                            if ($image) {
                                $product_image->product_image = $image['path'];
                                $product_image->driver = $image['driver'];
                            }
                            $product_image->save();
                        } catch (\Exception $exp) {
                            continue;
                        }
                    }
                }
            }
        }
    }

    public function insertAmenitites($numberOfAmenitiesPerListing, Request $request, $listing, $id)
    {
        $amenities = [];
        for ($i = 0; $i < $numberOfAmenitiesPerListing; $i++) {
            $amenities[] = [
                'listing_id' => $listing->id,
                'purchase_package_id' => $id,
                'amenity_id' => $request->amenity_id[$i],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        DB::table('listing_amenities')->insert($amenities);
    }

    public function insertSEO($listing, Request $request, $id)
    {
        try {
            $listingSeo = ListingSeo::firstOrNew([
                'listing_id' => $listing->id
            ]);
            $listingSeo->listing_id = $listing->id;
            $listingSeo->purchase_package_id = $id;
            $listingSeo->meta_title = $request->meta_title;
            $listingSeo->meta_keywords = $request->meta_keywords;
            $listingSeo->meta_robots = $request->meta_robots;
            $listingSeo->meta_description = $request->meta_description;
            $listingSeo->og_description = $request->og_description;
            if ($request->hasFile('seo_image')) {
                if (isset($listingSeo->seo_image)) {
                    $this->fileDelete($listingSeo->driver, $listingSeo->seo_image);
                }
                $image = $this->fileUpload($request->seo_image, config('filelocation.listing_seo.path'), null, null, 'webp', 99, $listingSeo->seo_image, $listingSeo->driver);
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
