<?php

namespace App\Services\Subscription\paddle;

require 'vendor/autoload.php';

use App\Models\Deposit;
use App\Models\PurchasePackage;
use App\Models\SubscriptionPurchase;
use Carbon\Carbon;
use Facades\App\Services\BasicService;
use Illuminate\Support\Facades\Log;


class Payment
{
    public static function createPlan($gateway, $subscriptionPlan)
    {
        log::info($subscriptionPlan);
        $apiKey = $gateway->parameters->api_key ?? '';
        $gatewayCurrency = $gateway->supported_currency[0] ?? null;
        $convertRate = 1;
        foreach ($gateway->receivable_currencies as $currency) {
            if ($currency->currency_symbol == $gatewayCurrency) {
                $convertRate = $currency->conversion_rate;
                break;
            }
        }

        if ($gateway->environment == 'test') {
            $baseUrl = 'https://sandbox-api.paddle.com/';
        } else {
            $baseUrl = 'https://api.paddle.com/';
        }

        $productParams = [
            "name" => $subscriptionPlan->details->title,
            "tax_category" => "standard",
            "description" => 'Plan Subscription via Paddle',
            "custom_data" => [
                "features" => [
                    "reports" => true,
                    "crm" => false,
                    "data_retention" => true
                ]
            ]
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $baseUrl . 'products',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($productParams),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if ($subscriptionPlan->expiry_time == 1 && $subscriptionPlan->expiry_time_type == 'Month') {
            $interval = 'month';
        } else {
            $interval = 'year';
        }
        $productRes = json_decode($response);
        if (isset($productRes->data) && isset($productRes->data->id)) {
            $productId = $productRes->data->id;
            $amount = $convertRate * $subscriptionPlan->price * 100;
            $postPrice = [
                "description" => 'Subscription Plan via Paddle',
                "name" => $subscriptionPlan->details->title,
                "product_id" => $productId,
                "unit_price" => [
                    "amount" => "$amount",
                    "currency_code" => $gatewayCurrency
                ],
                "billing_cycle" => [
                    "interval" => $interval,
                    "frequency" => 1
                ],
                "trial_period" => null,
                "quantity" => [
                    "minimum" => 1,
                    "maximum" => 1
                ]
            ];
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $baseUrl . 'prices',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($postPrice),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $apiKey,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $priceRes = json_decode($response);
            if (isset($priceRes->data) && isset($priceRes->data->id)) {
                makeSubscriptionProduct(json_decode($subscriptionPlan), $priceRes->data->id, $gateway->code);
            }
        }
        return 0;
    }

    public static function createSubscription($subPurId, $trx_id)
    {
        $subscriptionPurchase = PurchasePackage::findOrFail($subPurId);
        $gateway = $subscriptionPurchase->deposit->gateway;
        $apiKey = $gateway->parameters->api_key ?? '';
        if ($gateway->environment == 'test') {
            $baseUrl = 'https://sandbox-api.paddle.com/';
        } else {
            $baseUrl = 'https://api.paddle.com/';
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $baseUrl . 'transactions/' . $trx_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $apiKey
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response);
        if (isset($res->data) && $res->data->status == 'completed' && isset($res->data->subscription_id)) {
            $subscriptionPurchase->api_subscription_id = $res->data->subscription_id;
            $subscriptionPurchase->save();
        }
        return 0;
    }

    public static function ipn($request, $gateway, $deposit = null, $utr = null)
    {
        $apiRes = json_decode($request);
        if ($apiRes->event_type == 'subscription.created' || $apiRes->event_type == 'subscription.updated') {
            $purchasePlanId = $apiRes->data->id;
            $subsPurchase = PurchasePackage::where('api_subscription_id', $purchasePlanId)->where('status', 1)->first();
            if ($subsPurchase) {
                $deposit = Deposit::where('id', $subsPurchase->deposit_id)->latest()->first();
                if ($deposit) {
                    BasicService::subscriptionUpgrade($deposit);
                    $data['status'] = 'success';
                    $data['msg'] = 'Transaction was successful.';
                    $data['redirect'] = route('success');
                }
            }
        } else {
            $data['status'] = 'error';
            $data['msg'] = 'unsuccessful transaction.';
            $data['redirect'] = route('failed');
        }
        return $data;
    }

    public static function cancelSubscription($subscriptionPurchase)
    {
        $gateway = $subscriptionPurchase->gateway;
        $apiKey = $gateway->parameters->api_key ?? '';
        if ($gateway->environment == 'test') {
            $baseUrl = 'https://sandbox-api.paddle.com/';
        } else {
            $baseUrl = 'https://api.paddle.com/';
        }

        $postParams = [
            'effective_from' => 'immediately'
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $baseUrl . 'subscriptions/' . $subscriptionPurchase->api_subscription_id . '/cancel',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postParams),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response);

        // Check if cancellation was successful
        if (isset($res) && isset($res->status) && $res->status == 'canceled') {
            return [
                'status' => 'success'
            ];
        } else {
            return [
                'status' => 'error'
            ];
        }
    }

    public static function updatePlan($gateway, $subscriptionPlan)
    {
        $apiKey = $gateway->parameters->api_key ?? '';
        $gatewayCurrency = $gateway->supported_currency[0] ?? null;
        $convertRate = 1;
        foreach ($gateway->receivable_currencies as $currency) {
            if ($currency->currency_symbol === $gatewayCurrency) {
                $convertRate = $currency->conversion_rate;
                break;
            }
        }

        if ($gateway->environment == 'test') {
            $baseUrl = 'https://sandbox-api.paddle.com/';
        } else {
            $baseUrl = 'https://api.paddle.com/';
        }

        if ($subscriptionPlan->expiry_time == 1 && $subscriptionPlan->expiry_time_type == 'Month') {
            $interval = 'month';
        } else {
            $interval = 'year';
        }

        $amount = $convertRate * $subscriptionPlan->price * 100;
        $postPrice = [
            "description" => 'Subscription via Paddle Payment',
            "name" => $subscriptionPlan->details->title,
            "unit_price" => [
                "amount" => "$amount",
                "currency_code" => $gatewayCurrency
            ],
            "billing_cycle" => [
                "interval" => $interval,
                "frequency" => 1
            ],
            "trial_period" => null,
            "quantity" => [
                "minimum" => 1,
                "maximum" => 1
            ]
        ];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $baseUrl . 'prices',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postPrice),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return 0;
    }

    public static function activatedPlan($gateway, $subscriptionPlan)
    {
        return 0;
    }

    public static function deActivatedPlan($gateway, $subscriptionPlan)
    {
        return 0;
    }

}
