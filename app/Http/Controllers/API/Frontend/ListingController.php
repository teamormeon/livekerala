<?php

namespace App\Http\Controllers\API\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Analytics;
use App\Models\CollectDynamicFormData;
use App\Models\ContactMessage;
use App\Models\Country;
use App\Models\DynamicForm;
use App\Models\Follower;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Models\Page;
use App\Models\User;
use App\Models\UserReview;
use App\Models\Viewer;
use App\Traits\ApiResponse;
use App\Traits\Notify;
use App\Traits\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ListingController extends Controller
{
    use ApiResponse, Notify, Upload;

    public function listings(Request $request, $cat_id = null)
    {
        $search = $request->all();
        $categoryIds = $request->category;
        $today = Carbon::now()->format('Y-m-d');

        $listings = Listing::with(['get_package:id,expire_date','get_user:id,firstname,lastname,username','get_reviews:id,listing_id,user_id,rating'])
            ->select('id','user_id','category_id','purchase_package_id','country_id','state_id','city_id','title','slug','address','lat','long','thumbnail','thumbnail_driver','status','is_active','created_at')
            ->when(isset($categoryIds) && !in_array('all', $categoryIds), function ($query) use ($categoryIds) {
                $query->where(function ($query) use ($categoryIds) {
                    $query->whereJsonContains('category_id', $categoryIds[0]);
                    foreach (array_slice($categoryIds, 1) as $category_id) {
                        $query->orWhereJsonContains('category_id', $category_id);
                    }
                });
            })
            ->when(isset($cat_id), function ($query) use ($cat_id) {
                return $query->whereJsonContains('category_id', $cat_id);
            })
            ->when(isset($search['search']), function ($query) use ($search) {
                return $query->where('title', 'LIKE', "%{$search['search']}%");
            })
            ->when(isset($search['country_id']), function ($query2) use ($search) {
                return $query2->whereHas('get_place', function ($q) use ($search) {
                    $q->where('id', $search['country_id']);
                });
            })
            ->when(isset($search['city_id']), function ($query4) use ($search) {
                return $query4->where('city_id', $search['city_id']);
            })
            ->when(isset($search['user_id']), function ($query4) use ($search) {
                return $query4->where('user_id', $search['user_id']);
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
            ->latest()
            ->paginate(basicControl()->paginate);

        $formatedListing = $listings->getCollection()->map(function ($listing) {
            return [
                'id' => $listing->id,
                'user_id' => $listing->user_id,
                'purchase_package_id' => $listing->purchase_package_id,
                'country_id' => $listing->country_id,
                'state_id' => $listing->state_id,
                'city_id' => $listing->city_id,
                'title' => html_entity_decode($listing->title),
                'slug' => $listing->slug,
                'categories' => html_entity_decode($listing->getCategoriesName()),
                'address' => $listing->get_cities ? $listing->get_cities->getAddress() : '',
                'lat' => $listing->lat,
                'long' => $listing->long,
                'thumbnail' => getFile($listing->thumbnail_driver, $listing->thumbnail),
                'status' => $listing->status,
                'is_active' => $listing->is_active,
                'favourite_count' => $listing->get_favourite_count >= 1 ? 1 : 0,
                'average_rating' => $listing->average_rating,
                'created_at' => $listing->created_at,
                'user' => [
                    'firstname' => $listing->get_user->firstname,
                    'lastname' => $listing->get_user->lastname,
                    'username' => $listing->get_user->username,
                ],
            ];
        });
        $listings->setCollection($formatedListing);

        $info = [
            'status' => '0 = Pending, 1 = Approved, 2 = Rejected',
            'is_active' => '0 = Deactive, 1 = Active',
        ];
        return response()->json($this->withSuccess($listings, $info));
    }
    public function withoutAuthListings(Request $request, $cat_id = null)
    {
        $search = $request->all();
        $categoryIds = $request->category;
        $today = Carbon::now()->format('Y-m-d');

        $listings = Listing::with(['get_package:id,expire_date','get_user:id,firstname,lastname,username','get_reviews:id,listing_id,user_id,rating'])
            ->select('id','user_id','category_id','purchase_package_id','country_id','state_id','city_id','title','slug','address','lat','long','thumbnail','thumbnail_driver','status','is_active','created_at')
            ->when(isset($categoryIds) && !in_array('all', $categoryIds), function ($query) use ($categoryIds) {
                $query->where(function ($query) use ($categoryIds) {
                    $query->whereJsonContains('category_id', $categoryIds[0]);
                    foreach (array_slice($categoryIds, 1) as $category_id) {
                        $query->orWhereJsonContains('category_id', $category_id);
                    }
                });
            })
            ->when(isset($cat_id), function ($query) use ($cat_id) {
                return $query->whereJsonContains('category_id', $cat_id);
            })
            ->when(isset($search['search']), function ($query) use ($search) {
                return $query->where('title', 'LIKE', "%{$search['search']}%");
            })
            ->when(isset($search['country_id']), function ($query2) use ($search) {
                return $query2->whereHas('get_place', function ($q) use ($search) {
                    $q->where('id', $search['country_id']);
                });
            })
            ->when(isset($search['city_id']), function ($query4) use ($search) {
                return $query4->where('city_id', $search['city_id']);
            })
            ->when(isset($search['user_id']), function ($query4) use ($search) {
                return $query4->where('user_id', $search['user_id']);
            })
            ->when(!empty($search['rating']), function ($query5) use ($search) {
                return $query5->whereHas('get_reviews', function ($q) use ($search) {
                    $q->whereIn('rating', $search['rating']);
                });
            })
            ->withCount(['get_reviews as average_rating' => function ($query) {
                $query->select(DB::raw('coalesce(avg(rating),0)'));
            }])
            ->whereHas('get_package', function ($query5) use ($today) {
                return $query5->where('expire_date', '>=', $today)->orWhereNull('expire_date');
            })
            ->where('status', 1)
            ->where('is_active', 1)
            ->latest()
            ->paginate(basicControl()->paginate);

        $formatedListing = $listings->getCollection()->map(function ($listing) {
            return [
                'id' => $listing->id,
                'user_id' => $listing->user_id,
                'purchase_package_id' => $listing->purchase_package_id,
                'country_id' => $listing->country_id,
                'state_id' => $listing->state_id,
                'city_id' => $listing->city_id,
                'title' => html_entity_decode($listing->title),
                'slug' => $listing->slug,
                'categories' => html_entity_decode($listing->getCategoriesName()),
                'address' => $listing->get_cities ? $listing->get_cities->getAddress() : '',
                'lat' => $listing->lat,
                'long' => $listing->long,
                'thumbnail' => getFile($listing->thumbnail_driver, $listing->thumbnail),
                'status' => $listing->status,
                'is_active' => $listing->is_active,
                'average_rating' => $listing->average_rating,
                'created_at' => $listing->created_at,
                'user' => [
                    'firstname' => $listing->get_user->firstname,
                    'lastname' => $listing->get_user->lastname,
                    'username' => $listing->get_user->username,
                ],
            ];
        });
        $listings->setCollection($formatedListing);

        $info = [
            'status' => '0 = Pending, 1 = Approved, 2 = Rejected',
            'is_active' => '0 = Deactive, 1 = Active',
        ];
        return response()->json($this->withSuccess($listings, $info));
    }

    public function listingDetails($slug)
    {
        $listing_details = Listing::with(['get_package:id,package_id,is_video,is_product,is_whatsapp,is_messenger',
            'get_user:id,firstname,lastname,username,website,language_id,email,country_code,country,phone_code,phone,cover_image,cover_image_driver,image,image_driver,address_one,address_two,bio,created_at',
            'get_user.get_social_links_user:id,user_id,social_icon,social_url',
            'get_listing_images:id,listing_id,listing_image,driver',
            'get_listing_amenities:id,listing_id,amenity_id',
            'get_listing_amenities.get_amenity:id,icon,status',
            'get_listing_amenities.get_amenity.details:id,amenity_id,language_id,title',
            'get_products:id,user_id,listing_id,product_title,product_price,product_description,product_thumbnail,driver,created_at',
            'get_products.get_product_image:id,product_id,product_image,driver',
            'get_business_hour:id,listing_id,working_day,start_time,end_time',
            'get_social_info:id,listing_id,social_icon,social_url',
            'get_reviews:id,listing_id,user_id,rating,review,created_at',
            'get_reviews.review_user_info:id,username,image,image_driver',
            'form:id,user_id,listing_id,name,button_text,input_form,status'])
            ->select('id','user_id','category_id','purchase_package_id','country_id','state_id','city_id','title','slug','email','phone','description','address','lat','long',
                'youtube_video_id','thumbnail','thumbnail_driver','status','is_active','whatsapp_number','replies_text','body_text','created_at')
            ->where('status', 1)
            ->where('slug', $slug)
            ->first();

        if (!$listing_details) {
            return response()->json($this->withError('Listing not found'));
        }

        $user_id = $listing_details->user_id;
        $follower_count = collect(Follower::selectRaw("COUNT((CASE WHEN user_id = $user_id  THEN id END)) AS totalFollower")
            ->get()->toArray())->collapse();

        $total_listings_an_user = collect(Listing::selectRaw("COUNT((CASE WHEN user_id = $user_id  THEN id END)) AS totalListing")
            ->get()->makeHidden('avgRating')->toArray())->collapse();

        $total_listing_view = Viewer::where('listing_id', $listing_details->id)->count();

        if (Auth::check()) {
            $reviewDone = UserReview::where('listing_id', $listing_details->id)->where('user_id', Auth::user()->id)->count();
        } else {
            $reviewDone = 0;
        }

        $formated_listing_details = [
            'id' => $listing_details->id,
            'user_id' => $listing_details->user_id,
            'current_url' => route('listing.details', $listing_details->slug),
            'categories' => html_entity_decode($listing_details->getCategoriesName()),
            'purchase_package_id' => $listing_details->purchase_package_id,
            'country_id' => $listing_details->country_id,
            'state_id' => $listing_details->state_id,
            'city_id' => $listing_details->city_id,
            'title' => html_entity_decode($listing_details->title),
            'slug' => $listing_details->slug,
            'email' => $listing_details->email,
            'phone' => $listing_details->phone,
            'description' => strip_tags($listing_details->description),
            'address' => $listing_details->get_cities->getAddress(),
            'lat' => $listing_details->lat,
            'long' => $listing_details->long,
            'youtube_video_id' => $listing_details->youtube_video_id,
            'thumbnail' => getFile($listing_details->thumbnail_driver, $listing_details->thumbnail),
            'status' => $listing_details->status,
            'total_listing_views' => $total_listing_view,
            'reviewDone' => $reviewDone,
            'is_active' => $listing_details->is_active,
            'whatsapp_number' => $listing_details->whatsapp_number,
            'replies_text' => $listing_details->replies_text,
            'body_text' => $listing_details->body_text,
            'created_at' => $listing_details->created_at,
            'average_rating' => $listing_details->avgRating,
            'listing_images' => $listing_details->get_listing_images->map(function ($item) {
                return [
                    'id' => $item->id,
                    'listing_image' => getFile($item->driver, $item->listing_image),
                ];
            }),
            'listing_amenities' => $listing_details->get_listing_amenities->map(function ($item) {
                return [
                    'id' => $item->id,
                    'amenity_id' => $item->amenity_id,
                    'title' => optional(optional($item->get_amenity)->details)->title,
                    'icon' => optional($item->get_amenity)->icon,
                    'status' => optional($item->get_amenity)->status,
                ];
            }),
            'get_products' => $listing_details->get_products->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_title' => html_entity_decode($item->product_title),
                    'product_price' => $item->product_price,
                    'product_description' => strip_tags($item->product_description),
                    'product_thumbnail' => getFile($item->driver, $item->product_thumbnail),
                    'product_image' => $item->get_product_image->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product_image' => getFile($item->driver, $item->product_image),
                        ];
                    }),
                ];
            }),
            'get_business_hour' => $listing_details->get_business_hour->map(function ($item) {
                return [
                    'id' => $item->id,
                    'working_day' => $item->working_day,
                    'start_time' => $item->start_time,
                    'end_time' => $item->end_time,
                ];
            }),
            'get_social_info' => $listing_details->get_social_info->map(function ($item) {
                return [
                    'id' => $item->id,
                    'social_icon' => $item->social_icon,
                    'social_url' => $item->social_url
                ];
            }),
            'get_reviews' => $listing_details->get_reviews->map(function ($item) {
                return [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'username' => $item->review_user_info->username,
                    'image' => getFile($item->review_user_info->image_driver, $item->review_user_info->image),
                    'rating' => $item->rating,
                    'review' => $item->review,
                    'created_at' => $item->created_at,
                ];
            }),
            'dynamic_form' => $listing_details->form,
            'package' => $listing_details->get_package,
            'user' => [
                'id' => $listing_details->get_user->id,
                'firstname' => $listing_details->get_user->firstname,
                'lastname' => $listing_details->get_user->lastname,
                'username' => $listing_details->get_user->username,
                'website' => $listing_details->get_user->website,
                'language_id' => $listing_details->get_user->language_id,
                'email' => $listing_details->get_user->email,
                'country_code' => $listing_details->get_user->country_code,
                'country' => $listing_details->get_user->country,
                'phone_code' => $listing_details->get_user->phone_code,
                'phone' => $listing_details->get_user->phone,
                'cover_image' => getFile($listing_details->get_user->cover_image_driver, $listing_details->get_user->cover_image),
                'image' => getFile($listing_details->get_user->image_driver, $listing_details->get_user->image),
                'address_one' => $listing_details->get_user->address_one,
                'address_two' => $listing_details->get_user->address_two,
                'bio' => $listing_details->get_user->bio,
                'follower_count' => $follower_count['totalFollower'],
                'total_listings_an_user' => $total_listings_an_user['totalListing'],
                'social_links' => $listing_details->get_user->get_social_links_user,
                'created_at' => $listing_details->get_user->created_at,
            ],
        ];

        $viewer_ip = request()->ip();
        $viewer = new Viewer();
        $viewer->user_id = $user_id;
        $viewer->listing_id = $listing_details->id;
        $viewer->viewer_ip = $viewer_ip;
        $viewer->save();

        $browserInfo = getIpInfo($viewer_ip);
        $listingAnalytics = new Analytics();
        $listingAnalytics->listing_owner_id = $listing_details->user_id;
        $listingAnalytics->listing_id = $listing_details->id;
        $listingAnalytics->visitor_ip = $viewer_ip;

        $listingAnalytics->country = empty($browserInfo['country']) ? null : $browserInfo['country'];
        $listingAnalytics->city = empty($browserInfo['city']) ? null : $browserInfo['city'];
        $listingAnalytics->code = empty($browserInfo['code']) ? null : $browserInfo['code'];
        $listingAnalytics->lat = empty($browserInfo['lat']) ? null : $browserInfo['lat'];
        $listingAnalytics->long = empty($browserInfo['long']) ? null : $browserInfo['long'];
        $listingAnalytics->os_platform = $browserInfo['os_platform'] ?? null;
        $listingAnalytics->browser = $browserInfo['browser'] ?? null;
        $listingAnalytics->save();

        $info = [
            'status' => '0 = Pending, 1 = Approved, 2 = Rejected',
            'is_active' => '0 = Deactive, 1 = Active',
            'reviewDone' => 'reviewDone = 0 & $listing_details->user_id != Auth::id() then review submit form will be show, otherwise review submit form will be hide',
        ];
        return response()->json($this->withSuccess($formated_listing_details, $info));

    }

    public function sendListingMessage(Request $request, $id)
    {
        $rules = [
            'name' => 'required|max:50',
            'message' => 'required',
        ];
        $message = [
            'name.required' => __('Please write your name'),
            'message.required' => __('Please Write your message'),
        ];

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return response()->json($this->withError(collect($validator->errors())->collapse()));
        }

        $listing = Listing::with('get_user')->select('id','user_id')->find($id);
        if (!$listing){
            return response()->json($this->withError('Listing could not be found.'));
        }

        $user = $listing->get_user;
        $senderName = Auth::user()->firstname . ' ' . Auth::user()->lastname;

        $contactMessage = new ContactMessage();
        $contactMessage->user_id = $user->id;
        $contactMessage->client_id = Auth::user()->id;
        $contactMessage->listing_id = $id;
        $contactMessage->message = $request->message;
        $contactMessage->save();

        $msg = [
            'from' => $senderName ?? null,
        ];

        $userAction = [
            "link" => route('profile', $user->username),
            "icon" => "fa fa-money-bill-alt text-white"
        ];

        $adminAction = [
            "link" => route('admin.contact.message'),
            "icon" => "fa fa-money-bill-alt text-white"
        ];

        $this->userPushNotification($user, 'VIEWER_MESSAGE_TO_USER', $msg, $userAction);
        $this->sendMailSms($user, 'VIEWER_MESSAGE_TO_USER');
        $this->adminPushNotification( 'VIEWER_MESSAGE_TO_ADMIN', $msg, $adminAction);
        return response()->json($this->withSuccess('Message sent Successfully.'));
    }

    public function collectListingFormData(Request $request)
    {
        $dynamicForm = DynamicForm::find($request->dynamic_forms_id);
        if (!$dynamicForm){
            return response()->json($this->withError('Dynamic form could not be found.'));
        }
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
            return response()->json($this->withError(collect($validator->errors())->collapse()));
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
                            return response()->json($this->withError($exp->getMessage()));
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
        return response()->json($this->withSuccess('Data submit Successfully.'));
    }

    public function reviewPush(Request $request)
    {
        $rules = [
            'listing_id' => 'required',
            'rating' => 'required|in:0,1,2,3,4,5',
            'review' => 'required|string|max:500',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($this->withError(collect($validator->errors())->collapse()));
        }
        $listing = Listing::select('id','user_id')->find($request->listing_id);
        if (auth()->id() == $listing->user_id) {
            return response()->json($this->withError('You can not review own listing' ));
        }

        $review = new UserReview();
        $review->listing_id = $request->listing_id;
        $review->user_id = auth()->id();
        $review->rating = $request->rating;
        $review->review = $request->review;
        $review->save();
        return response()->json($this->withSuccess('Review submitted successfully'));
    }

    public function listingAuthorProfile($user_name = null)
    {
        $user_information = User::with(['follower','follower.get_follwer_user:id,firstname,lastname,username,image,image_driver,cover_image,cover_image_driver,created_at',
            'following.get_following_user:id,firstname,lastname,username,image,image_driver,cover_image,cover_image_driver,created_at',
            'get_social_links_user:id,user_id,social_icon,social_url'])
            ->select('id','firstname','lastname','username','website','email','image','image_driver','cover_image','cover_image_driver','address_one','address_two','bio','status','created_at')
            ->withCount('get_listing')
            ->withCount('totalViews')
            ->withCount('follower')
            ->withCount('following')
            ->where('username', $user_name)
            ->first();
        if (!$user_information){
            return response()->json($this->withError('User does not exist'));
        }

        if (Auth::check()) {
            $check_follower = Follower::whereHas('get_follwer_user')->select('id','user_id','following_id')->where('user_id', $user_information->id)->where('following_id', Auth::user()->id)->count();
        } else {
            $check_follower = Follower::whereHas('get_follwer_user')->select('id','user_id','following_id')->where('user_id', $user_information->id)->count();
        }

        $today = Carbon::now()->format('Y-m-d');
        $latest_listings = Listing::with(['get_package:id,user_id,package_id,expire_date'])
            ->select('id','user_id','category_id','purchase_package_id','country_id','state_id','city_id','title','slug','address','thumbnail','thumbnail_driver','status','is_active','created_at')
            ->whereHas('get_package', function ($query5) use ($today) {
                return $query5->where('expire_date', '>=', $today)->orWhereNull('expire_date');
            })
            ->where('user_id', $user_information->id)
            ->withCount('getFavourite')
            ->withCount('get_reviews')
            ->where('status', 1)
            ->where('is_active', 1)
            ->latest()->paginate(config('basic.paginate'));

        $formated_latest_listings = $latest_listings->getCollection()->map(function ($listing) {
            return [
                'id' => $listing->id,
                'user_id' => $listing->user_id,
                'purchase_package_id' => $listing->purchase_package_id,
                'country_id' => $listing->country_id,
                'state_id' => $listing->state_id,
                'city_id' => $listing->city_id,
                'title' => html_entity_decode($listing->title),
                'slug' => $listing->slug,
                'categories' => html_entity_decode($listing->getCategoriesName()),
                'address' => $listing->get_cities->getAddress() ?? $listing->address,
                'thumbnail' => getFile($listing->thumbnail_driver, $listing->thumbnail),
                'status' => $listing->status,
                'is_active' => $listing->is_active,
                'favourite_count' => $listing->get_favourite_count >= 1 ? 1 : 0,
                'average_rating' => $listing->avgRating ?? 0,
                'created_at' => $listing->created_at,
            ];
        });

        $latest_listings->setCollection($formated_latest_listings);

        $formated_user_information = [
            'id' => $user_information->id,
            'firstname' => $user_information->firstname,
            'lastname' => $user_information->lastname,
            'username' => $user_information->username,
            'bio' => $user_information->bio,
            'website' => $user_information->website,
            'email' => $user_information->email,
            'cover_image' => getFile($user_information->cover_image_driver, $user_information->cover_image),
            'image' => getFile($user_information->image_driver, $user_information->image),
            'address' => !empty($user_information->address_one) ? $user_information->fullAddress : null,
            'status' => $user_information->status,
            'check_following' => $check_follower,
            'created_at' => $user_information->created_at,
            'total_listing' => $user_information->get_listing_count,
            'total_views' => $user_information->total_views_count,
            'total_follower' => $user_information->follower_count,
            'total_following' => $user_information->following_count,
            'follower_info' => $user_information->follower->map(function ($info) {
                return [
                    'id' => $info->id,
                    'user_id' => $info->user_id,
                    'following_id' => $info->following_id,
                    'firstname' => optional($info->get_follwer_user)->firstname??'Unknown',
                    'lastname' => optional($info->get_follwer_user)->lastname??'',
                    'username' => optional($info->get_follwer_user)->username??'Unknown',
                    'cover_image' => getFile(optional($info->get_follwer_user)->cover_image_driver??null, optional($info->get_follwer_user)->cover_image??null),
                    'image' => getFile(optional($info->get_follwer_user)->image_driver??null, optional($info->get_follwer_user)->image??null),
                    'created_at' => $info->created_at,
                ];
            }),
            'following_info' => $user_information->following->map(function ($info) {
                return [
                    'id' => $info->id,
                    'user_id' => $info->user_id,
                    'following_id' => $info->following_id,
                    'firstname' => $info->get_following_user->firstname,
                    'lastname' => $info->get_following_user->lastname,
                    'username' => $info->get_following_user->username,
                    'cover_image' => getFile($info->get_following_user->cover_image_driver, $info->get_following_user->cover_image),
                    'image' => getFile($info->get_following_user->image_driver, $info->get_following_user->image),
                    'created_at' => $info->created_at,
                ];
            }),
            'social_links' => $user_information->get_social_links_user->map(function ($item) {
                return [
                    'social_icon' => $item->social_icon,
                    'social_url' => $item->social_url,
                ];
            }),
            'latest_listings' => $latest_listings,
        ];

        $info = [
            'check_following' => 'if check_following = 1 its mean user already following otherwize not following'
        ];
        return response()->json($this->withSuccess($formated_user_information, $info));
    }

    public function authorProfileFollowOrUnfollow(Request $request, $user_id)
    {
        if ($user_id != auth()->id()){
            $existingFollow = Follower::select('id','user_id','following_id')->where('user_id', $user_id)
                ->where('following_id', auth()->id())
                ->first();

            if ($existingFollow) {
                $existingFollow->delete();
                return response()->json($this->withSuccess('Unfollowed'));
            } else {
                Follower::create([
                    'user_id' => $user_id,
                    'following_id' => auth()->id(),
                    'created_at' => Carbon::now(),
                ]);
                return response()->json($this->withSuccess('Followed'));
            }
        }else{
            return response()->json($this->withError('You can\'t Follow own Profile'));
        }
    }

    public function sendMessageToListingAuthor(Request $request, $user_id)
    {
        $rules = [
            'name' => 'required|max:50',
            'message' => 'required',
        ];
        $message = [
            'name.required' => __('Please write your name'),
            'message.required' => __('Please Write your message'),
        ];
        $validate = Validator::make($request->all(), $rules, $message);
        if ($validate->fails()) {
            return response()->json($this->withError(collect($validate->errors())->collapse()));
        }

        $user = User::find($user_id);
        if (!$user){
            return response()->json($this->withError('User does not exist'));
        }

        $senderName = auth()->user()->firstname.' '.auth()->user()->lastname;

        $contact_message = new ContactMessage();
        $contact_message->user_id = $user_id;
        $contact_message->client_id = auth()->id();
        $contact_message->message = $request->message;
        $contact_message->save();

        $msg = [
            'from' => $senderName ?? null,
        ];

        $userAction = [
            "link" => route('profile', $user->username),
            "icon" => "fa fa-money-bill-alt text-white"
        ];

        $adminAction = [
            "link" => route('admin.contact.message'),
            "icon" => "fa fa-money-bill-alt text-white"
        ];

        $this->userPushNotification($user, 'VIEWER_MESSAGE_TO_USER', $msg, $userAction);
        $this->sendMailSms($user, 'VIEWER_MESSAGE_TO_USER');
        $this->adminPushNotification( 'VIEWER_MESSAGE_TO_ADMIN', $msg, $adminAction);
        return response()->json($this->withSuccess('Message sent'));
    }


}
