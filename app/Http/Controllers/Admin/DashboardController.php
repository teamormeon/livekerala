<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Payout;
use App\Models\PurchasePackage;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserKyc;
use App\Models\Visitor;
use App\Traits\Notify;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\Upload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    use Upload, Notify;

    public function index()
    {
        $purchasePackageQuery = PurchasePackage::query()->toBase();
        $visitorQuery = Visitor::query()->toBase();

        $todaySalesAmount = $purchasePackageQuery->clone()->where('status', 1)
            ->whereDate('created_at', Carbon::today())
            ->sum('price');
        $yesterdaySalesAmount = $purchasePackageQuery->clone()->where('status', 1)
            ->whereDate('created_at', Carbon::yesterday())
            ->sum('price');


        $data['firebaseNotify'] = config('firebase');
        $data['latestUser'] = User::latest()->limit(5)->get();
        $statistics['schedule'] = $this->dayList();
        $data['totalSalesAmount'] = $purchasePackageQuery->clone()->where('status', 1)->sum('price');
        $data['todaySalesAmount'] = $todaySalesAmount;
        $data['totalOrderCount'] = $purchasePackageQuery->clone()->where('status', 1)->count();
        $data['todayToYesterdaySalesAmountPercentage'] = ($yesterdaySalesAmount != 0) ? round(($todaySalesAmount - $yesterdaySalesAmount) / $yesterdaySalesAmount * 100, 2) : 0;
        $data['totalVisitorCount'] = $visitorQuery->clone()->count();
        $data['uniqueVisitorCount'] = $visitorQuery->clone()->get()->unique('ip_address')->count();
        $data['todayVisitorCount'] = $visitorQuery->clone()->whereDate('created_at', Carbon::today())->count();
        $data['yesterdayVisitorCount'] = $visitorQuery->clone()->whereDate('created_at', Carbon::yesterday())->count();
        return view('admin.dashboard-alternative', $data, compact("statistics"));
    }

    public function monthlyDepositWithdraw(Request $request)
    {
        $keyDataset = $request->keyDataset;

        $dailyDeposit = $this->dayList();

        Deposit::when($keyDataset == '0', function ($query) {
            $query->whereMonth('created_at', Carbon::now()->month);
        })
            ->when($keyDataset == '1', function ($query) {
                $lastMonth = Carbon::now()->subMonth();
                $query->whereMonth('created_at', $lastMonth->month);
            })
            ->select(
                DB::raw('SUM(payable_amount_in_base_currency) as totalDeposit'),
                DB::raw('DATE_FORMAT(created_at,"Day %d") as date')
            )
            ->groupBy(DB::raw("DATE(created_at)"))
            ->get()->map(function ($item) use ($dailyDeposit) {
                $dailyDeposit->put($item['date'], $item['totalDeposit']);
            });

        return response()->json([
            "totalDeposit" => currencyPosition($dailyDeposit->sum()),
            "dailyDeposit" => $dailyDeposit,
        ]);
    }

    public function saveToken(Request $request)
    {
        $admin = Auth::guard('admin')->user()
            ->fireBaseToken()
            ->create([
                'token' => $request->token,
            ]);
        return response()->json([
            'msg' => 'token saved successfully.',
        ]);
    }


    public function dayList()
    {
        $totalDays = Carbon::now()->endOfMonth()->format('d');
        $daysByMonth = [];
        for ($i = 1; $i <= $totalDays; $i++) {
            array_push($daysByMonth, ['Day ' . sprintf("%02d", $i) => 0]);
        }

        return collect($daysByMonth)->collapse();
    }

    protected function followupGrap($todaysRecords, $lastDayRecords = 0)
    {

        if (0 < $lastDayRecords) {
            $percentageIncrease = (($todaysRecords - $lastDayRecords) / $lastDayRecords) * 100;
        } else {
            $percentageIncrease = 0;
        }
        if ($percentageIncrease > 0) {
            $class = "bg-soft-success text-success";
        } elseif ($percentageIncrease < 0) {
            $class = "bg-soft-danger text-danger";
        } else {
            $class = "bg-soft-secondary text-body";
        }

        return [
            'class' => $class,
            'percentage' => round($percentageIncrease, 2)
        ];
    }


    public function chartUserRecords()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $userRecord = collect(User::selectRaw('COUNT(id) AS totalUsers')
            ->selectRaw('COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN id END) AS currentDateUserCount')
            ->selectRaw('COUNT(CASE WHEN DATE(created_at) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) THEN id END) AS previousDateUserCount')
            ->get()->makeHidden(['last-seen-activity', 'fullname'])
            ->toArray())->collapse();
        $followupGrap = $this->followupGrap($userRecord['currentDateUserCount'], $userRecord['previousDateUserCount']);

        $userRecord->put('followupGrapClass', $followupGrap['class']);
        $userRecord->put('followupGrap', $followupGrap['percentage']);

        $current_month_data = DB::table('users')
            ->select(DB::raw('DATE_FORMAT(created_at,"%e %b") as date'), DB::raw('count(*) as count'))
            ->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), $currentMonth)
            ->orderBy('created_at', 'asc')
            ->groupBy('date')
            ->get();

        $current_month_data_dates = $current_month_data->pluck('date');
        $current_month_datas = $current_month_data->pluck('count');
        $userRecord['chartPercentageIncDec'] = fractionNumber($userRecord['totalUsers'] - $userRecord['currentDateUserCount'], false);
        return response()->json(['userRecord' => $userRecord, 'current_month_data_dates' => $current_month_data_dates, 'current_month_datas' => $current_month_datas]);
    }

    public function chartTicketRecords()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $ticketRecord = collect(SupportTicket::selectRaw('COUNT(id) AS totalTickets')
            ->selectRaw('COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN id END) AS currentDateTicketsCount')
            ->selectRaw('COUNT(CASE WHEN DATE(created_at) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) THEN id END) AS previousDateTicketsCount')
            ->selectRaw('count(CASE WHEN status = 2  THEN status END) AS replied')
            ->selectRaw('count(CASE WHEN status = 1  THEN status END) AS answered')
            ->selectRaw('count(CASE WHEN status = 0  THEN status END) AS pending')
            ->get()
            ->toArray())->collapse();

        $followupGrap = $this->followupGrap($ticketRecord['currentDateTicketsCount'], $ticketRecord['previousDateTicketsCount']);
        $ticketRecord->put('followupGrapClass', $followupGrap['class']);
        $ticketRecord->put('followupGrap', $followupGrap['percentage']);

        $current_month_data = DB::table('support_tickets')
            ->select(DB::raw('DATE_FORMAT(created_at,"%e %b") as date'), DB::raw('count(*) as count'))
            ->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), $currentMonth)
            ->orderBy('created_at', 'asc')
            ->groupBy('date')
            ->get();

        $current_month_data_dates = $current_month_data->pluck('date');
        $current_month_datas = $current_month_data->pluck('count');
        $ticketRecord['chartPercentageIncDec'] = fractionNumber($ticketRecord['totalTickets'] - $ticketRecord['currentDateTicketsCount'], false);
        return response()->json(['ticketRecord' => $ticketRecord, 'current_month_data_dates' => $current_month_data_dates, 'current_month_datas' => $current_month_datas]);
    }

    public function chartKycRecords()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $kycRecords = collect(UserKyc::selectRaw('COUNT(id) AS totalKYC')
            ->selectRaw('COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN id END) AS currentDateKYCCount')
            ->selectRaw('COUNT(CASE WHEN DATE(created_at) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) THEN id END) AS previousDateKYCCount')
            ->selectRaw('count(CASE WHEN status = 0  THEN status END) AS pendingKYC')
            ->get()
            ->toArray())->collapse();
        $followupGrap = $this->followupGrap($kycRecords['currentDateKYCCount'], $kycRecords['previousDateKYCCount']);
        $kycRecords->put('followupGrapClass', $followupGrap['class']);
        $kycRecords->put('followupGrap', $followupGrap['percentage']);


        $current_month_data = DB::table('user_kycs')
            ->select(DB::raw('DATE_FORMAT(created_at,"%e %b") as date'), DB::raw('count(*) as count'))
            ->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), $currentMonth)
            ->orderBy('created_at', 'asc')
            ->groupBy('date')
            ->get();

        $current_month_data_dates = $current_month_data->pluck('date');
        $current_month_datas = $current_month_data->pluck('count');
        $kycRecords['chartPercentageIncDec'] = fractionNumber($kycRecords['totalKYC'] - $kycRecords['currentDateKYCCount'], false);
        return response()->json(['kycRecord' => $kycRecords, 'current_month_data_dates' => $current_month_data_dates, 'current_month_datas' => $current_month_datas]);
    }

    public function chartTransactionRecords()
    {
        $currentMonth = Carbon::now()->format('Y-m');

        $transaction = collect(Transaction::selectRaw('COUNT(id) AS totalTransaction')
            ->selectRaw('COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN id END) AS currentDateTransactionCount')
            ->selectRaw('COUNT(CASE WHEN DATE(created_at) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) THEN id END) AS previousDateTransactionCount')
            ->whereRaw('YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at) = MONTH(NOW())')
            ->get()
            ->toArray())
            ->collapse();

        $followupGrap = $this->followupGrap($transaction['currentDateTransactionCount'], $transaction['previousDateTransactionCount']);
        $transaction->put('followupGrapClass', $followupGrap['class']);
        $transaction->put('followupGrap', $followupGrap['percentage']);


        $current_month_data = DB::table('transactions')
            ->select(DB::raw('DATE_FORMAT(created_at,"%e %b") as date'), DB::raw('count(*) as count'))
            ->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), $currentMonth)
            ->orderBy('created_at', 'asc')
            ->groupBy('date')
            ->get();

        $current_month_data_dates = $current_month_data->pluck('date');
        $current_month_datas = $current_month_data->pluck('count');
        $transaction['chartPercentageIncDec'] = fractionNumber($transaction['totalTransaction'] - $transaction['currentDateTransactionCount'], false);
        return response()->json(['transactionRecord' => $transaction, 'current_month_data_dates' => $current_month_data_dates, 'current_month_datas' => $current_month_datas]);
    }


