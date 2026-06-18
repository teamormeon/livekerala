<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Product;
use App\Models\ProductQuery;
use App\Models\PurchasePackage;
use App\Models\User;
use App\Models\Viewer;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = auth()->user();
        $firebaseNotify = config('firebase');
        $listings = collect(Listing::selectRaw("COUNT(CASE WHEN user_id = $user->id   THEN id END) AS total_listing")
            ->selectRaw("COUNT(CASE WHEN user_id = $user->id  AND status = 1  THEN id END) AS active_listing")
            ->selectRaw("COUNT(CASE WHEN user_id = $user->id  AND status = 0  THEN id END) AS pending_listing")
            ->get()->makeHidden(['avgRating'])->toArray())->collapse();
        $views = Viewer::whereUser_id($user->id)->toBase()->count();
        $totalProduct = Product::where('user_id', $user->id)->toBase()->count();
        $productUnseenQuires  = ProductQuery::where('user_id', $user->id)->where('customer_enquiry', 0)->toBase()->count();
        $pendingPackage = PurchasePackage::where('user_id', $user->id)->where('status', 0)->toBase()->count();
        $activePackage = PurchasePackage::where('user_id', $user->id)->where('status', 1)->toBase()->count();

        $data = [
            'total_listing' => $listings['total_listing'],
            'active_listing' => $listings['active_listing'],
            'pending_listing' => $listings['pending_listing'],
            'total_views' => $views,
            'total_product' => $totalProduct,
            'total_product_quires' => $productUnseenQuires,
            'pending_package' => $pendingPackage,
            'active_package' => $activePackage,
        ];

        return response()->json($this->withSuccess($data));
    }


}
