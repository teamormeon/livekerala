<?php

namespace App\Http\Controllers\User;


use App\Helpers\GoogleAuthenticator;
use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\Language;
use App\Models\Listing;
use App\Models\Product;
use App\Models\ProductQuery;
use App\Models\PurchasePackage;
use App\Models\User;
use App\Models\Viewer;
use App\Traits\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;


class HomeController extends Controller
{
    use Upload;

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
        $this->theme = template();
    }

    public function saveToken(Request $request)
    {
        try {
            Auth::user()
                ->fireBaseToken()
                ->create([
                    'token' => $request->token,
                ]);
            return response()->json([
                'msg' => 'token saved successfully.',
            ]);
        } catch (\Exception $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }


    public function index(Request $request)
    {
        $user = Auth::user();
        $data['firebaseNotify'] = config('firebase');

        $data['all_viewers_count'] = Viewer::whereUser_id($user->id)->count();
        $data['user_information'] = User::with(['follower.get_follwer_user', 'following.get_following_user'])->findOrFail($user->id);
        $data['all_listing_addresses'] = Listing::where('user_id', $user->id)->distinct()->pluck('address');

        $data['listings'] = collect(Listing::selectRaw("COUNT(CASE WHEN user_id = $user->id   THEN id END) AS listing_infos")
            ->selectRaw("COUNT(CASE WHEN user_id = $user->id  AND status = 1  THEN id END) AS activeListing")
            ->selectRaw("COUNT(CASE WHEN user_id = $user->id  AND status = 0  THEN id END) AS pendingListing")
            ->get()->makeHidden(['avgRating'])->toArray())->collapse();

        $data['userPurchasePackage'] = PurchasePackage::with(['get_package', 'get_package.details'])->where('user_id', $user->id)->take(3)->latest()->get();

        $purchasePackage = PurchasePackage::where('user_id', $user->id)
            ->selectRaw('COUNT(CASE WHEN status = 0 THEN 1 END) as pendingPackage, COUNT(CASE WHEN status = 1 THEN 1 END) as activePackage')
            ->first();
        $data['pendingPackage'] = $purchasePackage->pendingPackage;
        $data['activePackage'] = $purchasePackage->activePackage;


        $data['totalProduct'] = Product::where('user_id', $user->id)->count();
        $data['productUnseenQuires']  = ProductQuery::where('user_id', $user->id)->where('customer_enquiry', 0)->count();

        $search = $request->all();
        $data['user_listings'] = Listing::with(['get_package.get_package.details'])->latest()
            ->when(isset($search['listing_search_name']), function ($query) use ($search) {
                return $query->where('title', 'LIKE', "%{$search['listing_search_name']}%")
                    ->orWhere('address', 'LIKE', "%{$search['listing_search_name']}%")
                    ->orWhereHas('get_package.get_package.details', function ($q2) use ($search) {
                        $q2->where('title', 'LIKE', "%{$search['listing_search_name']}%");
                    });
            })
            ->when(isset($search['listing_location_name']), function ($query2) use ($search) {
                return $query2->where('address', 'LIKE', "%{$search['listing_location_name']}%");
            })
            ->where('user_id', $user->id)
            ->limit(5)->get();
        $packages = PurchasePackage::select('expire_date')->where('user_id', auth()->id())->whereDate('expire_date', '>=', today())->whereNotNull('expire_date')->orderBy('expire_date','asc')->first();
        $data['handover'] = $packages ? Carbon::parse($packages->expire_date) : Carbon::today();
        return view('user_panel.user.dashboard', $data);
    }

    public function calender()
    {
        $packages = PurchasePackage::where('user_id', auth()->id())->whereDate('expire_date', '>=', today())->whereNotNull('expire_date')->get();
        $data = [];
        foreach ($packages as $package) {
            $data[] = [
                'title' => $package->get_package->details->title,
                'url' => route('user.myPackages', $package->id),
                'start' => $package->expire_date
            ];
        }
        return response()->json($data);
    }


}
