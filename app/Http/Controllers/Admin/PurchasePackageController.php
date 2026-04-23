<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PackageExport;
use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PurchasePackage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PurchasePackageController extends Controller
{
    public function purchasePackage()
    {
        $data['packages'] = Package::with('details')->latest()->get();
        return view('admin.purchase_package.index', $data);
    }

    public function purchasePackageSearch(Request $request)
    {
        $search = $request->search['value']??null;
        $filter_user = $request->filter_user;
        $filterPackageId = $request->filterPackageId;
        $filterValidity = $request->filterValidity;
        $filterStatus = $request->filterStatus;
        $filterDate = explode('-', $request->filterDate);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;

        $Packages = PurchasePackage::query()->with(['get_user','get_package.details'])
            ->when(!empty($search), function ($query) use ($search) {
                return $query->whereHas('get_user', function ($q2) use ($search) {
                    $q2->where('firstname', 'LIKE', "%$search%")
                    ->orWhere('lastname', 'LIKE', "%$search%")
                    ->orWhere('username', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%");
                })
                    ->orWhereHas('get_package.details', function ($q3) use ($search) {
                        $q3->where('title', 'LIKE', "%$search%");
                    });
            })
            ->when(isset($filter_user), function ($query1) use ($filter_user) {
                return $query1->whereHas('get_user', function ($q2) use ($filter_user) {
                    $q2->where('firstname', 'LIKE', "%$filter_user%")
                        ->orWhere('lastname', 'LIKE', "%$filter_user%")
                        ->orWhere('username', 'LIKE', "%$filter_user%")
                        ->orWhere('email', 'LIKE', "%$filter_user%");
                    });
            })
            ->when(isset($filterPackageId) && $filterPackageId != 'all', function ($query2) use ($filterPackageId) {
                return $query2->where('package_id', $filterPackageId);
            })
            ->when(isset($filterValidity) && $filterValidity != 'all', function ($query3) use ($filterValidity) {
                $carbonNow = Carbon::now();
                if ($filterValidity == 'active') {
                    return $query3->where(function($q) use ($carbonNow) {
                        $q->where('expire_date', '>=', $carbonNow);
                    });
                }
                if ($filterValidity == 'expired') {
                    return $query3->where('expire_date', '<', $carbonNow);
                }
            })
            ->when(isset($filterStatus), function ($query4) use ($filterStatus) {
                if ($filterStatus == 'all') {
                    return $query4->where('status', '!=', null);
                }
                return $query4->where('status', $filterStatus);
            })
            ->when(!empty($request->filterDate) && $endDate == null, function ($query5) use ($startDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $query5->whereDate('created_at', $startDate);
            })
            ->when(!empty($request->filterDate) && $endDate != null, function ($query6) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $endDate = Carbon::createFromFormat('d/m/Y', trim($endDate));
                $query6->whereBetween('created_at', [$startDate, $endDate]);
            })->orderBy('id', 'desc')->latest();

        return DataTables::of($Packages)
            ->addColumn('checkbox', function ($item) {
                return ' <input type="checkbox" id="chk-' . $item->id . '"
                           class="form-check-input row-tic tic-check" name="check" value="' . $item->id . '"
                           data-id="' . $item->id . '">';
            })
            ->addColumn('user', function ($item) {
                return '<a class="d-flex align-items-center me-2" href="javascript:void(0)">
                                <div class="flex-shrink-0">
                                  ' . $item->get_user->profilePicture() . '
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <h5 class="text-hover-primary mb-0">' . optional($item->get_user)->firstname . ' ' . optional($item->get_user)->lastname . '</h5>
                                  <span class="fs-6 text-body">' . optional($item->get_user)->email . '</span>
                                </div>
                              </a>';

            })
            ->addColumn('package-name', function ($item) {
                return '<div class="d-flex align-items-center">
                            <div class="avatar avatar-sm avatar-circle">
                                <img class="avatar-img" src="'.getFile(optional($item->get_package)->driver, optional($item->get_package)->image).'" alt="image">
                            </div>
                            <span class="d-block mb-0 ps-3">'.optional(optional($item->get_package)->details)->title.'</span>
                        </div>';
            })
            ->addColumn('validity', function ($item) {
                $badgeClass = $item->expire_date >= \Illuminate\Support\Carbon::now() || $item->expire_date == null ? 'success text-success' : 'danger text-danger';
                $legendBgClass = $item->expire_date >= \Illuminate\Support\Carbon::now() || $item->expire_date == null ? 'success' : 'danger';
                $status = $item->expire_date >= \Illuminate\Support\Carbon::now() || $item->expire_date == null ? 'Active' : 'Expired';
                return '<span class="badge bg-soft-'.$badgeClass.'"><span class="legend-indicator bg-'.$legendBgClass.'"></span>'.$status.'</span>';
            })
            ->addColumn('subscription-type', function ($item) {
                if ($item->api_subscription_id) {
                    return '<span class="badge bg-soft-primary text-primary">
                    <span class="legend-indicator bg-primary"></span>' . trans('Automatic') . '
                  </span>';

                } else {
                    return '<span class="badge bg-soft-secondary text-dark">
                    <span class="legend-indicator bg-secondary"></span>' . trans('Manual') . '
                  </span>';
                }
            })
            ->addColumn('status', function ($item) {
                if ($item->status == 0) {
                    return '<span class="badge bg-soft-warning text-warning">
                    <span class="legend-indicator bg-warning"></span>' . trans('Pending') . '
                  </span>';

                } elseif ($item->status == 1) {
                    return '<span class="badge bg-soft-success text-success">
                    <span class="legend-indicator bg-success"></span>' . trans('Running') . '
                  </span>';

                } else {
                    return '<span class="badge bg-soft-danger text-danger">
                    <span class="legend-indicator bg-danger"></span>' . trans('Cancel') . '
                  </span>';
                }
            })
            ->addColumn('purchased-date', function ($item) {
                return dateTime($item->purchase_date);
            })
            ->addColumn('expired-date', function ($item) {
                return $item->expire_date == null ? 'Unlimited' : dateTime($item->expire_date);
            })
            ->addColumn('action', function ($item) {
                $cancel = route('admin.purchase.package.subscription.cancel', $item->id);
                if (adminAccessRoute(config('role.purchase_package.access.edit')) && $item->api_subscription_id && $item->status) {
                    return '<div class="btn-group" role="group">
                      <a href="javascript:void(0)"
                       class="btn btn-white btn-sm delete_btn" data-bs-target="#delete"
                           data-bs-toggle="modal" data-route="' . $cancel . '">
                        <i class="fal fa-times me-1"></i> ' . trans("Cancel") . '
                      </a>
                  </div>';
                } else {
                    return '-';
                }
            })
            ->rawColumns(['checkbox','user','package-name','validity', 'subscription-type', 'status', 'purchased-date', 'expired-date','action'])
            ->make(true);
    }

    public function purchasePackageSubscriptionCancel($id)
    {
        $subscriptionPurchase = PurchasePackage::findOrFail($id);

        try {
            $code = $subscriptionPurchase->deposit->gateway->code;
            $getwayObj = 'App\\Services\\Subscription\\' . $code . '\\Payment';
            $data = $getwayObj::cancelSubscription($subscriptionPurchase);
            if ($data['status'] == 'success') {
                $subscriptionPurchase->status = 2;
                $subscriptionPurchase->deleted_at = Carbon::now();
                $subscriptionPurchase->save();
                return back()->with('success', 'subscription has been canceled');
            } else {
                return back()->with('error', 'You can not cancel subscription');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong. Please try again');
        }
    }

    public function purchasePackageExportExcel(Request $request)
    {
        return Excel::download(new PackageExport($request->package_id), 'purchased_package.xlsx');
    }
    public function purchasePackageExportCsv(Request $request)
    {
        return Excel::download(new PackageExport($request->package_id), 'purchased_package.csv');
    }
    public function purchasePackageDeleteMultiple(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select any Item.');
            return response()->json(['error' => 1]);
        } else {
            PurchasePackage::whereIn('id', $request->strIds)->delete();
            session()->flash('success', 'Purchase Package Deleted');
            return response()->json(['success' => 1]);
        }
    }
}
