<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\Package;
use App\Models\Page;
use App\Models\PurchasePackage;
use App\Traits\ApiResponse;
use App\Traits\Notify;
use App\Traits\PaymentValidationCheck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PricingController extends Controller
{
    use ApiResponse, Notify, PaymentValidationCheck;

    public function packages(Request $request)
    {
        $packages = Package::with(['details:id,package_id,language_id,title'])
            ->where('status', 1)
            ->orderBy('price', 'ASC')->get();

        $formatedPackages = $packages->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => optional($item->details)->title,
                'price' => $item->price,
                'image' => getFile($item->driver, $item->image),
                'is_free_purchase' => $item->isFreePurchase(),
                'gateway_plan_id' => $item->gateway_plan_id,
                'is_multiple_time_purchase' => $item->is_multiple_time_purchase,
                'expiry_time' => $item->expiry_time,
                'expiry_time_type' => $item->expiry_time_type,
                'no_of_listing' => $item->no_of_listing,
                'is_image' => $item->is_image,
                'no_of_img_per_listing' => $item->no_of_img_per_listing,
                'no_of_categories_per_listing' => $item->no_of_categories_per_listing,
                'is_product' => $item->is_product,
                'no_of_product' => $item->no_of_product,
                'no_of_img_per_product' => $item->no_of_img_per_product,
                'is_video' => $item->is_video,
                'is_amenities' => $item->is_amenities,
                'no_of_amenities_per_listing' => $item->no_of_amenities_per_listing,
                'is_business_hour' => $item->is_business_hour,
                'seo' => $item->seo,
                'is_messenger' => $item->is_messenger,
                'is_whatsapp' => $item->is_whatsapp,
                'dynamic_from' => $item->is_create_from,
                'is_renew' => $item->is_renew,
                'status' => $item->status,
                'created_at' => $item->created_at,
            ];
        });

        $info = [
            'is_free_purchase' => 'true = Already Purchased, false = Start Free Purchase',
            'status' => '0 = Inactive, 1 = Active',
            'condition' => 'follow condition-03 into condition.php file',
        ];
        return response()->json($this->withSuccess($formatedPackages, $info));
    }

    public function pricingPlanPayment($id, $type = null, $purchase_id = null){
        $data['plan_id'] = $id;
        $data['purchase_id'] = $purchase_id;
        $package = Package::with('details')->where('status',1)->find($id);

        if (!$package){
            return response()->json($this->withError('Package not found'));
        }

        if ($package->isFreePurchase() == 'true' && $type != 'renew'){
            return response()->json($this->withError('This package already purchased'));
        }

        if ($type == 'renew') {
            return $this->handleRenewal($package, $data, $purchase_id);
        }
        return $this->handlePurchase($package, $data);
    }


    protected function handlePurchase($package, $data)
    {
        if ($package->price == null || $package->price == 0) {
            $purchasePackage = new PurchasePackage();
            $this->updatePurchasePackage($purchasePackage, $package, 'Purchase');
            $this->sendNotification($package, 'PURCHASE_PACKAGE_BY_USER');
            return response()->json($this->withSuccess(optional($package->details)->title.' package has been purchased'));
        }
        return $this->showPaymentPage($package, $data);
    }

    protected function handleRenewal($package, $data, $purchase_id)
    {
        $existingPurchasePackage = PurchasePackage::where('user_id', Auth::id())
            ->where('id', $purchase_id)
            ->where('package_id', $package->id)
            ->first();
        if (!$existingPurchasePackage || $existingPurchasePackage->is_renew != 1) {
            return response()->json($this->withError('No previous purchase found for this package'));
        }
        if ($existingPurchasePackage->price == null || $existingPurchasePackage->price == 0) {
            $this->updatePurchasePackage($existingPurchasePackage, $package, 'Renew');
            $this->sendNotification($package, 'RENEWED_PACKAGE_BY_USER');
            return response()->json($this->withSuccess(optional($package->details)->title.' package has been Renewed'));
        }
        return $this->showPaymentPage($package, $data, 'renew');
    }

    protected function updatePurchasePackage($purchasePackage, $package, $type)
    {
        $purchasePackage->fill([
            'user_id' => Auth::id(),
            'package_id' => $package->id,
            'price' => $package->price,
            'is_renew' => $package->is_renew,
            'is_image' => $package->is_image,
            'is_video' => $package->is_video,
            'is_amenities' => $package->is_amenities,
            'is_product' => $package->is_product,
            'is_create_from' => $package->is_create_from,
            'is_business_hour' => $package->is_business_hour,
            'no_of_listing' => $package->no_of_listing,
            'no_of_img_per_listing' => $package->no_of_img_per_listing,
            'no_of_categories_per_listing' => $package->no_of_categories_per_listing,
            'no_of_amenities_per_listing' => $package->no_of_amenities_per_listing,
            'no_of_product' => $package->no_of_product,
            'no_of_img_per_product' => $package->no_of_img_per_product,
            'seo' => $package->seo,
            'is_whatsapp' => $package->is_whatsapp,
            'is_messenger' => $package->is_messenger,
            'status' => 1,
            'type' => $type,
            'purchase_date' => Carbon::now(),
            'expire_date' => $this->getExpiryDate($package)
        ]);
        $purchasePackage->save();
    }

    protected function getExpiryDate($package)
    {
        if ($package->expiry_time_type == 'Days' || $package->expiry_time_type == 'Day') {
            return Carbon::now()->addDays($package->expiry_time);
        } elseif ($package->expiry_time_type == 'Months' || $package->expiry_time_type == 'Month') {
            return Carbon::now()->addMonths($package->expiry_time);
        } elseif ($package->expiry_time_type == 'Years' || $package->expiry_time_type == 'Year') {
            return Carbon::now()->addYears($package->expiry_time);
        }
        return null;
    }
    protected function sendNotification($package, $notificationType)
    {
        $senderName = auth()->user()->firstname . ' ' . auth()->user()->lastname;
        $senderImg = getFile(auth()->user()->image_driver, auth()->user()->image);
        $msg = [
            'package' => optional($package->details)->title,
            'from'    => $senderName,
        ];
        $action = [
            "link" => route('admin.purchase.package'),
            "image" => $senderImg,
            "icon" => "fa fa-money-bill-alt text-white"
        ];
        $this->adminPushNotification($notificationType, $msg, $action);
    }

    protected function showPaymentPage($package, $data, $type = null)
    {
        $data['type'] = $type;
        $data['package'] = $package;
        return response()->json($this->withSuccess($data));
    }

    public function supportedCurrency(Request $request)
    {
        try {
            $gateway = Gateway::where('id', $request->gateway_id)->first();
            if (!$gateway){
                return response()->json($this->withError('Gateway not found'));
            }

            $pmCurrency =  $gateway->receivable_currencies[0]->name ?? $gateway->receivable_currencies[0]->currency;
            $isCrypto = $gateway->id < 1000 && checkTo($gateway->currencies, $pmCurrency) == 1;

            $data = [
                'supported_currency' => $gateway->supported_currency,
                'currencyType' => $isCrypto ? 0 : 1,
            ];
            return response()->json($this->withSuccess($data));
        } catch (\Exception $e) {
            return response()->json($this->withError($e->getMessage()));
        }
    }

    public function paymentConvertAmount(Request $request)
    {
        $amount = $request->amount;
        $selectedCurrency = $request->selected_currency;
        $selectGateway = $request->selected_gateway;
        $selectedCryptoCurrency = $request->selectedCryptoCurrency;

        $data = $this->checkConvertAmountValidate($amount, $selectedCurrency, $selectGateway, $selectedCryptoCurrency);
        return response()->json($this->withSuccess($data));
    }

    public function paymentRequest(Request $request)
    {
        $package = Package::with('details')->where('status', 1)->find($request->plan_id);
        if(!$package){
            return response()->json($this->withError('Package not found'));
        }

        $amount = $request->cvt_amount;
        $gateway = $request->gateway_id;
        $currency = $request->supported_currency;
        $cryptoCurrency= $request->supported_crypto_currency;

        try {
            $checkAmountValidate = $this->validationCheck($amount, $gateway, $currency, $cryptoCurrency);

            if ($checkAmountValidate['status'] == 'error') {
                return back()->with('error', $checkAmountValidate['msg']);
            }

            $method = Gateway::where('status', 1)->find($gateway);
            if (!$method){
                return response()->json($this->withError('Gateway not found'));
            }

            $deposit = Deposit::create([
                'user_id' => Auth::user()->id,
                'depositable_type' => Package::class,
                'depositable_id' => $package->id,
                'payment_method_id' => $checkAmountValidate['data']['gateway_id'],
                'payment_method_currency' => $checkAmountValidate['data']['currency'],
                'amount' => $amount,
                'percentage_charge' => $checkAmountValidate['data']['percentage_charge'],
                'fixed_charge' => $checkAmountValidate['data']['fixed_charge'],
                'payable_amount' => $checkAmountValidate['data']['payable_amount'],
                'base_currency_charge' => $checkAmountValidate['data']['base_currency_charge'],
                'payable_amount_in_base_currency' => $checkAmountValidate['data']['payable_amount_base_in_currency'],
                'trx_id' => strRandom(),
                'status' => 0,
                'purchase_type' => $request->type ?? 'purchase',
                'purchase_id' => $request->purchase_id ?? null,
            ]);

            $trx_id = $deposit->trx_id;
            $redirectUrl = $method->subscription_on ? route('user.subscription.process', $deposit->trx_id) : route('payment.process', $deposit->trx_id);
            $data = [
                'trx_id' => $trx_id,
                'redirect_url' => $redirectUrl,
            ];
            return response()->json($this->withSuccess($data));
        } catch (\Exception $e) {
            return response()->json($this->withError($e->getMessage()));
        }
    }

    public function checkConvertAmountValidate($amount, $selectedCurrency, $selectGateway, $selectedCryptoCurrency = null)
    {
        $selectGateway = Gateway::where('id', $selectGateway)->where('status', 1)->first();
        if (!$selectGateway) {
            return response()->json($this->withError(['status' => false, 'message' => "Payment method not available for this transaction"]));
        }

        if ($selectGateway->currency_type == 1) {
            $selectedCurrency = array_search($selectedCurrency, $selectGateway->supported_currency);
            if ($selectedCurrency !== false) {
                $selectedPayCurrency = $selectGateway->supported_currency[$selectedCurrency];
            } else {
                return response()->json($this->withError(['status' => false, 'message' => "Please choose the currency you'd like to use for payment"]));
            }
        }

        if ($selectGateway->currency_type == 0) {
            $selectedCurrency = array_search($selectedCryptoCurrency, $selectGateway->supported_currency);
            if ($selectedCurrency !== false) {
                $selectedPayCurrency = $selectGateway->supported_currency[$selectedCurrency];
            } else {
                return response()->json($this->withError(['status' => false, 'message' => "Please choose the currency you'd like to use for payment"]));
            }
        }

        if ($selectGateway) {
            $receivableCurrencies = $selectGateway->receivable_currencies;
            if (is_array($receivableCurrencies)) {
                if ($selectGateway->id < 999) {
                    $currencyInfo = collect($receivableCurrencies)->where('name', $selectedPayCurrency)->first();
                } else {
                    if ($selectGateway->currency_type == 1) {
                        $currencyInfo = collect($receivableCurrencies)->where('currency', $selectedPayCurrency)->first();
                    } else {
                        $currencyInfo = collect($receivableCurrencies)->where('currency', $selectedCryptoCurrency)->first();
                    }
                }
            } else {
                return null;
            }
        }

        $currencyType = $selectGateway->currency_type;
        $limit = $currencyType == 0 ? 8 : 2;
        $amount = getAmount($amount * $currencyInfo->conversion_rate, $limit);
        $status = false;

        if ($currencyInfo) {
            $percentage = getAmount($currencyInfo->percentage_charge, $limit);
            $percentage_charge = getAmount(($amount * $percentage) / 100, $limit);
            $fixed_charge = getAmount($currencyInfo->fixed_charge, $limit);
            $min_limit = getAmount($currencyInfo->min_limit, $limit);
            $max_limit = getAmount($currencyInfo->max_limit, $limit);
            $charge = getAmount($percentage_charge + $fixed_charge, $limit);
        }

        $basicControl = basicControl();
        $payable_amount = getAmount($amount + $charge, $limit);
        $amount_in_base_currency = getAmount($payable_amount / $currencyInfo->conversion_rate ?? 1, $limit);

        if ($amount < $min_limit || $amount > $max_limit) {
            $message = "minimum payment $min_limit and maximum payment limit $max_limit";
        } else {
            $status = true;
            $message = "Amount : $amount" . " " . $selectedPayCurrency;
        }

        $data['status'] = $status;
        $data['message'] = $message;
        $data['fixed_charge'] = $fixed_charge;
        $data['percentage'] = $percentage;
        $data['percentage_charge'] = $percentage_charge;
        $data['min_limit'] = $min_limit;
        $data['max_limit'] = $max_limit;
        $data['payable_amount'] = $payable_amount;
        $data['charge'] = $charge;
        $data['amount'] = $amount;
        $data['conversion_rate'] = $currencyInfo->conversion_rate ?? 1;
        $data['amount_in_base_currency'] = number_format($amount_in_base_currency, 2);
        $data['currency'] = ($selectGateway->currency_type == 1) ? ($currencyInfo->name ?? $currencyInfo->currency) : "USD";
        $data['base_currency'] = $basicControl->base_currency;
        $data['currency_limit'] = $limit;
        return $data;
    }
}
