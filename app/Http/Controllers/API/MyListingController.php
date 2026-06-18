<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Imports\ListingsImport;
use App\Models\Amenity;
use App\Models\BusinessHour;
use App\Models\CollectDynamicFormData;
use App\Models\DynamicForm;
use App\Models\Listing;
use App\Models\ListingAmenity;
use App\Models\ListingCategory;
use App\Models\ListingImage;
use App\Models\ListingSeo;
use App\Models\Package;
use App\Models\Product;
use App\Models\PurchasePackage;
use App\Models\UserReview;
use App\Models\WebsiteAndSocial;
use App\Rules\AlphaDashWithoutSlashes;
use App\Traits\ApiResponse;
use App\Traits\ListingTrait;
use App\Traits\Notify;
use App\Traits\Upload;
use Carbon\Carbon;
use hisorange\BrowserDetect\Exceptions\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MyListingController extends Controller
{
    use ApiResponse, Upload, Notify, ListingTrait;

    public function purchasePackages(Request $request, $paginate = null)
    {
        $package_name = $request->name;
        $purchase_date = Carbon::parse($request->purchase_date);
        $expire_date = Carbon::parse($request->expire_date);
        $status = $request->status;

        $purchasePackageQuery = PurchasePackage::with(['get_package:id','get_package.details:id,package_id,language_id,title'])
            ->when(isset($request->name), function ($query) use ($package_name) {
                return $query->whereHas('get_package.details', function ($q) use ($package_name) {
                    $q->where('title', 'Like', '%' . $package_name . '%');
                });
            })
            ->when($request->purchase_date && $request->expire_date, function ($query) use ($purchase_date, $expire_date) {
                $query->whereBetween('purchase_date', [$purchase_date, $expire_date]);
            })
            ->when(isset($request->status), function ($q4) use ($status){
                return $q4->where('status', $status);
            })
            ->where('user_id', auth()->id())
            ->latest();

        $purchasePackage = ($paginate == null)
            ? $purchasePackageQuery->paginate(config('basic.paginate'))
            : $purchasePackageQuery->get();


        $formatedPurchasePackages = $purchasePackage->map(function ($item) {
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'package_id' => $item->package_id,
                'trx_id' => $item->trx_id,
                'deposit_id' => $item->deposit_id,
                'api_subscription_id' => $item->api_subscription_id,
                'title' => $item->get_package->details->title,
                'price' => $item->price,
                'is_renew' => $item->is_renew,
                'is_image' => $item->is_image,
                'is_video' => $item->is_video,
                'is_amenities' => $item->is_amenities,
                'expiry_time' => $item->expiry_time,
                'is_product' => $item->is_product,
                'is_create_from' => $item->is_create_from,
                'is_business_hour' => $item->is_business_hour,
                'no_of_listing' => $item->no_of_listing,
                'no_of_img_per_listing' => $item->no_of_img_per_listing,
                'no_of_categories_per_listing' => $item->no_of_categories_per_listing,
                'no_of_amenities_per_listing' => $item->no_of_amenities_per_listing,
                'no_of_product' => $item->no_of_product,
                'no_of_img_per_product' => $item->no_of_img_per_product,
                'seo' => $item->seo,
                'is_whatsapp' => $item->is_whatsapp,
                'is_messenger' => $item->is_messenger,
                'purchase_from' => $item->purchase_from,
                'type' => $item->type,
                'status' => $item->status,
                'purchase_date' => $item->purchase_date,
                'expire_date' => $item->expire_date,
                'created_at' => $item->created_at,
            ];
        });

        $purchasePackage = ($paginate == null)
            ? $purchasePackage->setCollection($formatedPurchasePackages)
            : $formatedPurchasePackages;

        $info = [
            'status' => '0 = Pending, 1 = Approved, 2 = Cancelled',
            'no_of_listing' => 'null = Unlimited',
            'Validity' => 'expire_date >= currentDate ? Active : Expired',
            'api_subscription_id' => 'null = Manual, !null = Automatic',
            'action button condition' => 'follow condition-01 into condition.php file',
        ];
        return response()->json($this->withSuccess($purchasePackage, $info));
    }


    public function listings(Request $request, $type = null)
    {
        $current_user = Auth::user();
        $search = $request->all();
        $categoryIds = $request->category;

        $user_listings = Listing::with(['get_package:id,user_id,package_id','get_package.get_package:id','get_package.get_package.details:id,package_id,title','get_place:id,name'])
            ->select('id','user_id','country_id','category_id','purchase_package_id','title','slug','address','status','is_active','created_at')->latest()
            ->when(isset($categoryIds), function ($query) use ($categoryIds) {
                foreach ($categoryIds as $key => $category_id) {
                    $query->whereJsonContains('category_id', $category_id);
                }
            })
            ->when(isset($search['name']), function ($query) use ($search) {
                return $query->where('title', 'LIKE', "%{$search['name']}%");
            })
            ->when(isset($search['package_id']), function ($query) use ($search) {
                return $query->whereHas('get_package', function ($q) use ($search) {
                    $q->where('purchase_package_id', $search['package_id']);
                });
            })
            ->when(isset($search['country_id']), function ($query) use ($search) {
                return $query->whereHas('get_place', function ($q3) use ($search) {
                    $q3->where('id', $search['country_id']);
                });
            })
            ->when(strtolower($type) == 'pending', function ($query) {
                return $query->where('status', '0');
            })
            ->when(strtolower($type) == 'approved', function ($query) {
                return $query->where('status', '1');
            })
            ->when(strtolower($type) == 'rejected', function ($query) {
                return $query->where('status', '2');
            })
            ->where('user_id', $current_user->id)
            ->paginate(basicControl()->paginate);

        $formatedUserListing = $user_listings->getCollection()->map(function ($item){
           return [
               'id' => $item->id,
               'user_id' => $item->user_id,
               'purchase_package_id' => $item->purchase_package_id,
               'purchase_package_name' => $item->get_package->get_package->details->title,
               'categories' => html_entity_decode($item->getCategoriesName()),
               'listing_title' => $item->title,
               'listing_slug' => $item->slug,
               'address' => $item->address,
               'is_active' => $item->is_active,
               'status' => $item->status,
           ];
        });
        $user_listings->setCollection($formatedUserListing);

        $info = [
            'status' => '0 = Pending, 1 = Approved, 2 = Rejected',
            'is_active' => '0 = Deactive, 1 = Active',
            'for action button' => 'Follow Condition 02 into condition.php'
        ];
        return response()->json($this->withSuccess($user_listings, $info));
    }

    public function addListing(Request $request, $id)
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
            'phone' => 'nullable|numeric',
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
            return response()->json($this->withError(collect($validator->errors())->collapse()));
        }

        if (config('demo.IS_DEMO')) {
            return response()->json($this->withError('You are not allowed to change content on DEMO Version'));
        }

        DB::beginTransaction();
        try {
            $purchase_package_info = PurchasePackage::where('user_id', Auth::id())->where('status', 1)->find($id);

            if (!$purchase_package_info) {
                return response()->json($this->withError('Purchase Package does not exists'));
            }
            $user = Auth::user();

            if (!empty($purchase_package_info->no_of_listing) && $purchase_package_info->no_of_listing <= 0) {
                return response()->json($this->withError('You don\'t have any quota to create listing for this package'));
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
                    return response()->json($this->withError($e->getMessage()));
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
                return response()->json($this->withSuccess('Your listing has been created successfully! Admin approval is required to view the listing'));
            } else {
                Listing::findOrFail($listing->id)->update([
                    'status' => 1,
                ]);
                DB::commit();
                return response()->json($this->withSuccess('Your listing has been created successfully!'));
            }
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json($this->withError(collect($exception->getMessage())->collapse()));
        }
    }

    public function editListing($id)
    {
        $listing = Listing::where('user_id', auth()->id())
            ->select('id','user_id','category_id','purchase_package_id','country_id','state_id','city_id','title','slug','email','phone','description','lat','long','youtube_video_id','thumbnail','thumbnail_driver'
                ,'status','is_active','fb_app_id','fb_page_id','whatsapp_number','replies_text','body_text')
            ->toBase()->find($id);

        if (!$listing) {
            return response()->json($this->withError('Listing not Found'));
        }
        if ($listing->status == 2) {
            return response()->json($this->withError('Listing has been rejected by Admin'));
        }

        $formatedLisitng = [
            'id' => $listing->id,
            'user_id' => $listing->user_id,
            'category_id' => json_decode($listing->category_id),
            'purchase_package_id' => $listing->purchase_package_id,
            'country_id' => $listing->country_id,
            'state_id' => $listing->state_id,
            'city_id' => $listing->city_id,
            'title' => $listing->title,
            'slug' => $listing->slug,
            'email' => $listing->email,
            'phone' => $listing->phone,
            'description' => $listing->description,
            'lat' => $listing->lat,
            'long' => $listing->long,
            'youtube_video_id' => $listing->youtube_video_id,
            'thumbnail' => getFile($listing->thumbnail_driver, $listing->thumbnail),
            'status' => $listing->status,
            'is_active' => $listing->is_active,
            'fb_app_id' => $listing->fb_app_id,
            'fb_page_id' => $listing->fb_page_id,
            'whatsapp_number' => $listing->whatsapp_number,
            'replies_text' => $listing->replies_text,
            'body_text' => $listing->body_text
        ];

        $purchase_package = PurchasePackage::where('id',$listing->purchase_package_id)->toBase()->first();
        $business_hours = BusinessHour::where('listing_id', $id)->select('id','listing_id','working_day','start_time','end_time')->toBase()->get();
        $social_links = WebsiteAndSocial::where('listing_id', $id)->select('id','listing_id','social_icon','social_url')->toBase()->get();
        $listing_images = ListingImage::where('listing_id', $id)->select('id','listing_id','listing_image','driver')->toBase()->get()->map(function ($image){
            return [
                'id' => $image->id,
                'listing_id' => $image->listing_id,
                'listing_image' => getFile($image->driver, $image->listing_image),
            ];
        });
        $listing_aminities = ListingAmenity::select('amenity_id')->where('listing_id', $id)->toBase()->pluck('amenity_id')->toArray();

        $listing_products = Product::with('get_product_image:id,product_id,product_image,driver')
            ->select('id','user_id','listing_id','product_title','product_price','product_description','product_thumbnail','driver')->where('listing_id', $id)->get();
        if (isset($listing_products)){
            $formated_listing_products = $listing_products->map(function ($product){
                $productImages = $product->get_product_image->map(function ($image){
                    return [
                        'id' => $image->id,
                        'product_image' => getFile($image->driver, $image->product_image),
                    ];
                });
                return [
                    'id' => $product->id,
                    'user_id' => $product->user_id,
                    'listing_id' => $product->listing_id,
                    'product_title' => $product->product_title,
                    'product_price' => $product->product_price,
                    'product_description' => $product->product_description,
                    'product_thumbnail' => getFile($product->driver, $product->product_thumbnail),
                    'product_images' => $productImages,
                ];
            });
        }

        $listing_seo = ListingSeo::where('listing_id', $id)->select('id','meta_title','meta_description','meta_keywords','meta_robots','og_description','seo_image','driver')->toBase()->first();
        if (isset($listing_seo)) {
            $formated_listing_seo = [
                'id' => $listing_seo->id,
                'meta_title' => $listing_seo->meta_title,
                'meta_keywords' => $listing_seo->meta_keywords,
                'meta_robots' => $listing_seo->meta_robots,
                'meta_description' => $listing_seo->meta_description,
                'og_description' => $listing_seo->og_description,
                'seo_image' => getFile($listing_seo->driver, $listing_seo->seo_image),
            ];
        }

        $listing_form = DynamicForm::where('listing_id', $id)->select('id','name','button_text','input_form','status')->toBase()->first();
        if (isset($listing_form)) {
            $formated_listing_form = [
                'id' => $listing_form->id,
                'name' => $listing_form->name,
                'button_text' => $listing_form->button_text,
                'input_form' => json_decode($listing_form->input_form),
                'status' => $listing_form->status,
            ];
        }

        $data = [
            'listing' => $formatedLisitng,
            'package_info' => $purchase_package,
            'business_hours' => $business_hours,
            'social_links' => $social_links,
            'listing_images' => $listing_images,
            'listing_aminities' => $listing_aminities,
            'listing_products' => $formated_listing_products ?? null,
            'listing_seo' => $formated_listing_seo ?? null,
            'listing_form' => $formated_listing_form ?? null,
        ];

        return response()->json($this->withSuccess($data));
    }

    public function updateListing(Request $request, $id)
    {

        $rules = [
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'min:1',
                'max:500',
                Rule::unique('listings')->ignore($id),
                new AlphaDashWithoutSlashes(),
            ],
            'category_id' => 'required|array',
            'category_id.*' => 'exists:listing_categories,id',
            'email' => 'nullable|email',
            'phone' => 'nullable|numeric',
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
            return response()->json($this->withError(collect($validator->errors())->collapse()));
        }
        if (config('demo.IS_DEMO')) {
            return response()->json($this->withError('You are not allowed to change content on DEMO Version'));
        }

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $listing = Listing::has('get_package')->with('get_package')->where('user_id', $user->id)->find($id);

            if (!$listing) {
                return response()->json($this->withError('Listing does not exists'));
            }

            if ($request->hasFile('thumbnail')) {
                try {
                    $thumbnailImage = $this->fileUpload($request->thumbnail, config('filelocation.listing_thumbnail.path'), null,null, 'webp', 99, $listing->thumbnail, $listing->thumbnail_driver);
                    if ($thumbnailImage) {
                        $listing->thumbnail = $thumbnailImage['path'];
                        $listing->thumbnail_driver = $thumbnailImage['driver'];
                    }
                }catch (\Exception $e) {
                    return response()->json($this->withError($e->getMessage()));
                }
            }

            $numberOfCategoriesPerListing = min(count($request->category_id), optional($listing->get_package)->no_of_categories_per_listing ?? 1);
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

            if(optional($listing->get_package)->is_whatsapp == 1){
                $listing->whatsapp_number = $request->whatsapp_number;
                $listing->replies_text = $request->replies_text;
                $listing->body_text = $request->body_text;
            }
            if(optional($listing->get_package)->is_messenger == 1){
                $listing->fb_app_id = $request->fb_app_id;
                $listing->fb_page_id = $request->fb_page_id;
            }
            if ($request->youtube_video_id) {
                $listing->youtube_video_id = $request->youtube_video_id;
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

            if (optional($listing->get_package)->is_amenities && !empty($request->amenity_id)) {
                ListingAmenity::where('listing_id', $id)->delete();
                $numberOfAmenitiesPerListing = min(count($request->amenity_id), optional($listing->get_package)->no_of_amenities_per_listing ?? 500);
                $this->insertAmenitites($numberOfAmenitiesPerListing, $request, $listing, $listing->purchase_package_id);
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
            return response()->json($this->withSuccess('Your listing has been Updated'));
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json($this->withError(collect($exception->getMessage())->collapse()));
        }
    }

    public function deleteListing($id)
    {
        if (config('demo.IS_DEMO')) {
            return response()->json($this->withError('You are not allowed to change content on DEMO Version'));
        }

        DB::beginTransaction();
        try {
            $listing = Listing::with(['get_package', 'listingImages', 'get_listing_amenities', 'get_business_hour', 'get_social_info',
                'get_products', 'listingSeo', 'get_reviews', 'listingAnalytics', 'listingClaims', 'allWishlists', 'productQueries.replies', 'listingViews','form'])->where('user_id', auth()->id())->find($id);

            if (!$listing){
                return response()->json($this->withError('Listing does not exists'));
            }

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
            return response()->json($this->withSuccess('Your listing has been deleted'));
        }catch (Exception $exception){
            DB::rollBack();
            return response()->json($this->withError(collect($exception->getMessage())->collapse()));
        }
    }

    public function reviews(Request $request, $id)
    {
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $reviews = UserReview::with(['getListing:id,title', 'review_user_info:id,firstname,lastname,username,email'])
            ->select('id','listing_id','user_id','rating','review','created_at')
            ->when(isset($search['user_id']), function ($query) use ($search) {
                return $query->whereHas('review_user_info', function ($q) use ($search) {
                    $q->where('id', $search['user_id']);
                });
            })
            ->when(!empty($search['rating']), function ($query) use ($search) {
                return $query->where('rating', $search['rating']);
            })
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
                return $q2->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->where('listing_id', $id)
            ->latest()->paginate(basicControl()->paginate);

        $formatedReviews = $reviews->getCollection()->map(function ($review) {
            $review_user_info = [
                'id' => $review->review_user_info->id,
                'firstname' => $review->review_user_info->firstname,
                'lastname' => $review->review_user_info->lastname,
                'username' => $review->review_user_info->username,
                'email' => $review->review_user_info->email,
                'image' => $review->review_user_info->imgPath,
            ];

            return [
                'id' => $review->id,
                'user_id' => $review->user_id,
                'listing_id' => $review->listing_id,
                'listing_title' => $review->getListing->title,
                'rating' => $review->rating,
                'avgRating' => $review->getListing->avgRating,
                'review' => $review->review,
                'reviewer_info' => $review_user_info,
                'created_at' => $review->created_at,
            ];
        });
        $reviews->setCollection($formatedReviews);
        return response()->json($this->withSuccess($reviews));
    }

    public function dynamicFormData(Request $request, $id)
    {
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $dynamicFormData = CollectDynamicFormData::where('listing_id', $id)
            ->select('id','form_name','input_form','created_at')
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
                return $q2->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->latest()->paginate(basicControl()->paginate);
        return response()->json($this->withSuccess($dynamicFormData));
    }

    public function listingImportCsv(Request $request)
    {
        if (config('demo.IS_DEMO')) {
            return response()->json($this->withError('You are not allowed to delete on DEMO Version'));
        }
        $request->validate([
            'package_id' => 'required',
            'file' => 'required|file'
        ]);

        if ($request->file->getClientOriginalExtension() != 'csv') {
            throw new \Exception('Only accepted .csv files');
        }

        $purchase_package_info = PurchasePackage::where('user_id', Auth::id())->where('status', 1)->find($request->package_id);

        if(!$purchase_package_info){
            return response()->json($this->withError('The package does not exist'));
        }
        if($purchase_package_info->expire_date != null && \Carbon\Carbon::now() > \Carbon\Carbon::parse($purchase_package_info->expire_date)){
            return response()->json($this->withError('The package has expired'));
        }

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
        return response()->json($this->withSuccess('Listings imported successfully!'));
    }

    public function listingImportCsvSampleDownload()
    {
        $file = 'listing-import-sample.csv';
        $full_path = 'assets/' . $file;
        $title = 'listing-import-sample';
        return response()->json($this->withSuccess([
            'file_title' => $title,
            'file_path' => $full_path,
        ]));
    }



}
