<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Analytics;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function analytics(Request $request, $id = null, $title = null)
    {
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $data['allAnalytics'] = Analytics::with(['getListing','lastVisited:listing_id,created_at'])->withCount('listCount')
            ->when(isset($id), function ($query) use ($id) {
                return $query->where('listing_id', $id);
            })
            ->when(isset($search['listing']), function ($query) use ($search) {
                return $query->whereHas('getListing', function ($query) use ($search) {
                    $query->where('title', 'LIKE', "%{$search['listing']}%");
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
        return view('user_panel.user.analytics.index', $data);
    }

    public function listingAnalyticsShow($id = null)
    {
        $data['allSingleListingAnalytics'] = Analytics::with(['getListing'])
            ->where('listing_owner_id', auth()->id())
            ->where('listing_id', $id)
            ->latest()->paginate(20);
        return view('user_panel.user.analytics.details', $data);
    }
}
