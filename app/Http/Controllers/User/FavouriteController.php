<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Favourite;
use App\Models\Listing;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    public function addToWishList(Request $request)
    {
        $userId = auth()->id();
        $listing = Listing::with('getFavourite')->find($request->listing_id);

        if ($listing->getFavourite) {
            $stage='remove';
            $favourite = Favourite::where('listing_id',$request->listing_id)->where('client_id', $userId)->first();
            $favourite->delete();
        } else {
            $stage ='added';
            $data = new Favourite();
            $data->user_id = $request->user_id;
            $data->client_id = $userId;
            $data->purchase_package_id = $request->purchase_package_id;
            $data->listing_id =$request->listing_id;
            $data->save();
        }
        return response()->json([
            'stage' => $stage
        ]);
    }

    public function wishList(Request $request)
    {
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $data['favourite_listings'] = Favourite::with(['get_listing'])
            ->when(isset($search['name']), function ($query) use ($search) {
                return $query->whereHas('get_listing', function ($query) use ($search) {
                    $query->where('title', 'LIKE', "%{$search['name']}%");
                });
            })
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
                return $q2->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->where('client_id', auth()->id())
            ->latest()
            ->paginate(basicControl()->paginate);
        return view('user_panel.user.wishList.index', $data);
    }

    public function wishListDelete($id)
    {
        Favourite::where('client_id', auth()->id())->findOrfail($id)->delete();
        return back()->with('success', __('Deleted Successful!'));
    }
}
