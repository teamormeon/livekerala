<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Analytics;
use App\Models\BasicControl;
use App\Models\BusinessHour;
use App\Models\CollectDynamicFormData;
use App\Models\Country;
use App\Models\Listing;
use App\Models\ListingAmenity;
use App\Models\ListingCategory;
use App\Models\ListingImage;
use App\Models\ListingSeo;
use App\Models\PackageExpiryCron;
use App\Models\Place;
use App\Models\Product;
use App\Models\PurchasePackage;
use App\Models\User;
use App\Models\UserReview;
use App\Models\WebsiteAndSocial;
use App\Traits\ListingTrait;
use App\Traits\Notify;
use App\Traits\Upload;
use Carbon\Carbon;
use hisorange\BrowserDetect\Exceptions\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ListingController extends Controller
{
    use Notify, Upload, ListingTrait;

    public function listings(){
        $listingData = Listing::selectRaw("
                COUNT(*) AS totalListing,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS totalActiveListing,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) AS totalInactiveListing,
                SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) AS totalPending,
                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS totalApproved,
                SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) AS totalRejected
            ")->first();

        $data['totalListing'] = $listingData->totalListing;
        $data['totalActiveListing'] = $listingData->totalActiveListing ?? 0;
        $data['growthPercentageActive'] = ($data['totalListing'] > 0) ? ($data['totalActiveListing'] / $data['totalListing']) * 100 : 0;
        $data['totalInactiveListing'] = $listingData->totalInactiveListing ?? 0;
        $data['growthPercentageInactive'] = ($data['totalListing'] > 0) ? ($data['totalInactiveListing'] / $data['totalListing']) * 100 : 0;
        $data['totalPending'] = $listingData->totalPending ?? 0;
        $data['growthPercentagePending'] = ($data['totalListing'] > 0) ? ($data['totalPending'] / $data['totalListing']) * 100 : 0;
        $data['totalApproved'] = $listingData->totalApproved ?? 0;
        $data['growthPercentageApproved'] = ($data['totalListing'] > 0) ? ($data['totalApproved'] / $data['totalListing']) * 100 : 0;
        $data['totalRejected'] = $listingData->totalRejected ?? 0;
        $data['growthPercentageRejected'] = ($data['totalListing'] > 0) ? ($data['totalRejected'] / $data['totalListing']) * 100 : 0;
        return view('admin.listings.index', $data);
    }

    public function listingSearch(Request $request)
    {
        $search = $request->search['value']??null;
        $filterStatus = $request->filterStatus;
        $filterActiveStatus = $request->filterActiveStatus;
        $filterDate = explode('-', $request->filterDate);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;

        $listings = Listing::query()->with(['get_user', 'get_package.get_package'])->orderBy('id', 'DESC')
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%")
                    ->orWhereHas('get_package.get_package.details', function ($query) use ($search) {
                        return $query->where('title', 'LIKE', "%{$search}%");
                    })->orWhereHas('get_user', function ($query) use ($search) {
                        return $query->where('firstname', 'LIKE', "%{$search}%")
                            ->orWhere('lastname', 'LIKE', "%{$search}%")
                            ->orWhere('username', 'LIKE', "%{$search}%");
                    });
            })

            ->when(isset($filterStatus), function ($query) use ($filterStatus) {
                if ($filterStatus == 'all') {
                    return $query->whereNotNull('status');
                }else{
                    return $query->where('status', $filterStatus);
                }
            })
            ->when(isset($filterActiveStatus), function ($query) use ($filterActiveStatus) {
                if ($filterActiveStatus == 'all') {
                    return $query->whereNotNull('is_active');
                }else{
                    return $query->where('is_active', $filterActiveStatus);
                }
            })
            ->when(!empty($request->filterDate) && $endDate == null, function ($query) use ($startDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $query->whereDate('created_at', $startDate);
            })
            ->when(!empty($request->filterDate) && $endDate != null, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $endDate = Carbon::createFromFormat('d/m/Y', trim($endDate));
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });

        return DataTables::of($listings)
            ->addColumn('checkbox', function ($item) {
                return ' <input type="checkbox" id="chk-' . $item->id . '"
                           class="form-check-input row-tic tic-check" name="check" value="' . $item->id . '"
                           data-id="' . $item->id . '">';
            })
            ->addColumn('user', function ($item) {
                $url = route('admin.user.view.profile', optional($item->get_user)->id);
                return '<a class="d-flex align-items-center me-2" href="' . $url . '">
                                <div class="flex-shrink-0">
                                  ' . $item->get_user->profilePicture() . '
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <h5 class="text-hover-primary mb-0">' . optional($item->get_user)->firstname . ' ' . optional($item->get_user)->lastname . '</h5>
                                  <span class="fs-6 text-body">@' . optional($item->get_user)->email . '</span>
                                </div>
                              </a>';

            })
            ->addColumn('package', function ($item) {
                return '<div>
                            <span class="d-block mb-0 ps-2">' .optional(optional(optional($item->get_package)->get_package)->details)->title. '</span>
                        </div>';
            })
            ->addColumn('category', function ($item) {
                return '<div>
                            <span class="d-block mb-0 ps-2">' .$item->getCategoriesName(). '</span>
                        </div>';
            })
            ->addColumn('listing-title', function ($item) {
                return '<div>
                            <span class="d-block mb-0 ps-2">' .\Illuminate\Support\Str::limit($item->title, 30). '</span>
                        </div>';
            })
            ->addColumn('stage', function ($item) {
                if ($item->status == 0) {
                    return '<span class="badge bg-soft-warning text-warning">
                            <span class="legend-indicator bg-warning"></span>' . trans('Pending') . '
                        </span>';
                } elseif ($item->status == 1) {
                    return '<span class="badge bg-soft-success text-success">
                            <span class="legend-indicator bg-success"></span>' . trans('Approved') . '
                        </span>';
                } else {
                    return '<span class="badge bg-soft-danger text-danger">
                                <span class="legend-indicator bg-danger"></span>' . trans('Rejected') . '
                            </span>
                            <sup class="customSub">
                            <a href="javascript:void(0)"
                            class="listingRejectedInfo"
                            data-owner="'.$item->user->firstname.' '.$item->user->lastname.'"
                            data-title="' .$item->title. '"
                            data-rejectedreason="' .$item->rejected_reason. '"
                            data-bs-toggle="modal" data-bs-target="#singleRejectedInfoModal"
                            title="Rejected Reason"><i class="fas fa-info text-danger"></i></a></sup>';
                }
            })
            ->addColumn('status', function ($item) {
                if ($item->is_active == 1) {
                    $statusHtml = '<span class="badge bg-soft-secondary text-dark"><span class="legend-indicator bg-secondary"></span>'.trans('Running').'</span>
                           ';
                } elseif ($item->is_active == 0) {
                    $statusHtml =  '<a href="javascript:void(0)"  class="listingRejectedInfo" data-owner="' . $item->user->firstname . ' ' . $item->user->lastname . '"
                            data-title="' . $item->title . '"
                            data-deactivatedreason="' . $item->deactive_reason . '"
                            data-bs-toggle="modal" data-bs-target="#deactiveInfoModal"><span class="badge bg-soft-danger text-danger"><span class="legend-indicator bg-danger"></span>' . trans('Disabled') . '</span>
                            <i class="bi-exclamation-diamond-fill text-warning"
                            title="Canceled Reason"></i></a>';
                } else {
                    return '<span class="badge text-danger">Unpaid</span>';
                }
                return $statusHtml;
            })
            ->addColumn('created-date', function ($item) {
                return dateTime($item->created_at);
            })
            ->addColumn('action', function ($item) {
                $canViewAnalytics = adminAccessRoute(config('role.listing_analytics.access.view'));
                $canViewReviews = adminAccessRoute(config('role.listing_reviews.access.view'));
                $canViewListingFormData = adminAccessRoute(config('role.listing_form.access.view'));
                $canEditListing = adminAccessRoute(config('role.manage_listing.access.edit'));
                $canDeleteListing = adminAccessRoute(config('role.manage_listing.access.delete'));

                $editUrl = route('admin.listing.edit', [$item->id]);
                $analyticsUrl = route('admin.listing.single.analytics', [$item->id]);
                $reviewsUrl = route('admin.listing.reviews', [$item->id]);
                $formDataUrl = route('admin.listing.form.data', [$item->id]);
                $deleteUrl = route("admin.listing.delete", $item->id);
                $singleApprovedUrl = route("admin.single.listing.approved");
                $singleRejectedUrl = route("admin.single.listing.rejected");
                $singleActiveUrl = route("admin.single.listing.active");
                $singleDeactiveUrl = route("admin.single.listing.deactive");

                $actionButtons = '<div class="btn-group" role="group">';

                if ($canEditListing){
                    $actionButtons .= '<a href="' . $editUrl . '" class="btn btn-white btn-sm">
                                    <i class="bi-pencil-fill me-1"></i> ' . trans("Edit") . '
                                  </a>';
                }

                $actionButtons .=    '<div class="btn-group">
                                      <button type="button" class="btn btn-white btn-icon btn-sm dropdown-toggle dropdown-toggle-empty" id="userEditDropdown" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                      <div class="dropdown-menu dropdown-menu-end mt-1" aria-labelledby="userEditDropdown">';

                if ($canEditListing && $item->status == 0) {
                    $actionButtons .= '<a class="dropdown-item singleApprovedBtn" href="javascript:void(0)"
                           data-route="' . $singleApprovedUrl . '"
                           data-listingId="' . $item->id . '"
                           data-bs-toggle="modal" data-bs-target="#singleApprovedModal">
                            <i class="bi bi-check-square dropdown-item-icon"></i>
                            ' . trans("Approved") . '
                        </a>
                        <a class="dropdown-item singleRejectedBtn" href="javascript:void(0)"
                           data-route="' . $singleRejectedUrl . '"
                           data-listingId="' . $item->id . '"
                           data-bs-toggle="modal" data-bs-target="#singleRejectedModal">
                            <i class="bi bi-x-square dropdown-item-icon"></i>
                            ' . trans("Rejected") . '
                        </a>';
                }
                if ($canEditListing && $item->is_active == 1) {
                    $actionButtons .= '<a class="dropdown-item deactiveBtn" href="javascript:void(0)"
                           data-route="' . $singleDeactiveUrl . '"
                           data-listingId="' .$item->id. '"
                           data-title="' .$item->title. '"
                           data-bs-toggle="modal" data-bs-target="#deactiveModal">
                            <i class="bi bi-toggle-off dropdown-item-icon"></i>
                            ' . trans("Deactive") . '
                        </a>';
                }elseif($canEditListing && $item->is_active == 0){
                    $actionButtons .= '<a class="dropdown-item activeBtn" href="javascript:void(0)"
                           data-route="' . $singleActiveUrl . '"
                           data-listingId="' .$item->id. '"
                            data-title="' .$item->title. '"
                           data-bs-toggle="modal" data-bs-target="#activeModal">
                            <i class="bi bi-toggle-on dropdown-item-icon"></i>
                            ' . trans("Active") . '
                        </a>';
                }
                if ($canViewAnalytics){
                    $actionButtons .= '<a class="dropdown-item" href="'.$analyticsUrl.'">
                                        <i class="bi bi-graph-up-arrow dropdown-item-icon"></i>
                                        ' . trans("Analytics") . '
                                    </a>';
                }

                if ($canViewReviews){
                    $actionButtons .=  '<a class="dropdown-item" href="'.$reviewsUrl.'">
                                        <i class="bi bi-star dropdown-item-icon"></i>
                                        ' . trans("Reviews") . '
                                    </a>';
                }

                if ($canViewListingFormData){
                    $actionButtons .=  '<a class="dropdown-item" href="'.$formDataUrl.'">
                                        <i class="bi bi-info-circle dropdown-item-icon"></i>
                                        ' . trans("Form Data") . '
                                    </a>';
                }

                if ($canDeleteListing){
                    $actionButtons .=   '<a class="dropdown-item deleteBtn" href="javascript:void(0)"
                                           data-route="' . $deleteUrl . '"
                                           data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="bi bi-trash dropdown-item-icon"></i>
                                        '.trans("Delete").'
                                    </a>';
                }
                $actionButtons .='</div>
                                </div>
                              </div>';


                return $actionButtons;
            })->rawColumns(['checkbox', 'user', 'package', 'category', 'listing-title', 'stage', 'status', 'created-date', 'action'])
            ->make(true);
    }

    public function listingEdit($id)
    {
        $data['single_listing_infos'] = Listing::findOrFail($id);
        $data['single_package_infos'] = PurchasePackage::with('get_package')->findOrFail($data['single_listing_infos']->purchase_package_id);
        $data['all_listings_category'] = ListingCategory::with('details')->latest()->get();
        $data['all_places'] = Country::where('status',1)->latest()->get();
        $data['all_amenities'] = Amenity::with('details')->latest()->get();
        $data['listing_amenities'] = ListingAmenity::select('amenity_id')->where('listing_id', $id)->get();
        $data['listing_seo'] = ListingSeo::where('listing_id', $id)->first();
        $data['listing_products'] = Product::with('get_product_image')->where('listing_id', $id)->get();
        $data['business_hours'] = BusinessHour::where('listing_id', $id)->get();
        $data['social_links'] = WebsiteAndSocial::where('listing_id', $id)->get();
        $data['listing_images'] = ListingImage::where('listing_id', $id)->get()->map(function ($image){
            $image->src = getFile($image->driver, $image->listing_image);
            return $image;
        });
        return view('admin.listings.edit', $data);
    }

    public function listingUpdate(Request $request, $id)
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
            'lat' => 'required',
            'long' => 'required',
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

        $request->validate($rules, $message);

        DB::beginTransaction();
        try {
            $listing = Listing::has('get_package')->with('get_package')->findOrFail($id);
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


            DB::commit();
            return back()->with('success', __('Listing update successfully'));
        }catch (Exception $exception){
            DB::rollBack();
            return back()->with('error', $exception->getMessage());
        }

    }

    public function singleListingApproved(Request $request)
    {
        $listing = Listing::with('user')->findOrFail($request->listingId);
        $listing->status = 1;
        $listing->save();

        $admin = Auth::user();
        $msg = [
            'userListing' => $listing->title,
            'from' => $admin->name ?? null,
        ];
        $action = [
            "link" => route('user.listings'),
            "icon" => "fa fa-money-bill-alt text-white",
            'image' =>  getFile($admin->image_driver, $admin->image),
        ];
        $user = $listing->user;
        $this->userPushNotification($user, 'LISTING_APPROVED_BY_ADMIN', $msg, $action);

        session()->flash('success', __('Listing Status Has Been Approved'));
        return response()->json(['success' => 1]);
    }

    public function multiListingApproved(Request $request)
    {
        if ($request->listingIds == null) {
            session()->flash('error', __('You do not select any Listing.'));
            return response()->json(['error' => 1]);
        } else {
            foreach ($request->listingIds as $key => $listingId) {
                $listing = Listing::with('user')->findOrFail($listingId);
                if ($listing->status == 1) {
                    session()->flash('error', "Already `$listing->title` listing has been approved.");
                    return response()->json(['error' => 1]);
                } elseif ($listing->status == 2) {
                    session()->flash('error', "You can't approved rejected listing! You can deactivate or delete `$listing->title` listing if you wish");
                    return response()->json(['error' => 1]);
                } else {
                    $admin = Auth::user();
                    $msg = [
                        'userListing' => $listing->title,
                        'from' => $admin->name ?? null,
                    ];

                    $action = [
                        "link" => route('user.listings'),
                        'image' =>  getFile($admin->image_driver, $admin->image),
                        "icon" => "fa fa-money-bill-alt text-white"
                    ];
                    $user = $listing->user;
                    $this->userPushNotification($user, 'LISTING_APPROVED_BY_ADMIN', $msg, $action);
                    $listing->status = 1;
                    $listing->save();
                }
            }
            session()->flash('success', __('Listing Status Has Been Approved'));
            return response()->json(['success' => 1]);
        }
    }

    public function singleListingRejected(Request $request)
    {
        DB::beginTransaction();
        try {
            $listing = Listing::with('user')->findOrFail($request->listingId);
            if ($request->rejectReason == '') {
                session()->flash('error', __('Listing reject reason is required.'));
                return response()->json(['error' => 1]);
            } else {
                $purchase_package = PurchasePackage::findOrFail($listing->purchase_package_id);
                if ($purchase_package->expire_date != null && $purchase_package->expire_date >= Carbon::now()) {
                    if ($purchase_package->no_of_listing != null){
                        $purchase_package->no_of_listing += 1;
                        $purchase_package->save();
                    }
                }
                $listing->status = 2;
                $listing->rejected_reason = $request->rejectReason;
                $listing->save();

                $admin = Auth::user();
                $msg = [
                    'userListing' => $listing->title,
                    'rejectReason' => $request->rejectReason,
                    'from' => $admin->name ?? null,
                ];
                $action = [
                    "link" => route('user.listings'),
                    'image' =>  getFile($admin->image_driver, $admin->image),
                    "icon" => "fa fa-money-bill-alt text-white"
                ];
                $user = $listing->user;
                $this->userPushNotification($user, 'LISTING_REJECTED_BY_ADMIN', $msg, $action);

                DB::commit();
                session()->flash('success', __('Listing Status Has Been Rejected'));
                return response()->json(['success' => 1]);
            }
        }catch (\Exception $e){
            DB::rollBack();
            session()->flash('error', $e->getMessage());
            return response()->json(['error' => 1, 'message' => $e->getMessage()]);
        }
    }

    public function multiListingRejected(Request $request)
    {
        if ($request->listingIds == null) {
            session()->flash('error', 'You do not select any Listing.');
            return response()->json(['error' => 1]);
        } else {
            if ($request->rejectReason == '') {
                session()->flash('error', __('Listing reject reason is required.'));
                return response()->json(['error' => 1]);
            } else {
                DB::beginTransaction();
                try {
                    foreach ($request->listingIds as $key => $listingId) {
                        $listing = Listing::with('user')->findOrFail($listingId);
                        if ($listing->status == 2) {
                            session()->flash('error', "Already `$listing->title` listing has been rejected.");
                            return response()->json(['error' => 1]);
                        } elseif ($listing->status == 1) {
                            session()->flash('error', "You can't rejected approved listing! You can deactivate `$listing->title` listing if you wish");
                            return response()->json(['error' => 1]);
                        } else {
                            $purchase_package = PurchasePackage::findOrFail($listing->purchase_package_id);
                            if ($purchase_package->expire_date != null && $purchase_package->expire_date >= Carbon::now()) {
                                $purchase_package->no_of_listing += 1;
                                $purchase_package->save();
                            }

                            $admin = Auth::user();
                            $msg = [
                                'userListing' => $listing->title,
                                'rejectReason' => $request->rejectReason,
                                'from' => $admin->name ?? null,
                            ];

                            $action = [
                                "link" => route('user.listings'),
                                'image' =>  getFile($admin->image_driver, $admin->image),
                                "icon" => "fa fa-money-bill-alt text-white"
                            ];
                            $user = $listing->get_user;
                            $this->userPushNotification($user, 'LISTING_REJECTED_BY_ADMIN', $msg, $action);
                        }
                    }
                    Listing::whereIn('id', $request->listingIds)->update([
                        'status' => 2,
                        'rejected_reason' => $request->rejectReason,
                    ]);
                    DB::commit();
                    session()->flash('success', __('Listing Status Has Been Rejected'));
                    return response()->json(['success' => 1]);
                }catch (\Exception $e){
                    DB::rollBack();
                    session()->flash('error', $e->getMessage());
                    return response()->json(['error' => 1, 'message' => $e->getMessage()]);
                }
            }
        }
    }

    public function singleListingActive(Request $request)
    {
        $listing = Listing::with('user')->findOrFail($request->listingId);
        $listing->is_active = 1;
        $listing->deactive_reason = null;
        $listing->save();

        $admin = Auth::user();

        $msg = [
            'userListing' => $listing->title ?? null,
            'from' => $admin->name ?? null,
        ];

        $action = [
            "link" => route('user.listings'),
            "image" =>  getFile($admin->image_driver, $admin->image),
            "icon" => "fa fa-money-bill-alt text-white"
        ];
        $user = $listing->user;
        $this->userPushNotification($user, 'LISTING_ACTIVATED_BY_ADMIN', $msg, $action);
        session()->flash('success', __('listing has been activated successfully'));
        return response()->json(['success' => 1]);

    }

    public function singleListingDeactive(Request $request)
    {
        if ($request->deactiveReason == '') {
            session()->flash('error', __('Listing deactive reason is required.'));
            return response()->json(['error' => 1]);
        } else {
            $listing = Listing::with('user')->findOrFail($request->listingId);
            $listing->is_active = 0;
            $listing->deactive_reason = $request->deactiveReason;
            $listing->save();

            $admin = Auth::user();
            $msg = [
                'userListing' => $listing->title ?? null,
                'deactiveReason' => $request->deactiveReason,
                'from' => $admin->name ?? null,
            ];
            $action = [
                "link" => route('user.listings'),
                'image' =>  getFile($admin->image_driver, $admin->image),
                "icon" => "fa fa-money-bill-alt text-white"
            ];
            $user = $listing->user;
            $this->userPushNotification($user, 'LISTING_DEACTIVATED_BY_ADMIN', $msg, $action);

            session()->flash('success', __('listing has been deactivated successfully'));
            return response()->json(['success' => 1]);
        }
    }

    public function listingDelete($id)
    {
        DB::beginTransaction();
        try {
            $listing = Listing::with(['get_package', 'listingImages', 'get_listing_amenities', 'get_business_hour', 'get_social_info',
                'get_products', 'listingSeo', 'get_reviews', 'listingAnalytics', 'listingClaims', 'allWishlists', 'productQueries.replies', 'listingViews'])->findOrFail($id);

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

            $this->fileDelete($listing->thumbnail_driver, $listing->thumbnail);
            $listing->delete();
            DB::commit();
            return back()->with('success', __('Listing has been deleted successfully.'));
        }catch (Exception $exception){
            DB::rollBack();
            return back()->with('error', $exception->getMessage());
        }
    }

    public function listingDeleteMultiple(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select any Item.');
            return response()->json(['error' => 1]);
        } else {
            foreach ($request->strIds as $id) {
                $listing = Listing::with(['get_package', 'listingImages', 'get_listing_amenities', 'get_business_hour', 'get_social_info',
                    'get_products', 'listingSeo', 'get_reviews', 'listingAnalytics', 'listingClaims', 'allWishlists', 'productQueries.replies', 'listingViews'])->findOrFail($id);

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

                $this->fileDelete($listing->thumbnail_driver, $listing->thumbnail);
                $listing->delete();

            }
            session()->flash('success', 'Listing has been Deleted');
            return response()->json(['success' => 1]);
        }
    }


    public function listingReviews($id = null)
    {
        $data['userReviews'] = UserReview::with(['getListing', 'review_user_info'])
            ->where('listing_id', $id)
            ->latest()->get();
        $data['listingId'] = $id;
        $data['listing'] = Listing::select('title')->findOrFail($id);
        return view('admin.listings.reviews', $data);
    }

    public function listingReviewsSearch(Request $request, $id = null)
    {
        $search = $request->search['value']??null;
        $filterUser = $request->filterUser;
        $filterRating = $request->filterRating;
        $filterDate = explode('-', $request->filterDate);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;

        $userReviewList = UserReview::query()->with(['getListing', 'review_user_info'])->where('listing_id', $id)->orderBy('id', 'DESC')
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('rating', 'like', '%' . $search . '%')
                    ->orWhere('review', 'like', '%' . $search . '%')
                    ->orWhereHas('review_user_info', function ($query) use ($search) {
                        return $query->where('firstname', 'LIKE', "%{$search}%")
                            ->orWhere('lastname', 'LIKE', "%{$search}%")
                            ->orWhere('username', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            })
            ->when(isset($filterUser), function ($query) use ($filterUser) {
                if ($filterUser == 'all') {
                    return $query->whereNotNull('user_id');
                }else{
                    return $query->where('user_id', $filterUser);
                }
            })
            ->when(isset($filterRating), function ($query) use ($filterRating) {
                if ($filterRating == 'all') {
                    return $query->whereNotNull('rating');
                }else{
                    return $query->where('rating', $filterRating);
                }
            })
            ->when(!empty($request->filterDate) && $endDate == null, function ($query) use ($startDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $query->whereDate('created_at', $startDate);
            })
            ->when(!empty($request->filterDate) && $endDate != null, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $endDate = Carbon::createFromFormat('d/m/Y', trim($endDate));
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });

        return DataTables::of($userReviewList)
            ->addColumn('checkbox', function ($item) {
                return ' <input type="checkbox" id="chk-' . $item->id . '"
                           class="form-check-input row-tic tic-check" name="check" value="' . $item->id . '"
                           data-id="' . $item->id . '">';
            })
            ->addColumn('user', function ($item) {
                $url = route('admin.user.view.profile', optional($item->review_user_info)->id);
                return '<a class="d-flex align-items-center me-2" href="' . $url . '">
                                <div class="flex-shrink-0">
                                  ' . $item->review_user_info->profilePicture() . '
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <h5 class="text-hover-primary mb-0">' . optional($item->review_user_info)->firstname . ' ' . optional($item->review_user_info)->lastname . '</h5>
                                  <span class="fs-6 text-body">@' . optional($item->review_user_info)->email . '</span>
                                </div>
                              </a>';

            })
            ->addColumn('rating', function ($item) {
                $ratingHtml = '';
                $ratingHtml .= str_repeat('<i class="fas fa-star rating__gold"></i>', $item->rating);
                $ratingHtml .= str_repeat('<i class="far fa-star rating__gold"></i>', 5 - $item->rating);
                return $ratingHtml;
            })
            ->addColumn('review', function ($item) {
                return '<div>
                            <span class="d-block mb-0 ps-2">' .\Illuminate\Support\Str::limit($item->review, 100). '</span>
                        </div>';
            })

            ->addColumn('date-time', function ($item) {
                return dateTime($item->created_at);
            })
            ->addColumn('action', function ($item) {
                $canDelete = adminAccessRoute(config('role.listing_reviews.access.delete'));
                $deleteUrl = route("admin.listing.reviews.delete", $item->id);
                $actions = '';
                if ($canDelete) {
                    $actions .= '<div class="btn-group" role="group">
                          <a class="btn btn-white btn-sm text-danger deleteBtn" href="javascript:void(0)"
                               data-route="' . $deleteUrl . '"
                               data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash dropdown-item-icon text-danger"></i>
                            ' . trans("Delete") . '
                          </a>
                        </div>';
                }
                return $actions ?: '-';

            })->rawColumns(['checkbox', 'user', 'rating', 'review', 'date-time', 'action'])
            ->make(true);
    }

    public function listingReviewsDelete($id)
    {
        try {
            $review = UserReview::findOrFail($id);
            $review->delete();
            return back()->with('success', 'Review has been deleted.');
        }catch (\Exception $exception){
            return back()->with('error', $exception->getMessage());
        }
    }

    public function listingReviewsDeleteMultiple(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select any Item.');
            return response()->json(['error' => 1]);
        } else {
            UserReview::whereIn('id', $request->strIds)->get()->map(function ($review) {
                $review->delete();
            });
            session()->flash('success', 'Listing Review has been Deleted');
            return response()->json(['success' => 1]);
        }
    }


    public function listingAnalytics($id)
    {
        return view('admin.listings.analytics', compact('id'));
    }

    public function listingAnalyticsSearch(Request $request, $id)
    {
        $search = $request->search['value']??null;
        $filterDate = explode('-', $request->filterDate);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;

        $analytics = Analytics::query()->with(['getListing', 'lastVisited'])->withCount('listCount')->where('listing_id', $id)
            ->when(!empty($search), function ($query) use ($search) {
                return $query->whereHas('getListing', function ($query) use ($search) {
                    $query->where('title', 'LIKE', '%' . $search . '%');
                });
            })
            ->when(!empty($request->filterDate) && $endDate == null, function ($query) use ($startDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $query->whereDate('created_at', $startDate);
            })
            ->when(!empty($request->filterDate) && $endDate != null, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $endDate = Carbon::createFromFormat('d/m/Y', trim($endDate));
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });

        return DataTables::of($analytics)
            ->addColumn('checkbox', function ($item) {
                return ' <input type="checkbox" id="chk-' . $item->id . '"
                           class="form-check-input row-tic tic-check" name="check" value="' . $item->id . '"
                           data-id="' . $item->id . '">';
            })
            ->addColumn('listing', function ($item) {
                $url = route('listing.details', optional($item->getListing)->slug);
                return '<a class="d-flex align-items-center me-2" href="' . $url . '" target="_blank">' .\Illuminate\Support\Str::limit(optional($item->getListing)->title, 50).'</a>';

            })
            ->addColumn('country', function ($item) {
                return '<div>
                            <span class="d-block mb-0 ps-2">' .($item->country ?? "N/A"). '</span>
                        </div>';
            })
            ->addColumn('total-visit', function ($item) {
                return $item->list_count_count;
            })
            ->addColumn('last-visited-at', function ($item) {
                return dateTime(optional($item->lastVisited)->created_at);
            })
            ->addColumn('action', function ($item) {
                return '<div class="btn-group" role="group">
                          <a class="btn btn-white btn-sm" href="javascript:void(0)" onclick="analyticsDetails('.$item->id.')">
                            <i class="bi bi-eye dropdown-item-icon text-danger"></i>
                            ' . trans("View") . '
                          </a>
                        </div>';

            })->rawColumns(['checkbox', 'listing', 'country', 'total-visit', 'last-visited-at', 'action'])
            ->make(true);
    }

    public function listingFormData($id = null)
    {
        $data['dynamicFOrmData'] = CollectDynamicFormData::where('listing_id', $id)
            ->latest()->get();
        $data['listing'] = Listing::findOrFail($id);
        return view('admin.listings.dynamic_form_data', $data);
    }

    public function listingFormDataSearch(Request $request, $id = null)
    {
        $search = $request->search['value']??null;
        $filterDate = explode('-', $request->filterDate);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;

        $userReviewList = CollectDynamicFormData::query()->where('listing_id', $id)->orderBy('id', 'DESC')
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('form_name', 'like', '%' . $search . '%');
            })
            ->when(!empty($request->filterDate) && $endDate == null, function ($query) use ($startDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $query->whereDate('created_at', $startDate);
            })
            ->when(!empty($request->filterDate) && $endDate != null, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $endDate = Carbon::createFromFormat('d/m/Y', trim($endDate));
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });

        return DataTables::of($userReviewList)
            ->addColumn('checkbox', function ($item) {
                return ' <input type="checkbox" id="chk-' . $item->id . '"
                           class="form-check-input row-tic tic-check" name="check" value="' . $item->id . '"
                           data-id="' . $item->id . '">';
            })

            ->addColumn('form-name', function ($item) {
                return '<div>
                            <span class="d-block mb-0 ps-2">' .trans($item->form_name). '</span>
                        </div>';
            })

            ->addColumn('date-time', function ($item) {
                return dateTime($item->created_at);
            })
            ->addColumn('action', function ($item) {
                $canDelete = adminAccessRoute(config('role.listing_form.access.delete'));
                $deleteUrl = route("admin.listing.form.data.delete", $item->id);
                $actions = '';
                $actions .= '<div class="btn-group" role="group">
                            <a class="btn btn-white btn-sm show-details-btn" href="javascript:void(0)" data-bs-toggle="modal"
                            data-bs-target="#detailsModal" onclick="viewDetails(' . $item->id . ')">
                                <i class="bi-info-circle me-1"></i>'.trans("Details").'
                            </a>';

                if ($canDelete) {
                    $actions .= '<div class="btn-group">
                                <button type="button" class="btn btn-white btn-icon btn-sm dropdown-toggle dropdown-toggle-empty" id="productsEditDropdown1" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <div class="dropdown-menu dropdown-menu-end mt-1" aria-labelledby="productsEditDropdown1" style="">
                                    <a class="dropdown-item deleteBtn text-danger deleteBtn" href="javascript:void(0)"
                                           data-route="' . $deleteUrl . '"
                                           data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="bi bi-trash dropdown-item-icon text-danger"></i>
                                        ' . trans("Delete") . '
                                      </a>
                                </div>
                            </div>
                        </div>';
                }
                return $actions ?: '-';
            })->rawColumns(['checkbox', 'form-name', 'date-time', 'action'])
            ->make(true);
    }

    public function listingFormDataDetails(Request $request)
    {
        $dynamicFormData = CollectDynamicFormData::findOrFail($request->id);
        return $dynamicFormData;
    }


    public function listingSettings()
    {
        $data['packageExpiryCrons'] = PackageExpiryCron::get();
        return view('admin.listings.listingSettings', $data);
    }

    public function listingSettingsUpdate(Request $request)
    {
        try {
            $data = BasicControl::first();
            $data->listing_approval = $request->listing_approval;
            $data->save();

            $expiryDates = $request->before_expiry_date;

            DB::table('package_expiry_crons')->delete();
            foreach ($expiryDates as $key => $date) {
                $p = new PackageExpiryCron();
                $p->before_expiry_date = $request->before_expiry_date[$key];
                $p->save();
            }
            return back()->with('success', __('Listing Settings Updated!'));
        }catch (\Exception $exception){
            return back()->with('error', $exception->getMessage());
        }
    }

}
