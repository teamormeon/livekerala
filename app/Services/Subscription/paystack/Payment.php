<?php

namespace App\Services\Subscription\paystack;

use Facades\App\Services\BasicService;

class Payment
{
    public static function createPlan($gateway, $subscriptionPlan)
    {

        $secretKey = $gateway->parameters->secret_key;
        $gatewayCurrency = $gateway->supported_currency[0] ?? null;
        $convertRate = 1;
        foreach ($gateway->receivable_currencies as $currency) {
            if ($currency->currency_symbol === $gatewayCurrency) {
                $convertRate = $currency->conversion_rate;
                break;
            }
        }
        $amount = round($subscriptionPlan->price * $convertRate);

        $url = "https://api.paystack.co/plan";
        $fields = [
            'name' => $subscriptionPlan->details->title,
            'interval' => $subscriptionPlan->expiry_time == 1 && $subscriptionPlan->expiry_time_type == 'Month' ? "monthly" : 'annually',
            'amount' => $amount
        ];

        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer $secretKey",
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        $res = json_decode($result);

        if (isset($res) && isset($res->data->plan_code)) {
            makeSubscriptionProduct($subscriptionPlan, $res->data->plan_code, $gateway->code);
        }

        return 0;
    }

    public static function createSubscription($deposit, $gateway, $request = null)
    {

        $secretKey = $gateway->parameters->secret_key ?? '';
        $subscription  = $deposit->depositable;
        $subscriptionPurchase = optional($deposit->depositable)->purchasePackages;

        $url = "https://api.paystack.co/customer";

        $fields = [
            "email" => $request->email,
            "first_name" => $deposit->receiver->name ?? 'abc',
            "last_name" => $deposit->receiver->name ?? 'abc',
            "phone" => ""
        ];

        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer $secretKey",
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        $res = json_decode($result);


        $url = "https://api.paystack.co/subscription";

        $fields = [
            'customer' => $res->data->customer_code ?? $request->email,
            'plan' => $subscription->gateway_plan_id->paystack
        ];

        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer $secretKey",
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        $res = json_decode($result);


        if (isset($res) && isset($res->data->subscription_code) && isset($res->data->email_token)) {
            $params = [
                'email_token' => $res->data->email_token
            ];
            $subscriptionPurchase->api_subscription_id = $res->data->subscription_code;
            $subscriptionPurchase->extra_api_response = $params;
            $subscriptionPurchase->save();

            return [
                'status' => 'success'
            ];
        }

        return [
            'status' => 'error'
        ];

    }


    public static function cancelSubscription($subscriptionPurchase)
    {
        $gateway = $subscriptionPurchase->deposit->gateway;
        $secretKey = $gateway->parameters->secret_key;

        $url = "https://api.paystack.co/subscription/disable";

        $fields = [
            'code' => $subscriptionPurchase->api_subscription_id,
            'token' => $subscriptionPurchase->extra_api_response->email_token
        ];

        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer $secretKey",
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        $response = json_decode($result);

        if (isset($response->status) && $response->status == 'true') {
            return [
                'status' => 'success'
            ];
        }

        return [
            'status' => 'error'
        ];
    }

    public static function updatePlan($gateway, $subscriptionPlan)
    {
        $planCode = $subscriptionPlan->gateway_plan_id->paystack;
        $secretKey = $gateway->parameters->secret_key;
        $gatewayCurrency = $gateway->supported_currency[0] ?? null;
        $convertRate = 1;
        foreach ($gateway->receivable_currencies as $currency) {
            if ($currency->currency_symbol === $gatewayCurrency) {
                $convertRate = $currency->conversion_rate;
                break;
            }
        }
        $url = "https://api.paystack.co/plan/$planCode";

        $amount = round($subscriptionPlan->price * $convertRate);
        $fields = [
            'name' => $subscriptionPlan->plan_name,
            'amount' => $amount
        ];

        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer $secretKey",
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        $res = json_decode($result);
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
