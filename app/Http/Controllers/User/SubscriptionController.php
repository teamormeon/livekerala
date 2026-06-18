<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Fund;
use App\Models\Gateway;
use App\Models\Page;
use App\Models\Plan;
use App\Models\PurchasePackage;
use App\Models\SubscriptionPurchase;
use App\Traits\PaymentValidationCheck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Stevebauman\Purify\Facades\Purify;

class SubscriptionController extends Controller
{
    use PaymentValidationCheck;

    public function subsConfirm(Request $request, $trx_id)
    {
        $deposit = Deposit::with('user', 'depositable')->where(['trx_id' => $trx_id, 'status' => 0])->first();
        $gateway = Gateway::findOrFail($deposit->payment_method_id);

        $pageSeo = Page::where('template_name', getTheme())->where('slug', 'pricing-payment')->first();
        $pageSeo['breadcrumb_image'] = $pageSeo?->breadcrumb_status == 1 ?  getFile($pageSeo->breadcrumb_image_driver, $pageSeo->breadcrumb_image) : null;

            try {
                if ($gateway->code == 'square') {
                    $getwayObj = 'App\\Services\\Subscription\\' . $gateway->code . '\\Payment';
                    $data = $getwayObj::createSubscription($deposit, $gateway, $request);

                    if ($data['status'] == 'success') {
                        if (isset($data['redirect_url'])) {
                            return redirect()->away($data['redirect_url']);
                        }
                        return redirect()->route('success');
                    } else {
                        return back()->with('error', 'Invalid Payment');
                    }
                }
            } catch (\Exception $exception) {
                return back()->with('error', $exception->getMessage());
            }

        if ($request->method() == "GET") {
            return view('user_panel.user.subscription.payment-method.' . $gateway->code, compact('gateway', 'deposit','pageSeo'));
        } elseif ($request->method() == 'POST') {
            try {
                $getwayObj = 'App\\Services\\Subscription\\' . $gateway->code . '\\Payment';
                $data = $getwayObj::createSubscription($deposit, $gateway, $request);

                if ($data['status'] == 'success') {
                    if (isset($data['redirect_url'])) {
                        return redirect()->away($data['redirect_url']);
                    }
                    return redirect()->route('success');
                } else {
                    return back()->with('error', 'Invalid Payment');
                }
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }
    }

    public function subscriptionIpn(Request $request, $code, $trx_id = null)
    {

        try {
            $gateway = Gateway::where('code', $code)->first();
            if (!$gateway) throw new \Exception('Invalid Payment Gateway.');

            if (isset($trx_id)) {
                $deposit = Deposit::with('user')->where('trx_id', $trx_id)->first();
                if (!$deposit) throw new \Exception('Invalid Payment Request.');
            }
            $getwayObj = 'App\\Services\\Subscription\\' . $code . '\\Payment';
            $data = $getwayObj::ipn($request, $gateway, @$deposit, @$trx_id);

        } catch (\Exception $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }


    public function subsCancel($id)
    {
        $subscriptionPurchase = PurchasePackage::where('user_id', auth()->id())->findOrFail($id);

        try {
            $code = $subscriptionPurchase->gateway->code;
            $getwayObj = 'App\\Services\\Subscription\\' . $code . '\\Payment';
            $data = $getwayObj::cancelSubscription($subscriptionPurchase);
            if ($data['status'] == 'success') {
                $subscriptionPurchase->status = 2;
                $subscriptionPurchase->deleted_at = Carbon::now();
                $subscriptionPurchase->save();
                return back()->with('success', 'Your subscription has been canceled');
            } else {
                return back()->with('error', 'You can not cancel subscription');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong. Please try again');
        }
    }
}
