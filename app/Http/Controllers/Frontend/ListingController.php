<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\UserSystemInfo;
use App\Http\Controllers\Controller;
use App\Models\Analytics;
use App\Models\CollectDynamicFormData;
use App\Models\Country;
use App\Models\DynamicForm;
use App\Models\Follower;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Models\Page;
use App\Models\User;
use App\Models\UserReview;
use App\Models\Viewer;
use App\Traits\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ListingController extends Controller
{
    use Upload;
    public function __construct()
    {
        $this->theme = template();
    }

    public function listings(Request $request, $id = null, $type = null)
    {
        $selectedTheme = getTheme();
        $today = Carbon::now()->format('Y-m-d');
        $search = $request->all();
        $categoryIds = $request->category;

        $data['all_listings'] = Listing::with(['get_user', 'get_place', 'get_reviews', 'get_package', 'listingSeo'])
            ->when(isset($categoryIds) && !in_array('all', $categoryIds), function ($query) use ($categoryIds) {
                $query->where(function ($query) use ($categoryIds) {
                    $query->whereJsonContains('category_id', $categoryIds[0]);
                    foreach (array_slice($categoryIds, 1) as $category_id) {
                        $query->orWhereJsonContains('category_id', $category_id);
                    }
                });
            })
            ->when(isset($id), function ($query) use ($id) {
                return $query->whereJsonContains('category_id', $id);
            })
            ->when(isset($search['name']), function ($query) use ($search) {
                return $query->where('title', 'LIKE', "%{$search['name']}%")
                    ->orWhere('description', 'LIKE', "%{$search['name']}%")
                    ->orWhereHas('listingSeo', function ($tQuery) use ($search) {
                        $tQuery->where('meta_keywords', 'LIKE', "%{$search['name']}%");
                    });
            })
            ->when(isset($search['location']) && $search['location'] != 'all', function ($query2) use ($search) {
                return $query2->whereHas('get_place', function ($q) use ($search) {
                    $q->where('id', $search['location']);
                });
            })
            ->when(isset($search['city']) && $search['city'] != 'all', function ($query4) use ($search) {
                return $query4->where('city_id', $search['city']);
            })
            ->when(isset($search['user']) && $search['user'] != 'all', function ($query4) use ($search) {
                return $query4->where('user_id', $search['user']);
            })
            ->when(!empty($search['rating']), function ($query5) use ($search) {
                return $query5->whereHas('get_reviews', function ($q) use ($search) {
                    $q->whereIn('rating', $search['rating']);
                });
            })
            ->withCount(['getFavourite', 'get_reviews as average_rating' => function ($query) {
                $query->select(DB::raw('coalesce(avg(rating),0)'));
            }])
            ->whereHas('get_package', function ($query5) use ($today) {
                return $query5->where('expire_date', '>=', $today)->orWhereNull('expire_date');
            })
            ->where('status', 1)
            ->where('is_active', 1)
            ->when($request->exists('popular') == true, function ($query5) use ($search) {
                $query5->orderByDesc('average_rating');
            })
            ->when($request->exists('popular') == false, function ($query5) use ($search) {
                return $query5->orderBy('id', 'desc');
            })
            ->paginate(6);


        $data['all_places'] = Country::select('id', 'name')->where('status', 1)->orderBy('name', 'ASC')->toBase()->get();
        $data['uniqueCities'] = Listing::with('get_cities')->where('city_id', '!=', null)->get()->pluck('get_cities');
        $data['all_categories'] = ListingCategory::select('id')->with('details:id,listing_category_id,name')->where('status', 1)->latest()->get();
        $pageSeo = Page::where('template_name', $selectedTheme)->where('slug', 'listings')->first();
        $pageSeo['breadcrumb_image'] = $pageSeo?->breadcrumb_status == 1 ?  getFile($pageSeo->breadcrumb_image_driver, $pageSeo->breadcrumb_image) : null;
        return view(template() . 'frontend.listing.index', $data, compact('pageSeo'));
    }

    public function listingDetails($slug)
    {
        $selectedTheme = getTheme();
        $single_listing_details = Listing::with(['get_package',
            'get_user', 'get_user.get_social_links_user',
            'get_listing_images', 'get_listing_amenities.get_amenity.details',
            'get_products.get_product_image', 'get_business_hour', 'get_social_info', 'get_reviews', 'listingSeo','form'])
            ->where('status', 1)
            ->where('slug', $slug)
            ->firstOrFail();

        $ratingProgressCounts = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];
        foreach ($single_listing_details->get_reviews as $review) {
            if (isset($ratingProgressCounts[$review->rating])) {
                $ratingProgressCounts[$review->rating]++;
            }
        }

        $data['ratingProgressCounts'] = $ratingProgressCounts;
        $data['single_listing_details'] = $single_listing_details;
        $data['average_review'] = $single_listing_details->reviews()[0]->average;

        $user_id = $single_listing_details->user_id;
        $data['follower_count'] = collect(Follower::selectRaw("COUNT((CASE WHEN user_id = $user_id  THEN id END)) AS totalFollower")
            ->get()->toArray())->collapse();

        $data['total_listings_an_user'] = collect(Listing::selectRaw("COUNT((CASE WHEN user_id = $user_id  THEN id END)) AS totalListing")
            ->get()->makeHidden('avgRating')->toArray())->collapse();

        $data['category_wise_listing'] = Listing::with('get_user')
            ->where([
                'user_id' => $single_listing_details->user_id,
                'category_id' => $single_listing_details->category_id,
                'status' => 1
            ])
            ->where('id', '!=', $single_listing_details->id)
            ->withCount('getFavourite')->limit(3)->inRandomOrder()->latest()->get();

        $viewer_ip = $_SERVER['REMOTE_ADDR'];
        $viewer = new Viewer();
        $viewer->user_id = $user_id;
        $viewer->listing_id = $single_listing_details->id;
        $viewer->viewer_ip = $viewer_ip;
        $viewer->save();
        $data['total_listing_view'] = Viewer::where('listing_id', $single_listing_details->id)->count();
        if (Auth::check()) {
            $data['reviewDone'] = UserReview::where('listing_id', $single_listing_details->id)->where('user_id', Auth::user()->id)->count();
        } else {
            $data['reviewDone'] = '0';
        }

        $browserInfo = getIpInfo($viewer_ip);

        $listingAnalytics = new Analytics();
        $listingAnalytics->listing_owner_id = $single_listing_details->user_id;
        $listingAnalytics->listing_id = $single_listing_details->id;
        $listingAnalytics->visitor_ip = $viewer_ip;

        $listingAnalytics->country = empty($browserInfo['country']) ? null : $browserInfo['country'];
        $listingAnalytics->city = empty($browserInfo['city']) ? null : $browserInfo['city'];
        $listingAnalytics->code = empty($browserInfo['code']) ? null : $browserInfo['code'];
        $listingAnalytics->lat = empty($browserInfo['lat']) ? null : $browserInfo['lat'];
        $listingAnalytics->long = empty($browserInfo['long']) ? null : $browserInfo['long'];
        $listingAnalytics->os_platform =  UserSystemInfo::get_os();
        $listingAnalytics->browser = UserSystemInfo::get_browsers();
        $listingAnalytics->device_name = UserSystemInfo::get_device();
        $listingAnalytics->save();

        $pageSeo = Page::where('template_name', $selectedTheme)->where('slug', 'listing-details')->first();
        $pageSeo['breadcrumb_image'] = $pageSeo?->breadcrumb_status == 1 ?  getFile($pageSeo->breadcrumb_image_driver, $pageSeo->breadcrumb_image) : null;
        return view(template() . 'frontend.listing.listing_details', $data, compact('pageSeo'));
    }

    public function listingReviewsGet($id)
    {
        $data = UserReview::with('review_user_info')->where('listing_id', $id)->latest()->paginate(basicControl()->paginate);
        return response([
            'data' => $data
        ]);
    }


    public function collectListingFormData(Request $request)
    {
        $dynamicForm = DynamicForm::findOrFail($request->dynamic_forms_id);
        $params = $dynamicForm->input_form;
        $reqData = $request->except('_token', '_method','dynamic_forms_id', 'listing_id');
        $rules = [];
        $customMessages = [];

        if ($params !== null) {
            foreach ($params as $key => $cus) {
                $fieldRule = $cus->validation == 'required' ? 'required' : 'nullable';
                if ($cus->type == 'text') {
                    $fieldRule .= '|max:191';
                } elseif ($cus->type == 'number') {
                    $fieldRule .= '|integer';
                } elseif ($cus->type == 'textarea') {
                    $fieldRule .= '|min:3|max:300';
                } elseif ($cus->type == 'select') {
                    $fieldRule .= '|required';
                } elseif ($cus->type == 'file') {
                    $fieldRule .= '|max:4048';
                }
                $rules['key'.$key] = $fieldRule;

                $customMessages['key'.$key.'.required'] = "The {$cus->field_name} field is required.";
                $customMessages['key'.$key.'.max'] = "The {$cus->field_name} may not be greater than :max characters.";
                $customMessages['key'.$key.'.min'] = "The {$cus->field_name} must be at least :min characters.";
                $customMessages['key'.$key.'.integer'] = "The {$cus->field_name} must be a valid number.";
                $customMessages['key'.$key.'.nullable'] = "The {$cus->field_name} can be left empty, but if filled, it must be valid.";
                $customMessages['key'.$key.'.file'] = "The {$cus->field_name} must be a valid file.";
            }
        }

        foreach ($reqData as $index => $value) {
            $transformedData['key' .$index] = $value;
        }

        $validator = Validator::make($transformedData, $rules, $customMessages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $reqField = [];
        foreach ($reqData as $k => $v) {
            foreach ($params as $inKey => $inVal) {
                if ($k == $inKey) {
                    if ($inVal->type == 'file' && $request->hasFile($inKey)) {
                        try {
                            $file = $this->fileUpload($request[$inKey], config('filelocation.dynamicFormData.path'), null, null, 'webp', 99);
                            $reqField[$inKey] = [
                                'field_name' => $inVal->field_name,
                                'field_value' => $file['path'],
                                'field_driver' => $file['driver'],
                                'validation' => $inVal->validation,
                                'type' => $inVal->type,
                            ];
                        } catch (\Exception $exp) {
                            session()->flash('error', 'Could not upload your try later');
                            return back()->withInput();
                        }
                    } else {
                        $reqField[$inKey] = [
                            'field_name' => $inVal->field_name,
                            'field_value' => $v,
                            'validation' => $inVal->validation,
                            'type' => $inVal->type,
                        ];
                    }
                }
            }
        }

        $user = Auth::user();
        CollectDynamicFormData::create([
            'user_id' => $user->id,
            'dynamic_forms_id' => $dynamicForm->id,
            'listing_id' => $request->listing_id,
            'form_name' => $dynamicForm->name,
            'input_form' => $reqField
        ]);
        return back()->with('success', $dynamicForm->name.' submit successfully');
    }

}
