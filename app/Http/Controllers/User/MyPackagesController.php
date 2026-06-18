<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\PurchasePackage;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyPackagesController extends Controller
{
    public function myPackages(Request $request, $id = null)
    {
        $package_name = $request->name;
        $current_date = Carbon::parse(now()->format('Y-m-d'));
        $purchase_date = Carbon::parse($request->purchase_date);
        $expire_date = Carbon::parse($request->expire_date);
        $status = $request->status;

        $data['my_packages'] = PurchasePackage::with('get_package','get_package.details')
            ->when(isset($id), function ($q) use ($id) {
                $q->where('id', $id);
            })
            ->when(isset($request->name), function ($query) use ($package_name) {
                return $query->whereHas('get_package.details', function ($q) use ($package_name) {
                    $q->where('title', 'Like', '%' . $package_name . '%');
                });
            })
            ->when(isset($request->purchase_date), function ($query) use ($purchase_date) {
                $query->where('purchase_date', $purchase_date);
            })
            ->when(isset($request->expire_date), function ($query) use ($expire_date) {
                $query->where('expire_date', $expire_date);
            })
            ->when($request->package_status == 'active', function ($query) use ($current_date) {
                $query->whereNull('expire_date')->orWhere('expire_date', '>=', $current_date);
            })
            ->when($request->package_status == 'expired', function ($query) use ($current_date) {
                $query->where('expire_date', '<', $current_date);
            })
            ->when(isset($request->status), function ($q4) use ($status){
                return $q4->where('status', $status);
            })
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(basicControl()->paginate);
        return view('user_panel.user.package.index', $data);
    }


    public function paymentHistory(Request $request, $id)
    {
        $search = $request->all();
        $dateSearch = Carbon::parse($request->datetrx);
        $purchasePackage = PurchasePackage::select('id','user_id','package_id','deposit_id')
            ->where('user_id', Auth::id())->findOrFail($id);

        $allTransaction = Deposit::where('depositable_id', $purchasePackage->package_id)
            ->where('user_id', Auth::id())
            ->where('status', 1)
            ->when(@$search['transaction_id'], function ($query) use ($search) {
                return $query->where('trx_id', 'LIKE', "%{$search['transaction_id']}%");
            })
            ->when(@$search['remark'], function ($query) use ($search) {
                return $query->where('purchase_type', 'LIKE', "%{$search['remark']}%");
            })
            ->when(@$search['datetrx'], function ($query) use ($dateSearch) {
                return $query->whereDate("created_at", $dateSearch);
            })
            ->orderBy('id', 'DESC')->latest()->paginate(basicControl()->paginate);
        return view('user_panel.user.package.payment_history', compact( 'purchasePackage', 'allTransaction'));
    }

}