//    public function chartLoginHistory()
//    {
//        $userLoginsData = DB::table('user_logins')
//            ->whereDate('created_at', '>=', now()->subDays(30))
//            ->select('browser', 'os', 'get_device')
//            ->get();
//
//        $userLoginsBrowserData = $userLoginsData->groupBy('browser')->map->count();
//        $data['browserKeys'] = $userLoginsBrowserData->keys();
//        $data['browserValue'] = $userLoginsBrowserData->values();
//
//        $userLoginsOSData = $userLoginsData->groupBy('os')->map->count();
//        $data['osKeys'] = $userLoginsOSData->keys();
//        $data['osValue'] = $userLoginsOSData->values();
//
//        $userLoginsDeviceData = $userLoginsData->groupBy('get_device')->map->count();
//        $data['deviceKeys'] = $userLoginsDeviceData->keys();
//        $data['deviceValue'] = $userLoginsDeviceData->values();
//
//        return response()->json(['loginPerformance' => $data]);
//    }


    public function chartBrowserHistory(Request $request)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        $userLoginsData = DB::table('user_logins')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('browser', 'os', 'get_device')
            ->get();

        $userLoginsBrowserData = $userLoginsData->groupBy('browser')->map->count();
        $data['browserKeys'] = $userLoginsBrowserData->keys();
        $data['browserValue'] = $userLoginsBrowserData->values();

        return response()->json(['browserPerformance' => $data]);
    }

    public function chartOsHistory(Request $request)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        $userLoginsData = DB::table('user_logins')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('browser', 'os', 'get_device')
            ->get();

        $userLoginsOSData = $userLoginsData->groupBy('os')->map->count();
        $data['osKeys'] = $userLoginsOSData->keys();
        $data['osValue'] = $userLoginsOSData->values();

        return response()->json(['osPerformance' => $data]);
    }

    public function chartDeviceHistory(Request $request)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        $userLoginsData = DB::table('user_logins')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('browser', 'os', 'get_device')
            ->get();

        $userLoginsDeviceData = $userLoginsData->groupBy('get_device')->map->count();
        $data['deviceKeys'] = $userLoginsDeviceData->keys();
        $data['deviceValue'] = $userLoginsDeviceData->values();

        return response()->json(['deviceHistory' => $data]);
    }


    public function getSalesRevenueHistory(Request $request)
    {
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // grouping type based on date range
        $diffInDays = $startDate->diffInDays($endDate);
        $previousStartDate = $startDate->copy()->subDays($diffInDays + 1);
        $previousEndDate = $endDate->copy()->subDays($diffInDays + 1);
        $groupBy = $diffInDays <= 1 ? 'HOUR' : 'DATE';
        $dateFormat = $diffInDays <= 1 ? 'ga' : 'd-M';

        $salesData = PurchasePackage::with(['deposit'])
            ->whereHas('deposit', function ($query) {
                $query->where('status', 1);
            })
            ->where('purchase_from', $request->store)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw($groupBy . '(created_at) as period,
                 COUNT(*) as item_count,
                 SUM(price) as total')
            ->groupBy('period')
            ->get();

        $labels = [];
        $revenueData = [];
        $ordersData = [];


        foreach ($salesData as $data) {
            $period = $data->period;
            $formattedPeriod = $diffInDays <= 1 ? date($dateFormat, strtotime($period . ':00')) : date($dateFormat, strtotime($period));
            $labels[] = $formattedPeriod;
            $revenueData[] = $data->total;
            $ordersData[] = $data->item_count;
        }

        $currentPeriodSalesData = PurchasePackage::with(['deposit'])
            ->whereHas('deposit', function ($query) {
                $query->where('status', 1);
            })
            ->where('purchase_from', $request->store)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('SUM(price) as total,
             COUNT(*) as item_count')
            ->first();

        $previousPeriodSalesData = PurchasePackage::with(['deposit'])
            ->whereHas('deposit', function ($query) {
                $query->where('status', 1);
            })
            ->where('purchase_from', $request->store)
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->selectRaw('SUM(price) as total,
             COUNT(*) as item_count')
            ->first();

        $revenuePercentageChange = '0%';
        $revenuePercentageValue = $currentPeriodSalesData->total - $previousPeriodSalesData->total;
        $orderPercentageChange = '0%';
        $orderPercentageValue = $currentPeriodSalesData->item_count - $previousPeriodSalesData->item_count;

        if ($previousPeriodSalesData->total > 0) {
            $revenuePercentageChangeValue = round((($currentPeriodSalesData->total - $previousPeriodSalesData->total) / $previousPeriodSalesData->total) * 100, 2);
            $revenuePercentageChange = $revenuePercentageChangeValue . '%';
        }
        if ($previousPeriodSalesData->item_count > 0) {
            $orderPercentageChangeValue = round((($currentPeriodSalesData->item_count - $previousPeriodSalesData->item_count) / $previousPeriodSalesData->item_count) * 100, 2);
            $orderPercentageChange = $orderPercentageChangeValue . '%';
        }

        $response = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $revenueData,
                    'backgroundColor' => '#377dff',
                    'hoverBackgroundColor' => '#377dff',
                    'borderColor' => '#377dff',
                    'maxBarThickness' => 10,
                ],
                [
                    'label' => 'Sales',
                    'data' => $ordersData,
                    'backgroundColor' => '#e7eaf3',
                    'borderColor' => '#e7eaf3',
                    'maxBarThickness' => 10,
                ],
            ],
            'revenuePercentageChange' => $revenuePercentageChange,
            'revenuePercentageValue' => $revenuePercentageValue,
            'orderPercentageChange' => $orderPercentageChange,
            'orderPercentageValue' => $orderPercentageValue,
        ];
        return response()->json($response);
    }


    public function totalSalesHistory()
    {
        $orderQuery = PurchasePackage::query()->with(['deposit']);

        $todayOrders = $orderQuery->clone()->whereHas('deposit', function ($query) {
            $query->where('status', 1);
        })
            ->selectRaw('HOUR(created_at) as hour, SUM(price) as total')
            ->whereDate('created_at', Carbon::today())
            ->groupBy('hour')
            ->pluck('price', 'hour')
            ->toArray();
        $yesterdayOrders = $orderQuery->clone()->whereHas('deposit', function ($query) {
            $query->where('status', 1);
        })
            ->selectRaw('HOUR(created_at) as hour, SUM(price) as total')
            ->whereDate('created_at', Carbon::yesterday())
            ->groupBy('hour')
            ->pluck('price', 'hour')
            ->toArray();
        $labels = $this->getFormattedHours();

        $todayOrdersByHour = [];
        $yesterdayOrdersByHour = [];

        foreach ($labels as $hourLabel) {
            $hour = Carbon::createFromFormat('ga', $hourLabel)->format('G');
            $todayOrdersByHour[] = $todayOrders[$hour] ?? 0;
            $yesterdayOrdersByHour[] = $yesterdayOrders[$hour] ?? 0;
        }
        return response()->json([
            'labels' => $labels,
            'dataSet1' => $todayOrdersByHour,
            'dataSet2' => $yesterdayOrdersByHour,
        ]);
    }

    public function visitorsHistory()
    {
        $todayVisitors = Visitor::selectRaw('HOUR(created_at) as hour, COUNT(*) as visitors_count')
            ->whereDate('created_at', Carbon::today())
            ->groupBy('hour')
            ->pluck('visitors_count', 'hour')
            ->toArray();
        $yesterdayVisitors = Visitor::selectRaw('HOUR(created_at) as hour, COUNT(*) as visitors_count')
            ->whereDate('created_at', Carbon::yesterday())
            ->pluck('visitors_count', 'hour')
            ->toArray();

        $labels = $this->getFormattedHours();

        $todayVisitorsByHour = [];
        $yesterdayVisitorsByHour = [];

        foreach ($labels as $hourLabel) {
            $hour = Carbon::createFromFormat('ga', $hourLabel)->format('G');
            $todayVisitorsByHour[] = $todayVisitors[$hour] ?? 0;
            $yesterdayVisitorsByHour[] = $yesterdayVisitors[$hour] ?? 0;

        }

        return response()->json([
            'labels' => $labels,
            'dataSet1' => $todayVisitorsByHour,
            'dataSet2' => $yesterdayVisitorsByHour,
        ]);
    }

    public function forbidden()
    {
        return view('admin.errors.403');
    }


    private function getFormattedHours()
    {
        $formattedHours = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $formattedHours[] = Carbon::createFromTime($hour, 0)->format('ga');
        }
        return $formattedHours;
    }


}
