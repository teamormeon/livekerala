<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Analytics;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    use ApiResponse;

    public function analytics(Request $request, $id = null)
    {
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $analytics = Analytics::with(['getListing:id,user_id,title,slug','lastVisited:listing_id,created_at'])->withCount('listCount')
            ->when(isset($id), function ($query) use ($id) {
                return $query->where('listing_id', $id);
            })
            ->when(isset($search['listing_title']), function ($query) use ($search) {
                return $query->whereHas('getListing', function ($query) use ($search) {
                    $query->where('title', 'LIKE', "%{$search['listing_title']}%");
                });
            })
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate,$toDate) {
                return $q2->whereBetween('created_at', [$fromDate,$toDate]);
            })
            ->where('listing_owner_id', auth()->id())
            ->latest()->groupBy('listing_id')->paginate(basicControl()->paginate);

        $formatedAnalytics = $analytics->getCollection()->map(function ($analytic) {
            $data = [
                'id' => $analytic->id,
                'listing_owner_id' => $analytic->listing_owner_id,
                'listing_id' => $analytic->listing_id,
                'visitor_ip' => $analytic->visitor_ip,
                'country' => $analytic->country,
                'listing_title' => $analytic->getListing ? html_entity_decode($analytic->getListing->title) : null,
                'total_visit' => $analytic->listCount ? $analytic->list_count_count : null,
                'last_visited' => $analytic->lastVisited ? $analytic->lastVisited->created_at : null,
            ];
            return $data;
        });
        $analytics->setCollection($formatedAnalytics);
        return response()->json($this->withSuccess($analytics));
    }

    public function listingAnalyticsShow($id = null)
    {
        $singleListingAnalytics = Analytics::where('listing_owner_id', auth()->id())
            ->where('listing_id', $id)
            ->latest()->paginate(config('basic.paginate'));

        $formatedSingleListingAnalytics = $singleListingAnalytics->map(function ($singleListingAnalytics) {
            return [
                'id' => $singleListingAnalytics->id,
                'listing_owner_id' => $singleListingAnalytics->listing_owner_id,
                'listing_id' => $singleListingAnalytics->listing_id,
                'visitor_ip' => $singleListingAnalytics->visitor_ip,
                'country' => $singleListingAnalytics->country,
                'city' => $singleListingAnalytics->city,
                'code' => $singleListingAnalytics->code,
                'os_platform' => $singleListingAnalytics->os_platform,
                'browser' => $singleListingAnalytics->browser,
                'created_at' => $singleListingAnalytics->created_at,
            ];
        });
        return response()->json($this->withSuccess($formatedSingleListingAnalytics));
    }


}
