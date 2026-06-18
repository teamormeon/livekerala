<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransactionController extends Controller
{
    use ApiResponse;

    public function transaction(Request $request)
    {
        $search = $request->all();
        $dateSearch = Carbon::parse($request->date);
        $transactions = Transaction::with([
                'deposit:id,depositable_id,depositable_type,payment_method_id,user_id,status',
                'deposit.depositable:id',
                'deposit.depositable.details:id,package_id,title',
                'deposit.gateway:id,name'
            ])->where('user_id', auth()->id())
            ->whereHas('deposit', function ($query) {
                $query->where('status', 1);
            })
            ->select('id','transactional_id','transactional_id','amount','charge','trx_id','remarks','created_at')
            ->when(@$search['transaction_id'], function ($query) use ($search) {
                return $query->where('trx_id', 'LIKE', "%{$search['transaction_id']}%");
            })
            ->when(@$search['remark'], function ($query) use ($search) {
                return $query->where('remarks', 'LIKE', "%{$search['remark']}%");
            })
            ->when(@$search['date'], function ($query) use ($dateSearch) {
                return $query->whereDate("created_at", $dateSearch);
            })
            ->orderBy('created_at', 'desc')
            ->latest()->paginate(basicControl()->paginate);

        $formatedTransactions = $transactions->getCollection()->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'transactional_id' => $transaction->transactional_id,
                'gateway_id' => $transaction->deposit->gateway->id,
                'gateway_name' => $transaction->deposit->gateway->name,
                'amount' => $transaction->amount,
                'charge' => (float) $transaction->charge,
                'trx_id' => $transaction->trx_id,
                'purchase_title' => $transaction->deposit->depositable->details->title,
                'created_at' => $transaction->created_at,
            ];
        });

        $transactions->setCollection($formatedTransactions);
        return response()->json($this->withSuccess($transactions));
    }
}
