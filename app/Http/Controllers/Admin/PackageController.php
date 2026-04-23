<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configure;
use App\Models\Gateway;
use App\Models\Language;
use App\Models\Package;
use App\Models\PackageDetails;
use App\Models\packages;
use App\Models\Transaction;
use App\Traits\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PackageController extends Controller
{
    use Upload;

    public function package()
    {
        return view('admin.package.index');
    }

    public function packageSearch(Request $request)
    {
        $search = $request->search['value']??null;

        $Packages = Package::query()->with(['details'])
            ->orderBy('price', 'ASC')
            ->when(!empty($search), function ($query) use ($search) {
                return $query->whereHas('details', function ($subquery) use ($search) {
                    $subquery->where('title', 'LIKE', "%$search%")
                        ->where('language_id', 1);
                });
            });

        return DataTables::of($Packages)
            ->addColumn('no', function ($item) {
                static $counter = 0;
                $counter++;
                return $counter;
            })
            ->addColumn('package-name', function ($item) {
                $title = $item->details['title'] ?? 'unknown';
                return '<div class="d-flex align-items-center">
                            <div class="avatar avatar-sm avatar-circle">
                                <img class="avatar-img" src="' . getFile($item->driver, $item->image) . '" alt="image">
                            </div>
                            <span class="d-block mb-0 ps-3">' . $title . '</span>
                        </div>';
            })
            ->addColumn('price', function ($item) {
                return currencyPosition($item->price);
            })
            ->addColumn('expiry-time', function ($item) {
                if($item->expiry_time == null){
                    return '<span class="badge bg-soft-secondary text-secondary"><span class="legend-indicator bg-secondary "></span>' . trans('Unlimited') . '</span>';
                }
                return  $item->expiry_time . ' ' . $item->expiry_time_type;
            })
            ->addColumn('status', function ($item) {
                $badgeClass = $item->status == 1 ? 'success text-success' : 'danger text-danger';
                $legendBgClass = $item->status == 1 ? 'success' : 'danger';
                $status = $item->status == 1 ? 'Active' : 'Deactive';
                return '<span class="badge bg-soft-' . $badgeClass . '"><span class="legend-indicator bg-' . $legendBgClass . '"></span>' . $status . '</span>';
            })
            ->addColumn('action', function ($item) {
                $EditUrl = route('admin.package.edit', $item->id);
                $deleteUrl = route('admin.package.delete', $item->id);

                $canEdit = adminAccessRoute(config('role.manage_package.access.edit'));
                $canDelete = adminAccessRoute(config('role.manage_package.access.delete'));

                $actions = '';
                if ($canEdit) {
                    $actions .= '<a class="btn btn-white btn-sm" href="' . $EditUrl . '">
                            <i class="bi-pencil-fill me-1"></i>' . trans("Edit") . '
                          </a>';
                }
                if ($canDelete) {
                    $actions .= '<div class="btn-group">
                            <button type="button" class="btn btn-white btn-icon btn-sm dropdown-toggle dropdown-toggle-empty"
                                    id="productsEditDropdown1" data-bs-toggle="dropdown" aria-expanded="false">
                            </button>
                            <div class="dropdown-menu dropdown-menu-end mt-1" aria-labelledby="productsEditDropdown1">
                                <a class="dropdown-item deleteBtn" href="javascript:void(0)"
                                   data-route="' . $deleteUrl . '"
                                   data-package-name="' . optional($item->details)->title . '"
                                   data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="bi-trash dropdown-item-icon"></i> ' . trans("Delete") . '
                                </a>
                            </div>
                          </div>';
                }
                return $actions ?: '-';
            })
            ->rawColumns(['no', 'package-name', 'price', 'expiry-time', 'status', 'action'])
            ->make(true);
    }


    public function packageCreate()
    {
        $languages = Language::all();
        return view('admin.package.create', compact('languages'));
    }

    public function packageStore(Request $request, $language = null)
    {
        $rules = [
            'title.*' => 'required',
            'price' => 'sometimes|required|numeric|not_in:0',
            'is_free' => 'sometimes|required_without:price|integer|in:-1',
            'expiry_time' => 'sometimes|required_without:expiry_time_unlimited|integer|not_in:0',
            'expiry_time_unlimited' => 'sometimes|required_without:expiry_time|integer|in:-1',
            'is_image' => 'sometimes|required|boolean',
            'is_whatsapp' => 'sometimes|required|boolean',
            'is_messenger' => 'sometimes|required|boolean',
            'is_video' => 'sometimes|required|boolean',
            'is_amenities' => 'sometimes|required|boolean',
            'is_product' => 'sometimes|required|boolean',
            'is_business_hour' => 'sometimes|required|boolean',
            'no_of_listing' => 'sometimes|required_without:no_of_listing_unlimited|integer|not_in:0',
            'no_of_listing_unlimited' => 'sometimes|required_without:no_of_listing|integer|in:-1',
            'no_of_img_per_listing' => 'exclude_if:is_image,0|sometimes|required_without:no_of_img_per_listing_unlimited|integer|not_in:0',
            'no_of_img_per_listing_unlimited' => 'exclude_if:is_image,0|sometimes|required_without:no_of_img_per_listing|integer|in:-1',
            'no_of_amenities_per_listing' => 'exclude_if:is_amenities,0|sometimes|required_without:no_of_amenities_per_listing_unlimited|integer|not_in:0',
            'no_of_categories_per_listing' => 'min:1|numeric|not_in:0',
            'no_of_amenities_per_listing_unlimited' => 'exclude_if:is_amenities,0|sometimes|required_without:no_of_amenities_per_listing|integer|in:-1',
            'no_of_product' => 'exclude_if:is_product,0|sometimes|required_without:no_of_product_unlimited|integer|not_in:0',
            'no_of_product_unlimited' => 'exclude_if:is_product,0|sometimes|required_without:no_of_product|integer|in:-1',
            'no_of_img_per_product' => 'exclude_if:is_product,0|sometimes|required_without:no_of_img_per_product_unlimited|integer|not_in:0',
            'no_of_img_per_product_unlimited' => 'exclude_if:is_product,0|sometimes|required_without:no_of_img_per_product|integer|in:-1',
            'seo' => 'sometimes|required',
            'status' => 'sometimes|required',
            'image' => 'required|mimes:jpg,jpeg,png'
        ];

        $message = [
            'title.*.required' => __('Package title is required'),
            'price.required' => __('Price field is required'),
            'is_free.required_without' => __('price field is required'),
            'expiry_time.required' => __('Package expiry field is required'),
            'is_image.required' => __('please select image field'),
            'is_video.required' => __('please select video field'),
            'is_amenities.required' => __('please select amenities field'),
            'is_product.required' => __('please select product field'),
            'is_business_hour.required' => __('please select business hour field'),
            'no_of_listing.required_without' => __('No of listing field is required'),
            'no_of_listing_unlimited.required_without' => __('No of listing field is required'),
            'no_of_img_per_listing.required_without' => __('No of img per listing is required'),
            'no_of_img_per_listing_unlimited.required_without' => __('No of img per listing is required'),
            'no_of_amenities_per_listing.required_without' => __('No of amenities per listing is required'),
            'no_of_amenities_per_listing_unlimited.required_without' => __('No of amenities per listing is required'),
            'no_of_product.required_without' => __('No of product is required'),
            'no_of_product_unlimited.required_without' => __('No of product is required'),
            'no_of_img_per_product.required_without' => __('No of img per product is required'),
            'no_of_img_per_product_unlimited.required_without' => __('No of img per product is required'),
            'seo.required' => __('Seo is required'),
            'status.required' => __('Status is required'),
            'image.required' => __('Image is required'),
        ];

        $request->validate($rules, $message);

        DB::beginTransaction();
        try {
            $package = new Package();
            $package->price = isset($request->is_free) && $request->is_free == -1 ? null : $request->price;
            $package->is_multiple_time_purchase = isset($request->is_free) && $request->is_free == -1 ? $request->is_multiple_time_purchase : 0;
            $package->expiry_time_type = isset($request->expiry_time_unlimited) && $request->expiry_time_unlimited == -1 ? null : $request->expiry_time_type;

            if ($request->expiry_time) {
                $package->expiry_time = $request->expiry_time;
                if ($request->expiry_time == 1 && $request->expiry_time_type == 'Days') {
                    $package->expiry_time_type = 'Day';
                } elseif ($request->expiry_time == 1 && $request->expiry_time_type == 'Months') {
                    $package->expiry_time_type = 'Month';
                } elseif ($request->expiry_time == 1 && $request->expiry_time_type == 'Years') {
                    $package->expiry_time_type = 'Year';
                } else {
                    $package->expiry_time_type = $request->expiry_time_type;
                }
            }

            $package->is_renew = $request->is_renew ? $request->is_renew : 0;
            $package->is_image = $request->is_image;
            $package->is_video = $request->is_video;
            $package->is_amenities = $request->is_amenities;
            $package->is_product = $request->is_product;
            $package->is_create_from = $request->is_create_from;
            $package->is_business_hour = $request->is_business_hour;
            $package->no_of_listing = isset($request->no_of_listing_unlimited) && $request->no_of_listing_unlimited == -1 ? null : $request->no_of_listing;
            $package->no_of_img_per_listing = isset($request->no_of_img_per_listing_unlimited) && $request->no_of_img_per_listing_unlimited == -1 && $request->is_image == 0 ? null : $request->no_of_img_per_listing;
            $package->no_of_amenities_per_listing = isset($request->no_of_amenities_per_listing_unlimited) && $request->no_of_amenities_per_listing_unlimited == -1 && $request->is_amenities == 0 ? null : $request->no_of_amenities_per_listing;
            $package->no_of_categories_per_listing = $request->no_of_categories_per_listing;
            $package->no_of_product = isset($request->no_of_product_unlimited) && $request->no_of_product_unlimited == -1 && $request->is_product == 0 ? null : $request->no_of_product;
            $package->no_of_img_per_product = isset($request->no_of_img_per_product_unlimited) && $request->no_of_img_per_product_unlimited == -1 && $request->is_product == 0 ? null : $request->no_of_img_per_product;
            $package->seo = $request->seo;
            $package->is_whatsapp = $request->is_whatsapp;
            $package->is_messenger = $request->is_messenger;
            $package->status = $request->status;

            if ($request->hasFile('image')) {
                try {
                    $image = $this->fileUpload($request->image, config('filelocation.package.path'), null, config('filelocation.package.size'), 'webp', 99);
                    if ($image) {
                        $package->image = $image['path'];
                        $package->driver = $image['driver'];
                    }
                } catch (\Exception $exp) {
                    return back()->with('error', __('Image could not be uploaded.'));
                }
            }
            $package->save();
            $package->details()->create([
                'language_id' => $language,
                'title' => $request["title"][$language],
            ]);

            if ($package->expiry_time == 1 && ($package->expiry_time_type == 'Month' || $package->expiry_time_type == 'Year')) {
                $gateways = Gateway::where('subscription_on', 1)->get();
                foreach ($gateways as $gateway) {
                    $jobs = 'App\\Jobs\\gateway\\' . $gateway->code;
                    $jobs::dispatch($package, $gateway, 'create');
                }
            }

            DB::commit();
            return back()->with('success', __('Package Saved Successfully.'));
        } catch (\Exception $exp) {
            DB::rollback();
            return back()->with('error', $exp->getMessage());
        }
    }

    public function packageEdit($id)
    {
        $data['id'] = $id;
        $data['languages'] = Language::orderBy('default_status', 'desc')->get();
        $data['packageDetails'] = PackageDetails::with('package')->where('package_id', $id)->get()->groupBy('language_id');
        $data['gateways'] = Gateway::select(['id', 'name', 'code', 'subscription_on'])->where('subscription_on', 1)->get();
        return view('admin.package.edit', $data);
    }

    public function packageUpdate(Request $request, $id, $language_id)
    {
        $rules = [
            'title.*' => 'required',
            'price' => 'sometimes|required|numeric|not_in:0',
            'is_free' => 'sometimes|required_without:price|integer|in:-1',
            'expiry_time' => 'sometimes|required_without:expiry_time_unlimited|integer|not_in:0',
            'expiry_time_unlimited' => 'sometimes|required_without:expiry_time|integer|in:-1',
            'is_image' => 'sometimes|required|boolean',
            'is_whatsapp' => 'sometimes|required|boolean',
            'is_messenger' => 'sometimes|required|boolean',
            'is_video' => 'sometimes|required|boolean',
            'is_amenities' => 'sometimes|required|boolean',
            'is_product' => 'sometimes|required|boolean',
            'is_business_hour' => 'sometimes|required|boolean',
            'no_of_listing' => 'sometimes|required_without:no_of_listing_unlimited|integer|not_in:0',
            'no_of_listing_unlimited' => 'sometimes|required_without:no_of_listing|integer|in:-1',
            'no_of_img_per_listing' => 'exclude_if:is_image,0|sometimes|required_without:no_of_img_per_listing_unlimited|integer|not_in:0',
            'no_of_img_per_listing_unlimited' => 'exclude_if:is_image,0|sometimes|required_without:no_of_img_per_listing|integer|in:-1',
            'no_of_amenities_per_listing' => 'exclude_if:is_amenities,0|sometimes|required_without:no_of_amenities_per_listing_unlimited|integer|not_in:0',
            'no_of_categories_per_listing' => 'min:1|numeric|not_in:0',
            'no_of_amenities_per_listing_unlimited' => 'exclude_if:is_amenities,0|sometimes|required_without:no_of_amenities_per_listing|integer|in:-1',
            'no_of_product' => 'exclude_if:is_product,0|sometimes|required_without:no_of_product_unlimited|integer|not_in:0',
            'no_of_product_unlimited' => 'exclude_if:is_product,0|sometimes|required_without:no_of_product|integer|in:-1',
            'no_of_img_per_product' => 'exclude_if:is_product,0|sometimes|required_without:no_of_img_per_product_unlimited|integer|not_in:0',
            'no_of_img_per_product_unlimited' => 'exclude_if:is_product,0|sometimes|required_without:no_of_img_per_product|integer|in:-1',
            'seo' => 'sometimes|required',
            'status' => 'sometimes|required',
        ];

        $message = [
            'title.*.required' => __('Package title is required'),
            'price.required' => __('Price field is required'),
            'is_free.required_without' => __('price field is required'),
            'expiry_time.required' => __('Package expiry field is required'),
            'is_image.required' => __('please select image field'),
            'is_video.required' => __('please select video field'),
            'is_amenities.required' => __('please select amenities field'),
            'is_product.required' => __('please select product field'),
            'is_business_hour.required' => __('please select business hour field'),
            'no_of_listing.required_without' => __('No of listing field is required'),
            'no_of_listing_unlimited.required_without' => __('No of listing field is required'),
            'no_of_img_per_listing.required_without' => __('No of img per listing is required'),
            'no_of_img_per_listing_unlimited.required_without' => __('No of img per listing is required'),
            'no_of_amenities_per_listing.required_without' => __('No of amenities per listing is required'),
            'no_of_amenities_per_listing_unlimited.required_without' => __('No of amenities per listing is required'),
            'no_of_product.required_without' => __('No of product is required'),
            'no_of_product_unlimited.required_without' => __('No of product is required'),
            'no_of_img_per_product.required_without' => __('No of img per product is required'),
            'no_of_img_per_product_unlimited.required_without' => __('No of img per product is required'),
            'seo.required' => __('Seo is required'),
            'status.required' => __('Status is required'),
        ];

        $request->validate($rules, $message);

        DB::beginTransaction();
        try {
            $package = Package::find($id);
            if (!$package) {
                return back()->with('error', __('Package not Found'));
            }

           $language = Language::select('id','default_status')->findOrFail($language_id);
            if ($language->default_status){
                $arr = [];
                if ($request->gateway_plan_id && count($request->gateway_plan_id) > 0) {
                    foreach ($request->gateway_plan_id as $key => $planId) {
                        $arr[$key] = $planId[0];
                    }
                }
                $oldPackage = $package;

                $package->price = isset($request->is_free) && $request->is_free == -1 ? null : $request->price;
                $package->is_multiple_time_purchase = isset($request->is_free) && $request->is_free == -1 ? $request->is_multiple_time_purchase : 0;
                $package->expiry_time_type = isset($request->expiry_time_unlimited) && $request->expiry_time_unlimited == -1 ? null : $request->expiry_time_type;

                if ($request->expiry_time) {
                    $package->expiry_time = $request->expiry_time;
                    if ($request->expiry_time == 1 && $request->expiry_time_type == 'Days') {
                        $package->expiry_time_type = 'Day';
                    } elseif ($request->expiry_time == 1 && $request->expiry_time_type == 'Months') {
                        $package->expiry_time_type = 'Month';
                    } elseif ($request->expiry_time == 1 && $request->expiry_time_type == 'Years') {
                        $package->expiry_time_type = 'Year';
                    } else {
                        $package->expiry_time_type = $request->expiry_time_type;
                    }
                }

                $package->is_renew = $request->is_renew ? $request->is_renew : 0;
                $package->is_image = $request->is_image;
                $package->is_video = $request->is_video;
                $package->is_amenities = $request->is_amenities;
                $package->is_product = $request->is_product;
                $package->is_create_from = $request->is_create_from;
                $package->is_business_hour = $request->is_business_hour;
                $package->no_of_listing = isset($request->no_of_listing_unlimited) && $request->no_of_listing_unlimited == -1 ? null : $request->no_of_listing;
                $package->no_of_img_per_listing = isset($request->no_of_img_per_listing_unlimited) && $request->no_of_img_per_listing_unlimited == -1 && $request->is_image == 0 ? null : $request->no_of_img_per_listing;
                $package->no_of_amenities_per_listing = isset($request->no_of_amenities_per_listing_unlimited) && $request->no_of_amenities_per_listing_unlimited == -1 && $request->is_amenities == 0 ? null : $request->no_of_amenities_per_listing;
                $package->no_of_categories_per_listing = $request->no_of_categories_per_listing;
                $package->no_of_product = isset($request->no_of_product_unlimited) && $request->no_of_product_unlimited == -1 && $request->is_product == 0 ? null : $request->no_of_product;
                $package->no_of_img_per_product = isset($request->no_of_img_per_product_unlimited) && $request->no_of_img_per_product_unlimited == -1 && $request->is_product == 0 ? null : $request->no_of_img_per_product;
                $package->seo = $request->seo;
                $package->is_whatsapp = $request->is_whatsapp;
                $package->is_messenger = $request->is_messenger;
                $package->status = $request->status;
                $package->gateway_plan_id = $arr;

                if ($request->hasFile('image')) {
                    try {
                        $image = $this->fileUpload($request->image, config('filelocation.package.path'), null, config('filelocation.package.size'), 'webp', 99, $package->image, $package->driver);
                    } catch (\Exception $exp) {
                        return back()->with('error', __('Image could not be uploaded.'));
                    }
                }

                $package->image = $image['path'] ?? $package->image;
                $package->driver = $image['driver'] ?? $package->driver;
                $package->save();


                if ($package->price != null && $package->expiry_time == 1 && ($package->expiry_time_type == 'Month' || $package->expiry_time_type == 'Year')) {
                    if ($package->price != $oldPackage->price) {
                        $gateways = Gateway::where('subscription_on', 1)->get();
                        foreach ($gateways as $gateway) {
                            $jobs = 'App\\Jobs\\gateway\\' . $gateway->code;
                            $jobs::dispatch($oldPackage, $gateway, 'update');
                        }
                    }

                    if ($request->status == 1) {
                        $gateways = Gateway::where('subscription_on', 1)->get();
                        foreach ($gateways as $gateway) {
                            $jobs = 'App\\Jobs\\gateway\\' . $gateway->code;
                            $jobs::dispatch($oldPackage, $gateway, 'active');
                        }
                    } elseif ($request->status == 0) {
                        $gateways = Gateway::where('subscription_on', 1)->get();
                        foreach ($gateways as $gateway) {
                            $jobs = 'App\\Jobs\\gateway\\' . $gateway->code;
                            $jobs::dispatch($oldPackage, $gateway, 'deactive');
                        }
                    }
                }
            }

            $package->details()->updateOrCreate(
                ['language_id' => $language_id],
                [
                    'title' => $request["title"][$language_id],
                ]
            );
            DB::commit();
            return back()->with('success', __('Package Updated Successfully.'));
        } catch (\Exception $exp) {
            DB::rollback();
            return back()->with('error', $exp->getMessage());
        }
    }

    public function packageDelete($id)
    {
        $package = Package::with('details')->findOrFail($id);

        $package->details->delete();
        $this->fileDelete($package->driver, $package->image);
        $package->delete();
        return back()->with('success', __('Package has been deleted'));
    }


}
