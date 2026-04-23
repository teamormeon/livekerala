<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransactionController extends Controller
{
    public function transaction(Request $request)
    {
        $search = $request->all();
        $dateSearch = Carbon::parse($request->datetrx);
        $data['transactions'] = Transaction::where('user_id', auth()->id())
            ->when(@$search['transaction_id'], function ($query) use ($search) {
                return $query->where('trx_id', 'LIKE', "%{$search['transaction_id']}%");
            })
            ->when(@$search['remark'], function ($query) use ($search) {
                return $query->where('remarks', 'LIKE', "%{$search['remark']}%");
            })
            ->when(@$search['datetrx'], function ($query) use ($dateSearch) {
                return $query->whereDate("created_at", $dateSearch);
            })
            ->orderBy('created_at', 'desc')
            ->latest()->paginate(basicControl()->paginate);
        return view('user_panel.user.transaction.index', $data);
    }
}
