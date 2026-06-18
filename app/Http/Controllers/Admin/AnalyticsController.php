<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Analytics;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AnalyticsController extends Controller
{
    public function listingAnalytics()
    {
        return view('admin.analytics.index');
    }

    public function listingAnalyticsSearch(Request $request)
    {
        $search = $request->search['value']??null;
        $filterDate = explode('-', $request->filterDate);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;

        $analytics = Analytics::with(['getListing:id,title,slug'])->withCount('listCount')
            ->latest()
            ->when(!empty($search), function ($query) use ($search) {
                return $query->whereHas('getListing', function ($query) use ($search) {
                    $query->where('title', 'LIKE', '%' . $search . '%');
                });
            })
            ->when(!empty($request->filterDate) && $endDate == null, function ($query) use ($startDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $query->whereDate('created_at', $startDate);
            })
            ->when(!empty($request->filterDate) && $endDate != null, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $endDate = Carbon::createFromFormat('d/m/Y', trim($endDate));
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });


        return DataTables::of($analytics)
            ->addColumn('checkbox', function ($item) {
                return ' <input type="checkbox" id="chk-' . $item->id . '"
                           class="form-check-input row-tic tic-check" name="check" value="' . $item->id . '"
                           data-id="' . $item->id . '">';
            })
            ->addColumn('listing', function ($item) {
                $url = route('listing.details', optional($item->getListing)->slug);
                return '<a class="d-flex align-items-center me-2" href="' . $url . '" target="_blank">' . \Illuminate\Support\Str::limit(optional($item->getListing)->title, 50) . '</a>';

            })
            ->addColumn('total-visit', function ($item) {
                return '<span class="badge bg-soft-dark text-dark  ms-1">' . numberConverter($item->list_count_count) . '</span>';
            })
            ->addColumn('ip', function ($item) {
                return '<p class="d-none">' . htmlspecialchars($item->code) . '</p>
            <div class="input-group input-group-sm input-group-merge table-input-group">
                <input id="referralsKeyCode' . $item->id . '" type="text" class="form-control"
                       readonly
                       value="' . htmlspecialchars($item->visitor_ip) . '">
                <a class="js-clipboard input-group-append input-group-text"
                   onclick="copyFunction(\'referralsKeyCode' . $item->id . '\')"
                   href="javascript:void(0)"
                   title="Copy to clipboard">
                    <i id="referralsKeyCodeIcon' . $item->id . '" class="bi-clipboard"></i>
                </a>
            </div>';
            })
            ->addColumn('browser', function ($item) {
                $file = asset("assets/admin/img/browser/" . browserIcon($item->browser) . ".svg");
                return '<img class="avatar avatar-xss me-2"
                                 src="' . $file . '"
                                 alt="Image Description"> ' . $item->browser . ' on ' . $item->os_platform;
            })
            ->addColumn('device', function ($item) {
                return '<i class="' . deviceIcon($item->device_name) . ' fs-3 me-2"></i>' . $item->device_name;
            })
            ->addColumn('country', function ($item) {
                return '<div>
                            <span class="d-block mb-0 ps-2">' . ($item->country ?? "N/A") . '</span>
                        </div>';
            })
            ->addColumn('last-visited-at', function ($item) {
                return dateTime(optional($item->lastVisited)->created_at);
            })
            ->rawColumns(['checkbox', 'listing', 'total-visit', 'ip', 'browser', 'device', 'country', 'last-visited-at'])

            ->make(true);
    }

    public function listingAnalyticsShow(Request $request)
    {
        $analytics = Analytics::where('id', $request->id)->first();
        return response()->json($analytics);
    }


    public function listingAnalyticsDelete($id)
    {
        Analytics::findOrFail($id)->delete();
        return back()->with('success', 'Delete Successfully!');
    }
}
