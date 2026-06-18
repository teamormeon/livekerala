<?php

namespace App\Services;


use App\Models\Fund;
use App\Models\Package;
use App\Models\PurchasePackage;
use App\Models\Transaction;
use App\Traits\Notify;
use Carbon\Carbon;
use GPBMetadata\Google\Api\Auth;

class BasicService
{
    use Notify;

    public function setEnv($value)
    {
        $envPath = base_path('.env');
        $env = file($envPath);
        foreach ($env as $env_key => $env_value) {
            $entry = explode("=", $env_value, 2);
            $env[$env_key] = array_key_exists($entry[0], $value) ? $entry[0] . "=" . $value[$entry[0]] . "\n" : $env_value;
        }
        $fp = fopen($envPath, 'w');
        fwrite($fp, implode($env));
        fclose($fp);
    }

    public function preparePaymentUpgradation($deposit)
    {
        try {
            $deposit->save();
            $user = $deposit->user;
            if($deposit->depositable_type == Package::class && ($deposit->status == 0 || $deposit->status == 2)){
                $user = $deposit->user;
                $amount = $deposit->payable_amount_in_base_currency;
                $charge = getAmount($deposit->base_currency_charge);
                $balance = getAmount($user->balance);
                $trx_type = '+';
                $trx_id = $deposit->trx_id;
                $remarks = 'payment Via ' . optional($deposit->gateway)->name;
                $transactional_id = $deposit->id;
                $transactional_type = $deposit->depositable_type;
                $this->makeTransaction($user, $amount, $charge, $balance, $trx_type, $trx_id, $remarks, $transactional_id, $transactional_type);

                $package = Package::with('details')->where('status', 1)->findOrFail($deposit->depositable_id);
                if ($package){
                    $purchasePackage = $deposit->purchase_type == 'renew'
                        ? PurchasePackage::where('user_id', $deposit->user_id)
                            ->where('id', $deposit->purchase_id)
                            ->where('package_id', $package->id)
                            ->first()
                        : new PurchasePackage();

                    $purchasePackage->fill([
                        'user_id' => $user->id,
                        'package_id' => $package->id,
                        'trx_id' => $deposit->trx_id,
                        'deposit_id' => $deposit->id,
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
                        'purchase_date' => Carbon::now(),
                        'type' => $deposit->purchase_type == 'renew' ? 'Renew' : 'Purchase',
                        'status' => 1,
                        'expire_date' => match ($package->expiry_time_type) {
                            'Day', 'Days' => Carbon::now()->addDays((int)$package->expiry_time),
                            'Month', 'Months' => Carbon::now()->addMonths((int)$package->expiry_time),
                            'Year', 'Years' => Carbon::now()->addYears((int)$package->expiry_time),
                            default => null,
                        }
                    ]);
                    $purchasePackage->save();
                }else{
                    return back()->with('error','Plan Not Available.');
                }
                $params = [
                    'user' => optional($deposit->user)->username,
                    'amount' => currencyPosition($deposit->payable_amount_in_base_currency),
                    'topic' => 'Package',
                    'transaction' => $deposit->trx_id,
                ];
                $actionAdmin = [
                    "name" => optional($deposit->user)->firstname . ' ' . optional($deposit->user)->lastname,
                    "image" => getFile(optional($deposit->user)->image_driver, optional($deposit->user)->image),
                    "link" => route('admin.payment.log'),
                    "icon" => "fas fa-list text-white"
                ];
                $this->adminMail('PAYMENT_PACKAGE_BY_USER', $params, $actionAdmin);
                $this->adminPushNotification('PAYMENT_PACKAGE_BY_USER', $params, $actionAdmin);
                $this->adminFirebasePushNotification('PAYMENT_PACKAGE_BY_USER', $params);
            }
        } catch (\Exception $e) {
        }
        return true;
    }

    public function subscriptionUpgrade($deposit)
    {
        if ($deposit->depositable_type == Package::class) {
            $user = $deposit->user;
            $amount = $deposit->payable_amount_in_base_currency;
            $charge = getAmount($deposit->base_currency_charge);
            $balance = getAmount($user->balance);
            $trx_type = '+';
            $trx_id = $deposit->trx_id;
            $remarks = 'payment Via ' . optional($deposit->gateway)->name;
            $transactional_id = $deposit->id;
            $transactional_type = $deposit->depositable_type;
            $this->makeTransaction($user, $amount, $charge, $balance, $trx_type, $trx_id, $remarks, $transactional_id, $transactional_type);

            $package = Package::with('details')->where('status', 1)->findOrFail($deposit->depositable_id);
            if ($package){
                $purchasePackage = $deposit->purchase_type == 'renew'
                    ? PurchasePackage::where('user_id', $deposit->user_id)
                        ->where('id', $deposit->purchase_id)
                        ->where('package_id', $package->id)
                        ->first()
                    : new PurchasePackage();

                $purchasePackage->fill([
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'trx_id' => $deposit->trx_id,
                    'deposit_id' => $deposit->id,
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
                    'purchase_date' => Carbon::now(),
                    'type' => $deposit->purchase_type == 'renew' ? 'Renew' : 'Purchase',
                    'status' => 1,
                    'expire_date' => match ($package->expiry_time_type) {
                        'Month' => Carbon::now()->addDays(30),
                        'Year' => Carbon::now()->addDays(365),
                        default => null,
                    }
                ]);
                $purchasePackage->save();
            }else{
                return back()->with('error','Plan Not Available.');
            }
            $params = [
                'user' => optional($deposit->user)->username,
                'amount' => currencyPosition($deposit->payable_amount_in_base_currency),
                'topic' => 'Package',
                'transaction' => $deposit->trx_id,
            ];
            $actionAdmin = [
                "name" => optional($deposit->user)->firstname . ' ' . optional($deposit->user)->lastname,
                "image" => getFile(optional($deposit->user)->image_driver, optional($deposit->user)->image),
                "link" => route('admin.payment.log'),
                "icon" => "fas fa-list text-white"
            ];
            $this->adminMail('PAYMENT_PACKAGE_BY_USER', $params, $actionAdmin);
            $this->adminPushNotification('PAYMENT_PACKAGE_BY_USER', $params, $actionAdmin);
            $this->adminFirebasePushNotification('PAYMENT_PACKAGE_BY_USER', $params);
        }
        return true;
    }


    public function cryptoQR($wallet, $amount, $crypto = null)
    {
        $varb = $wallet . "?amount=" . $amount;
        return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$varb&choe=UTF-8";
    }

    public function makeTransaction($user, $amount, $charge, $balance, $trx_type = null, $trx_id = null, $remarks = null, $transactional_id = null, $transactional_type = null)
    {
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $amount;
        $transaction->charge = $charge;
        $transaction->balance = $balance;
        $transaction->trx_type = $trx_type;
        $transaction->trx_id = $trx_id;
        $transaction->remarks = $remarks;
        $transaction->transactional_id = $transactional_id;
        $transaction->transactional_type = $transactional_type;
        $transaction->save();
        return $transaction;
    }
}
