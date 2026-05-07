<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\BusinessHour;
use App\Models\Country;
use App\Models\Listing;
use App\Models\ListingAmenity;
use App\Models\ListingCategory;
use App\Models\ListingImage;
use App\Models\ListingSeo;
use App\Models\Product;
use App\Models\PurchasePackage;
use App\Models\User;
use App\Models\WebsiteAndSocial;
use App\Rules\AlphaDashWithoutSlashes;
use App\Traits\ListingTrait;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ListingCreateController extends Controller
{
    use Upload, ListingTrait;

    public function create(Request $request)
    {
        $selectedUserId = $request->integer('user_id');
        $selectedPackageId = $request->integer('purchase_package_id');

        $availablePackages = PurchasePackage::with(['get_user', 'get_package.details'])
            ->where('status', 1)
            ->latest()
            ->get();

        $data['users'] = $availablePackages->pluck('get_user')
            ->filter()
            ->unique('id')
            ->sortBy(fn ($user) => strtolower(trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''))))
            ->values();

        if (!$selectedUserId && $selectedPackageId) {
            $selectedPackage = $availablePackages->firstWhere('id', $selectedPackageId);
            $selectedUserId = optional($selectedPackage)->user_id;
        }

        $data['selectedUserId'] = $selectedUserId;
        $data['packages'] = $availablePackages
            ->when($selectedUserId, fn ($collection) => $collection->where('user_id', $selectedUserId))
            ->values();
        $data['selectedPackageId'] = $selectedPackageId;
        $data['single_package_infos'] = null;

        if ($selectedPackageId) {
            $data['single_package_infos'] = PurchasePackage::with('get_package')
                ->where('status', 1)
                ->when($selectedUserId, fn ($query) => $query->where('user_id', $selectedUserId))
                ->findOrFail($selectedPackageId);

            $data['single_listing_infos'] = new Listing([
                'category_id' => [],
            ]);
            $data['all_listings_category'] = ListingCategory::with('details')->where('status', 1)->latest()->get();
            $data['all_places'] = Country::select('id', 'name', 'iso2')->where('status', 1)->orderBy('name', 'ASC')->get();
            $data['all_amenities'] = Amenity::with('details')->where('status', 1)->latest()->get();
            $data['listing_amenities'] = collect();
            $data['listing_seo'] = new ListingSeo();
            $data['listing_products'] = collect();
            $data['business_hours'] = collect();
            $data['social_links'] = collect();
            $data['listing_images'] = collect();
        }

        return view('admin.listings.create', $data);
    }

    public function store(Request $request)
    {
        $rules = [
            'user_id' => 'required|integer|exists:users,id',
            'purchase_package_id' => 'required|integer|exists:purchase_packages,id',
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'min:1',
                'max:500',
                Rule::unique('listings'),
                new AlphaDashWithoutSlashes(),
            ],
            'category_id' => 'required|array',
            'category_id.*' => 'exists:listing_categories,id',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'description' => 'required|string',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required|string',
            'lat' => 'required|between:-90,90',
            'long' => 'required|between:-180,180',
            'working_day.*' => 'nullable|string|max:20',
            'social_url.*' => 'nullable|url|max:180',
            'youtube_video_id' => 'nullable|string|max:20',
            'thumbnail' => 'nullable|mimes:jpeg,png,jpg|max:51200',
            'listing_image.*' => 'nullable|mimes:jpeg,png,jpg',
            'amenity_id.*' => 'nullable|numeric|exists:amenities,id',
            'product_title.*' => 'nullable|string|max:150',
            'product_price.*' => 'nullable|numeric',
            'product_description.*' => 'nullable|string',
            'product_image.*.*' => 'nullable|mimes:jpeg,png,jpg',
            'product_thumbnail.*' => 'nullable|mimes:jpeg,png,jpg',
            'seo_image' => 'nullable|mimes:jpeg,png,jpg|max:51200',
            'meta_title' => 'nullable|string|max:200',
            'meta_keywords' => 'nullable|string',
            'meta_description' => 'nullable|string',
        ];

        $message = [
            'thumbnail.mimes' => __('The thumbnail must be a file of type: jpg, jpeg, png.'),
            'thumbnail.max' => __('The thumbnail may not be greater than 5 MB.'),
            'category_id.required' => __('This category field is required.'),
            'category_id.array' => __('The category must be an array.'),
            'category_id.*.exists' => __('The selected category is invalid.'),
            'listing_image.*.mimes' => __('This listing image must be a file of type: jpg, jpeg, png.'),
            'working_day.*.string' => __('The working day must be a string.'),
            'working_day.*.max' => __('The working day may not be greater than :max characters.'),
            'social_url.*.url' => __('The social url should be a url.'),
            'social_url.*.max' => __('The social url may not be greater than :max characters.'),
            'product_title.*.string' => __('The product title must be a string.'),
            'product_title.*.max' => __('The product title may not be greater than :max characters.'),
            'product_price.*.numeric' => __('The product price should be numeric.'),
            'product_description.*.string' => __('The product description must be a string.'),
            'product_image.*.*.mimes' => __('This product image must be a file of type: jpg, jpeg, png.'),
            'product_thumbnail.*.mimes' => __('This product thumbnail must be a file of type: jpg, jpeg, png.'),
            'product_thumbnail.*.max' => __('The product thumbnail may not be greater than 5 MB.'),
            'seo_image' => __('The seo image may not be greater than 5 MB.'),
        ];

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return redirect()->route('admin.listing.create', [
                'user_id' => $request->user_id,
                'purchase_package_id' => $request->purchase_package_id,
            ])->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $purchasePackage = PurchasePackage::with('get_package')
                ->where('user_id', $request->user_id)
                ->where('status', 1)
                ->findOrFail($request->purchase_package_id);

            $user = User::findOrFail($request->user_id);
            $listing = new Listing();

            if ($request->hasFile('thumbnail')) {
                try {
                    $thumbnailImage = $this->fileUpload($request->thumbnail, config('filelocation.listing_thumbnail.path'), null, null, 'webp', 99);
                    if ($thumbnailImage) {
                        $listing->thumbnail = $thumbnailImage['path'];
                        $listing->thumbnail_driver = $thumbnailImage['driver'];
                    }
                } catch (\Exception $e) {
                    return back()->with('error', __('Thumbnail could not be uploaded.'));
                }
            }

            $numberOfCategoriesPerListing = min(count($request->category_id), $purchasePackage->no_of_categories_per_listing ?? 1);

            $listing->user_id = $user->id;
            $listing->purchase_package_id = $purchasePackage->id;
            $listing->skip_package_quota = true;
            $listing->title = $request->title;
            $listing->slug = $request->filled('slug') ? $request->slug : $this->generateUniqueSlug($request->title);
            $listing->category_id = array_slice($request->category_id, 0, $numberOfCategoriesPerListing);
            $listing->phone = $request->phone;
            $listing->email = $request->email;
            $listing->description = $request->description;
            $listing->country_id = $request->country_id;
            $listing->state_id = $request->state_id;
            $listing->city_id = $request->city_id;
            $listing->address = $request->address;
            $listing->lat = $request->lat;
            $listing->long = $request->long;
            $listing->status = 1;
            $listing->is_active = 1;

            if ($purchasePackage->is_whatsapp == 1) {
                $listing->whatsapp_number = $request->whatsapp_number;
                $listing->replies_text = $request->replies_text;
                $listing->body_text = $request->body_text;
            }

            if ($purchasePackage->is_messenger == 1) {
                $listing->fb_app_id = $request->fb_app_id;
                $listing->fb_page_id = $request->fb_page_id;
            }

            if ($request->filled('youtube_video_id')) {
                $listing->youtube_video_id = $request->youtube_video_id;
            }

            $listing->save();

            if ($purchasePackage->is_business_hour && !empty($request->working_day)) {
                $this->insertBusinessHours($request, $listing, $purchasePackage->id);
            }

            if (!empty($request->social_icon)) {
                $this->insertSocialAndWebsite($request, $listing, $purchasePackage->id);
            }

            if ($purchasePackage->is_image && !empty($request->listing_image)) {
                $numberOfImgPerListing = min(count($request->listing_image), $purchasePackage->no_of_img_per_listing ?? 500);
                $this->uploadListingImages($numberOfImgPerListing, $request, $listing, $purchasePackage->id);
            }

            if ($purchasePackage->is_product && !empty($request->product_title)) {
                $numberOfProductsPerListing = min(count($request->product_title), $purchasePackage->no_of_product ?? 500);
                $this->uploadProducts($request, $listing, $numberOfProductsPerListing);
            }

            if ($purchasePackage->is_amenities && !empty($request->amenity_id)) {
                $numberOfAmenitiesPerListing = min(count($request->amenity_id), $purchasePackage->no_of_amenities_per_listing ?? 500);
                $this->insertAmenitites($numberOfAmenitiesPerListing, $request, $listing, $purchasePackage->id);
            }

            if ($purchasePackage->seo && ($request->meta_title || $request->meta_description || $request->meta_keywords || $request->seo_image)) {
                $this->insertSEO($listing, $request, $purchasePackage->id);
            }

            if ($purchasePackage->no_of_listing != null && !$listing->skip_package_quota) {
                $purchasePackage->update([
                    'no_of_listing' => $purchasePackage->no_of_listing - 1,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.listing.edit', $listing->id)
                ->with('success', __('Listing created successfully.'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return back()->with('error', $exception->getMessage())->withInput();
        }
    }

    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $slug = $slug ?: 'listing';
        $originalSlug = $slug;
        $count = 1;

        while (Listing::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
