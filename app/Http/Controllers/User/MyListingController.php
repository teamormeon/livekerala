<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Imports\ListingsImport;
use App\Models\Amenity;
use App\Models\BusinessHour;
use App\Models\CollectDynamicFormData;
use App\Models\Country;
use App\Models\CountryCities;
use App\Models\CountryStates;
use App\Models\DynamicForm;
use App\Models\Listing;
use App\Models\ListingAmenity;
use App\Models\ListingCategory;
use App\Models\ListingCategoryDetails;
use App\Models\ListingImage;
use App\Models\ListingSeo;
use App\Models\Package;
use App\Models\Place;
use App\Models\Product;
use App\Models\PurchasePackage;
use App\Models\UserReview;
use App\Models\WebsiteAndSocial;
use App\Rules\AlphaDashWithoutSlashes;
use App\Traits\ListingTrait;
use App\Traits\Notify;
use App\Traits\Upload;
use Carbon\Carbon;
use hisorange\BrowserDetect\Exceptions\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MyListingController extends Controller
{
    use Upload, ListingTrait, Notify;

    public function listings(Request $request, $type = null)
    {
        $types = ['pending', 'approved', 'rejected'];
        abort_if(isset($type) && !in_array($type, $types), 404);
        $current_user = Auth::user();
        $data['packages'] = Package::with('details')->where('status', 1)->latest()->get();
        $data['listingCategories'] = ListingCategory::with('details')->where('status', 1)->latest()->get();
        $data['allAddresses'] = Country::select('id', 'name')->where('status', 1)->orderBy('name', 'ASC')->toBase()->get();
        $data['my_packages'] = PurchasePackage::with('get_package')->where('user_id', auth()->id())->get();

        $search = $request->all();
        $categoryIds = $request->category;

        $data['user_listings'] = Listing::with(['get_package.get_package', 'get_place'])->latest()
            ->when(isset($categoryIds), function ($query) use ($categoryIds) {
                if (implode('',$categoryIds) == 'all'){
                    $query->where('status', 1)->where('is_active', 1);
                }else{
                    foreach ($categoryIds as $key => $category_id) {
                        $query->whereJsonContains('category_id', $category_id);
                    }
                }
            })
            ->when(isset($search['name']), function ($query) use ($search) {
                return $query->where('title', 'LIKE', "%{$search['name']}%");
            })
            ->when(isset($search['package']), function ($query) use ($search) {
                return $query->whereHas('get_package', function ($q) use ($search) {
                    $q->where('package_id', $search['package']);
                });
            })
            ->when(isset($search['location']), function ($query) use ($search) {
                return $query->whereHas('get_place', function ($q3) use ($search) {
                    $q3->where('id', $search['location']);
                });
            })
            ->when($type == 'pending', function ($query) {
                return $query->where('status', '0');
            })
            ->when($type == 'approved', function ($query) {
                return $query->where('status', '1');
            })
            ->when($type == 'rejected', function ($query) {
                return $query->where('status', '2');
            })
            ->where('user_id', $current_user->id)
            ->paginate(basicControl()->paginate);
        return view('user_panel.user.listing.list', $data);
    }

    public function addListing($id)
    {
        $data['all_listings_category'] = ListingCategory::with('details')->where('status', 1)->latest()->get();
        $data['countries'] = Country::select('id', 'name','iso2')->where('status', 1)->orderBy('name', 'ASC')->has('state')->get();
        $data['all_amenities'] = Amenity::with('details')->where('status', 1)->latest()->get();

        $data['single_package_infos'] = PurchasePackage::with('get_package')->where('user_id', Auth::id())->where('status', 1)->findOrFail($id);
        return view('user_panel.user.listing.add_listing', $data, compact('id'));
    }

    public function storeListing(Request $request, $id)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if(config('demo.IS_DEMO')){
            return back()->with('error', "This is DEMO version. You can just explore all the features but can't take any action.");
        }
        DB::beginTransaction();
        try {
            $purchase_package_info = PurchasePackage::where('user_id', Auth::id())->where('status', 1)->findOrFail($id);
            $user = Auth::user();

            if (!empty($purchase_package_info->no_of_listing) && $purchase_package_info->no_of_listing <= 0) {
                return back()->with('error', __("You don't have any quota to create listing for this package."));
            }

            $listing = new Listing();

            if ($request->hasFile('thumbnail')) {
                try {
                    $thumbnailImage = $this->fileUpload($request->thumbnail, config('filelocation.listing_thumbnail.path'), null,null, 'webp', 99);
                    if ($thumbnailImage) {
                        $listing->thumbnail = $thumbnailImage['path'];
                        $listing->thumbnail_driver = $thumbnailImage['driver'];
                    }
                }catch (\Exception $e) {
                    return back()->with('error', __("Thumbnail could not be uploaded."));
                }
            }

            $numberOfCategoriesPerListing = min(count($request->category_id), $purchase_package_info->no_of_categories_per_listing ?? 1);

            $listing->user_id = $user->id;
            $listing->purchase_package_id = $id;
            $listing->title = $request->title;
            $listing->slug = $request->slug;
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
            $listing->status = 0;

            if($purchase_package_info->is_whatsapp == 1){
                $listing->whatsapp_number = $request->whatsapp_number;
                $listing->replies_text = $request->replies_text;
                $listing->body_text = $request->body_text;
            }
            if($purchase_package_info->is_messenger == 1){
                $listing->fb_app_id = $request->fb_app_id;
                $listing->fb_page_id = $request->fb_page_id;
            }
            if ($request->youtube_video_id) {
                $listing->youtube_video_id = $request->youtube_video_id;
            }
            $listing->save();

            if ($purchase_package_info->is_business_hour && !empty($request->working_day)) {
                $this->insertBusinessHours($request, $listing, $id);
            }

            if (!empty($request->social_icon)) {
                $this->insertSocialAndWebsite($request, $listing, $id);
            }

            if ($purchase_package_info->is_image && !empty($request->listing_image)) {
                $numberOfImgPerListing = min(count($request->listing_image), $purchase_package_info->no_of_img_per_listing ?? 500);
                $this->uploadListingImages($numberOfImgPerListing, $request, $listing, $id);
            }

            if ($purchase_package_info->is_product && !empty($request->product_title)) {
                $numberOfProductsPerListing = min(count($request->product_title), $purchase_package_info->no_of_product ?? 500);
                $this->uploadProducts($request, $listing, $numberOfProductsPerListing);
            }

            if ($purchase_package_info->is_amenities && !empty($request->amenity_id)) {
                $numberOfAmenitiesPerListing = min(count($request->amenity_id), $purchase_package_info->no_of_amenities_per_listing ?? 500);
                $this->insertAmenitites($numberOfAmenitiesPerListing, $request, $listing, $id);
            }

            if ($purchase_package_info->seo && ($request->meta_title || $request->meta_description || $request->meta_keywords || $request->seo_image)) {
                $this->insertSEO($listing, $request, $id);
            }

            if ($purchase_package_info->no_of_listing != null) {
                $purchase_package_info->update([
                    'no_of_listing' => $purchase_package_info->no_of_listing - 1,
                ]);
            }

            if ($request->has('field_name')) {
                $inputForm = [];
                foreach ($request->field_name as $index => $field) {
                    $inputForm[$index] = [
                        'field_name' => $field,
                        'type' => $request['input_type'][$index],
                        'validation' => $request['is_required'][$index]
                    ];

                    if ($request['input_type'][$index] == 'select') {
                        $optionNames = $request['option_name'][$index] ?? [];
                        $optionValues = $request['option_value'][$index] ?? [];

                        $options = array_combine($optionNames, $optionValues);
                        $inputForm[$index]['option'] = $options;
                    }
                }

                $dynamicForm = new DynamicForm();
                $dynamicForm->user_id = $user->id;
                $dynamicForm->package_id = $id;
                $dynamicForm->listing_id = $listing->id;
                $dynamicForm->name = $request->form_name;
                $dynamicForm->button_text = $request->form_btn_text;
                $dynamicForm->input_form = $inputForm;
                $dynamicForm->save();
            }

            $userName = $user->firstname . ' ' . $user->lastname;
            $msg = [
                'from' => $userName ?? null,
                'title' => $request->title ?? null,
            ];
            $action = [
                "link" => route('admin.listings'),
                'image' =>  getFile($user->image_driver, $user->image),
                "icon" => "fa fa-money-bill-alt text-white"
            ];
            $this->adminPushNotification('CREATE_LISTING_BY_USER', $msg, $action);

            if (basicControl()->listing_approval == 1) {
                DB::commit();
                return redirect()->route('user.listings')->with('success', __('Your listing has been created successfully! Admin approval is required to view the listing'));
            } else {
                Listing::findOrFail($listing->id)->update([
                    'status' => 1,
                ]);
                DB::commit();
                return back()->with('success', __('Your listing has been created successfully!'));
            }
        }catch (\Exception $exception){
            DB::rollBack();
            return back()->with('error', $exception->getMessage());
        }
    }

    public function editListing($id)
    {
        $data['single_listing_infos'] = Listing::where('user_id', auth()->id())->findOrFail($id);
        $data['single_package_infos'] = PurchasePackage::with('get_package')
            ->where('status', 1)
            ->findOrFail($data['single_listing_infos']->purchase_package_id);

        if ($data['single_listing_infos']->status == 2) {
            return redirect()->route('user.listings', 'rejected');
        }

        $data['all_listings_category'] = ListingCategory::with('details')->where('status', 1)->latest()->get();
        $data['all_places'] = Country::where('status', 1)->orderBy('name', 'ASC')->toBase()->get();
        $data['all_amenities'] = Amenity::with('details')->where('status', 1)->latest()->get();
        $data['listing_aminities'] = ListingAmenity::select('amenity_id')->where('listing_id', $id)->pluck('amenity_id')->toArray();
        $data['listing_products'] = Product::with('get_product_image')->where('listing_id', $id)->get();
        $data['listing_seo'] = ListingSeo::where('listing_id', $id)->first();
        $data['business_hours'] = BusinessHour::where('listing_id', $id)->get();
        $data['social_links'] = WebsiteAndSocial::where('listing_id', $id)->get();
        $data['listing_images'] = ListingImage::where('listing_id', $id)->get()->map(function ($image){
            $image->src = getFile($image->driver, $image->listing_image);
            return $image;
        });

        return view('user_panel.user.listing.edit_listing', $data, compact('id'));
    }

    public function updateListing(Request $request, $id)
    {
        $rules = [
            'title' => 'required|string|max:255',
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $listing = Listing::has('get_package')->with('get_package')->where('user_id', $user->id)->findOrFail($id);

            if ($request->hasFile('thumbnail')) {
                try {
                    $thumbnailImage = $this->fileUpload($request->thumbnail, config('filelocation.listing_thumbnail.path'), null,null, 'webp', 99,$listing->thumbnail, $listing->thumbnail_driver);
                    if ($thumbnailImage) {
                        $listing->thumbnail = $thumbnailImage['path'];
                        $listing->thumbnail_driver = $thumbnailImage['driver'];
                    }
                }catch (\Exception $e) {
                    return back()->with('error', __("Thumbnail could not be uploaded."));
                }
            }

            $listing->user_id = $user->id;
            $listing->title = $request->title;

            $numberOfCategoriesPerListing = min(count($request->category_id), optional($listing->get_package)->no_of_categories_per_listing ?? 1);
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

            if ($request->youtube_video_id) {
                $listing->youtube_video_id = $request->youtube_video_id;
            }
            if(optional($listing->get_package)->is_whatsapp == 1){
                $listing->whatsapp_number = $request->whatsapp_number;
                $listing->replies_text = $request->replies_text;
                $listing->body_text = $request->body_text;
            }
            if(optional($listing->get_package)->is_messenger == 1){
                $listing->fb_app_id = $request->fb_app_id;
                $listing->fb_page_id = $request->fb_page_id;
            }
            $listing->save();

            if (optional($listing->get_package)->is_business_hour && !empty($request->working_day)) {
                BusinessHour::where('listing_id', $id)->delete();
                $this->insertBusinessHours($request, $listing, $listing->purchase_package_id);
            }

            if (!empty($request->social_icon)) {
                WebsiteAndSocial::where('listing_id', $id)->delete();
                $this->insertSocialAndWebsite($request, $listing, $listing->purchase_package_id);
            }

            $old_listing_image = $request->old_listing_image ?? [];
            $dbImages = ListingImage::where('listing_id', $listing->id)->whereNotIn('id', $old_listing_image)->get();
            foreach ($dbImages as $dbImage) {
                $this->fileDelete($dbImage->driver, $dbImage->listing_image);
                $dbImage->delete();
            }

            if (optional($listing->get_package)->is_image && !empty($request->listing_image)) {
                $numberOfImagePerListing = optional($listing->get_package)->no_of_img_per_listing ?? 500;
                $leftNumberOfImagePerListing = min(count($request->listing_image), ($numberOfImagePerListing - count($old_listing_image ?? [])));
                $this->uploadListingImages($leftNumberOfImagePerListing, $request, $listing, $listing->purchase_package_id);
            }

            if (optional($listing->get_package)->is_amenities && !empty($request->amenity_id)) {
                ListingAmenity::where('listing_id', $id)->delete();
                $numberOfAmenitiesPerListing = min(count($request->amenity_id), optional($listing->get_package)->no_of_amenities_per_listing ?? 500);
                $this->insertAmenitites($numberOfAmenitiesPerListing, $request, $listing, $listing->purchase_package_id);
            }

            $oldProductImages = $request->product_id ?? [];
            $dbProducts = Product::with('get_product_image')->where('listing_id', $listing->id)->whereNotIn('id', $oldProductImages)->get();
            foreach ($dbProducts as $dbProduct) {
                foreach ($dbProduct->get_product_image as $pImage) {
                    $this->fileDelete($pImage->driver, $pImage->product_image);
                    $pImage->delete();
                }
                $this->fileDelete($dbProduct->driver, $dbProduct->product_thumbnail);
                $dbProduct->delete();
            }

            if (optional($listing->get_package)->is_product && !empty($request->product_title)) {
                $numberOfProductsPerListing = min(count($request->product_title), optional($listing->get_package)->no_of_product ?? 500);
                $this->uploadProducts($request, $listing, $numberOfProductsPerListing, false);
            }

            if (optional($listing->get_package)->seo && ($request->meta_title || $request->meta_description || $request->meta_keywords || $request->seo_image)) {
                $this->insertSEO($listing, $request, $listing->purchase_package_id);
            }

            if ($request->has('field_name')) {
                $inputForm = [];
                foreach ($request->field_name as $index => $field) {
                    $inputForm[$index] = [
                        'field_name' => $field,
                        'type' => $request['input_type'][$index],
                        'validation' => $request['is_required'][$index]
                    ];

                    if ($request['input_type'][$index] == 'select') {
                        $optionNames = $request['option_name'][$index] ?? [];
                        $optionValues = $request['option_value'][$index] ?? [];
                        $options = array_combine($optionNames, $optionValues);
                        $inputForm[$index]['option'] = $options;
                    }
                }

                $dynamicForm = DynamicForm::updateOrCreate(
                    ['user_id' => $user->id, 'listing_id' => $listing->id],
                    [
                        'package_id' => $listing->purchase_package_id ?? null,
                        'name' => $request['form_name'],
                        'button_text' => $request['form_btn_text'],
                        'input_form' => $inputForm,
                    ]
                );
            }

            DB::commit();
            return back()->with('success', __('Listing update successfully'));
        }catch (Exception $exception){
            DB::rollBack();
            return back()->with('error', $exception->getMessage());
        }
    }

    public function updateListingSlug(Request $request)
    {
        $request->validate([
            'slug' => [
                'required',
                'min:1',
                'max:500',
                Rule::unique('listings')->ignore($request->listingId),
                new AlphaDashWithoutSlashes(),
            ],
            'listingId' => 'required|integer|exists:listings,id',
        ]);

        $listing = Listing::find($request->listingId);

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found.'
            ], 404);
        }

        $listing->slug = $request->slug;
        $listing->save();

        return response()->json([
            'success' => true,
            'message' => 'Slug updated successfully.',
            'slug' => $listing->slug,
        ], 200);
    }

    public function deleteListing($id)
    {
        DB::beginTransaction();
        try {
            $listing = Listing::with(['get_package', 'listingImages', 'get_listing_amenities', 'get_business_hour', 'get_social_info',
                'get_products', 'listingSeo', 'get_reviews', 'listingAnalytics', 'listingClaims', 'allWishlists', 'productQueries.replies', 'listingViews','form'])->where('user_id', auth()->id())->findOrFail($id);

            if (optional($listing->get_package)->expire_date != null) {
                $expiry_date = $listing->get_package->expire_date;
                $current_date = Carbon::now();
                $no_of_listing = $listing->get_package->no_of_listing;

                if ($current_date <= $expiry_date) {
                    $increase = $no_of_listing + 1;
                    $listing->get_package->no_of_listing = $increase;
                    $listing->get_package->save();
                }
            }

            foreach ($listing->listingImages as $lisImage) {
                $this->fileDelete($lisImage->driver, $lisImage->listing_image);
                $lisImage->delete();
            }

            foreach ($listing->get_products as $dbProduct) {
                foreach ($dbProduct->get_product_image as $pImage) {
                    $this->fileDelete($pImage->driver, $pImage->product_image);
                    $pImage->delete();
                }
                $this->fileDelete($dbProduct->driver, $dbProduct->product_thumbnail);
                $dbProduct->delete();
            }

            foreach ($listing->productQueries as $query){
                foreach ($query->replies as $reply){
                    $this->fileDelete($reply->driver, $reply->file);
                    $reply->delete();
                }
                $query->delete();
            }

            if(optional($listing->listingSeo)->seo_image){
                $this->fileDelete(optional($listing->listingSeo)->driver, optional($listing->listingSeo)->seo_image);
                optional($listing->listingSeo)->delete();
            }

            foreach ($listing->get_listing_amenities as $lisAmenity) {
                $lisAmenity->delete();
            }

            foreach ($listing->get_business_hour as $business) {
                $business->delete();
            }

            foreach ($listing->get_social_info as $social) {
                $social->delete();
            }

            foreach ($listing->get_reviews as $review) {
                $review->delete();
            }

            foreach ($listing->listingAnalytics as $analytic) {
                $analytic->delete();
            }

            foreach ($listing->allWishlists as $wishlist) {
                $wishlist->delete();
            }

            foreach ($listing->listingViews as $view) {
                $view->delete();
            }

            foreach ($listing->listingClaims as $claim) {
                $claim->delete();
            }

            if (isset($listing->form)){
                $listing->form->delete();
            }
            $this->fileDelete($listing->thumbnail_driver, $listing->thumbnail);
            $listing->delete();
            DB::commit();
            return back()->with('success', __('Listing has been deleted successfully.'));
        }catch (Exception $exception){
            DB::rollBack();
            return back()->with('error', $exception->getMessage());
        }
    }



    public function reviews(Request $request, $id)
    {
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $data['allReviews'] = UserReview::with(['getListing', 'review_user_info'])
            ->when(isset($search['user']), function ($query) use ($search) {
                return $query->whereHas('review_user_info', function ($q) use ($search) {
                    $q->where('id', 'LIKE', "%{$search['user']}%");
                });
            })
            ->when(!empty($search['rating']), function ($query) use ($search) {
                return $query->whereIn('rating', $search['rating']);
            })
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
                return $q2->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->where('listing_id', $id)
            ->latest()->paginate(basicControl()->paginate);
        $data['listing'] = Listing::where('user_id', Auth::id())->findOrFail($id);
        return view('user_panel.user.listing.reviews', $data);
    }

    public function dynamicFormData(Request $request, $id)
    {
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $data['listing'] = Listing::findOrFail($id);
        $data['dynamicFOrmData'] = CollectDynamicFormData::where('listing_id', $id)
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
                return $q2->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->latest()->paginate(basicControl()->paginate);
        return view('user_panel.user.listing.dynamic_form_data', $data);
    }

    public function listingImportCsv(Request $request)
    {
        if ($request->method() == 'GET') {
            $fileFields = [];
            $filePath = asset('assets/listing-import-sample.csv');
            $file = fopen($filePath, 'r');
            while (($row = fgetcsv($file)) != false) {
                foreach ($row as $field) {
                    preg_match('/(.*)\s\((.*)\)/', $field, $matches);
                    if (count($matches) == 3) {
                        $fileFields[$matches[1]] = $matches[2];
                    }
                }
                break;
            }
            fclose($file);
            $data['fileFields'] = $fileFields;
            $data['packages'] = PurchasePackage::with('get_package')->where('user_id', auth()->id())->get();
            return view('user_panel.user.listing.import_csv', $data);
        } elseif ($request->method() == 'POST') {
            $rules = [
                'package' => 'required',
                'file' => 'required|file'
            ];
            $validation = Validator::make($request->all(), $rules);
            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()]);
            }

            if ($request->file->getClientOriginalExtension() != 'csv') {
                throw new \Exception('Only accepted .csv files');
            }

            $purchase_package_info = PurchasePackage::where('user_id', Auth::id())->where('status', 1)->findOrFail($request->package);
            $no_of_listings = $purchase_package_info->no_of_listing;

            $file = fopen($request->file->getRealPath(), 'r');
            $csvRows = [];
            $firstIteration = true;
            $count = 0;
            while (($row = fgetcsv($file)) != false) {
                if ($firstIteration) {
                    $firstIteration = false;
                    continue;
                }
                $csvRows[] = $row;
                $count++;
                if ($no_of_listings != null && $count >= $no_of_listings) {
                    break;
                }
            }
            fclose($file);

            $import = new ListingsImport($purchase_package_info);
            foreach ($csvRows as $row) {
                $import->model($row);
            }
            $data =[
                'success' => true,
                'message' => 'Listings imported successfully'
            ];
            return response()->json($data);
        }
    }

    public function listingImportCsvSampleDownload()
    {
        $file = 'listing-import-sample.csv';
        $full_path = 'assets/' . $file;
        $title = 'listing-import-sample';

        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mimetype = mime_content_type($full_path);

        return response()->download($full_path, $title . '.' . $ext, [
            'Content-Type' => $mimetype,
        ]);

    }


    public function getStates(Request $request)
    {
        $data['states'] = CountryStates::where('country_id', $request->country_id)->where('status', 1)->has('cities')->get();
        return response()->json($data);
    }
    public function getCities(Request $request)
    {
        $data['cities'] = CountryCities::where('state_id', $request->state_id)->where('status', 1)->get();
        return response()->json($data);
    }


    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (Listing::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        return $slug;
    }
}
