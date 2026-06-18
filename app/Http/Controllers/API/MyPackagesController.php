<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Package;
use App\Models\PurchasePackage;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyPackagesController extends Controller
{
    use ApiResponse;
    public function paymentHistory(Request $request, $id)
    {
        $search = $request->all();
        $dateSearch = Carbon::parse($request->date);
        $purchasePackage = PurchasePackage::with(['get_package:id','get_package.details:id,package_id,title'])->select('id','user_id','package_id','deposit_id')->where('user_id', Auth::id())->find($id);

        $allTransaction = Deposit::with(['gateway:id,name,image,driver'])
            ->select('id','depositable_id','depositable_type','user_id','payment_method_id','amount','trx_id','status','purchase_type','created_at')
            ->where('depositable_id', $purchasePackage->package_id)
            ->where('user_id', Auth::id())
            ->where('status', 1)
            ->when(@$search['transaction_id'], function ($query) use ($search) {
                return $query->where('trx_id', 'LIKE', "%{$search['transaction_id']}%");
            })
            ->when(@$search['remark'], function ($query) use ($search) {
                return $query->where('purchase_type', 'LIKE', "%{$search['remark']}%");
            })
            ->when(@$search['date'], function ($query) use ($dateSearch) {
                return $query->whereDate("created_at", $dateSearch);
            })
            ->orderBy('id', 'DESC')->latest()->paginate(basicControl()->paginate);

        $formatedAllTransaction = $allTransaction->getCollection()->map(function ($item) use ($purchasePackage) {
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'payment_method_id' => $item->payment_method_id,
                'payment_method_name' => $item->gateway->name,
                'payment_method_image' => getFile($item->gateway->driver, $item->gateway->image),
                'amount' => $item->amount,
                'trx_id' => $item->trx_id,
                'purchase_type' => $item->purchase_type,
                'purchase_item_name' => $purchasePackage->get_package->details->title,
                'remark' => $item->purchase_type.' '.$purchasePackage->get_package->details->title,
                'status' => $item->status,
                'created_at' => $item->created_at,
            ];
        });
        $allTransaction->setCollection($formatedAllTransaction);

        $info = [
            'status' => '0 = Pending, 1 = Completed, 2 = Requested, 3 = Rejected',
        ];
        return response()->json($this->withSuccess($allTransaction, $info));
    }
}
