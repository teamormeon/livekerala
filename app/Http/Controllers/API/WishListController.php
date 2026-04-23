<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Favourite;
use App\Models\Listing;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishListController extends Controller
{
    use ApiResponse;

    public function wishList(Request $request)
    {
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $favouriteListings = Favourite::with(['get_listing:id,category_id,title,slug,thumbnail,thumbnail_driver'])
            ->when(isset($search['listing_name']), function ($query) use ($search) {
                return $query->whereHas('get_listing', function ($query) use ($search) {
                    $query->where('title', 'LIKE', "%{$search['listing_name']}%");
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

        $formatedFavouriteListings = $favouriteListings->getCollection()->map(function ($favouriteListing) {
            return [
                'id' => $favouriteListing->id,
                'listing_id' => $favouriteListing->listing_id,
                'listing_title' => html_entity_decode($favouriteListing->get_listing->title),
                'listing_slug' => optional($favouriteListing->get_listing)->slug,
                'categories' => strip_tags(optional($favouriteListing->get_listing)->getCategoriesName()),
                'thumbnail' => getFile(optional($favouriteListing->get_listing)->thumbnail_driver, optional($favouriteListing->get_listing)->thumbnail),
                'created_at' => $favouriteListing->created_at,
            ];
        });

        $favouriteListings->setCollection($formatedFavouriteListings);
        return response()->json($this->withSuccess($favouriteListings));
    }


    public function wishListAdd(Request $request)
    {
        try {
            if (Auth::check()) {
                $userId = auth()->id();
                $listing = Listing::select('id','user_id','purchase_package_id')->find($request->listing_id);
                $favourite = Favourite::select('id','user_id','client_id','listing_id')->where('listing_id',$request->listing_id)->where('client_id', $userId)->first();

                if ($favourite) {
                    $favourite->delete();
                    $data['message'] = 'Listing removed from wishlist';
                } else {
                    Favourite::create([
                        'user_id' => $listing->user_id,
                        'client_id' => $userId,
                        'listing_id' => $request->listing_id,
                        'purchase_package_id' => $listing->purchase_package_id,
                    ]);
                    $data['message'] = 'Listing added to wishlist';
                }
                return response()->json($this->withSuccess($data));
            } else {
                return response()->json($this->withError('Please log in first to add wishlist'));
            }
        }catch (\Exception $exception){
            return response()->json($this->withError($exception->getMessage()));
        }
    }

    public function wishListDestroy($id)
    {
        $wishlistItem = Favourite::where('client_id', auth()->id())->find($id);
        if (!$wishlistItem){
            return response()->json($this->withError('Wishlist Item not found'));
        }
        $wishlistItem->delete();
        return response()->json($this->withSuccess('Wishlist Item Removed'));
    }

}
