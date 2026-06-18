<?php

namespace App\Services\Subscription\stripe;

use Facades\App\Services\BasicService;
use Stripe\StripeClient;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;

class Payment
{
    public static function createPlan($gateway, $subscriptionPlan)
    {
        $secretKey = $gateway->parameters->secret_key;
        $gatewayCurrency = $gateway->supported_currency[0] ?? null;
        $convertRate = 1;
        foreach ($gateway->receivable_currencies as $currency) {
            if ($currency->currency_symbol == $gatewayCurrency) {
                $convertRate = $currency->conversion_rate;
                break;
            }
        }

        if ($subscriptionPlan->expiry_time == 1 && $subscriptionPlan->expiry_time_type == 'Month') {
            $interval = 'month';
        } else {
            $interval = 'year';
        }

        $stripe = new \Stripe\StripeClient($secretKey);
        $product = $stripe->products->create([
            'name' => $subscriptionPlan->details->title,
            'type' => 'service',
        ]);

        $stripe->plans->create([
            'amount' => ($subscriptionPlan->price * 100),
            'currency' => $gatewayCurrency,
            'interval' => $interval,
            'product' => $product->id,
        ]);

        $price = $stripe->prices->create([
            'unit_amount' => ($convertRate * $subscriptionPlan->price * 100),
            'currency' => $gatewayCurrency,
            'recurring' => ['interval' => $interval],
            'product' => $product->id,
        ]);

        makeSubscriptionProduct($subscriptionPlan, $price->id, $gateway->code);
        return 0;
    }

    public static function createSubscription($deposit, $gateway, $request = null)
    {
        $secretKey = $gateway->parameters->secret_key ?? '';
        $subscription = $deposit->depositable;
        $subscriptionPurchase = optional($deposit->depositable)->purchasePackages;

        $stripe = new \Stripe\StripeClient($secretKey);

        \Stripe\Stripe::setApiKey($secretKey);
        $customer = \Stripe\Customer::create([
            'email' => $request->email, // Replace with the customer's email
            'source' => $request->token, // Replace with a valid payment source, such as a Stripe token or card ID
        ]);

        $webhookEndpoint = \Stripe\WebhookEndpoint::create([
            'url' => route('subscription.ipn', [$gateway->code, $deposit->trx_id]), // Replace with your actual webhook URL
            //'url' => 'https://bestai.free.beeceptor.com', // Replace with your actual webhook URL
            'enabled_events' => [
                'customer.subscription.created',
                'customer.subscription.updated',
                'customer.subscription.deleted',
                'payment_intent.succeeded',
            ],
        ]);

        if (isset($customer->id) && isset($webhookEndpoint->id)) {
            $newSubs = $stripe->subscriptions->create([
                'customer' => $customer->id,
                'items' => [
                    ['price' => $subscription->gateway_plan_id->stripe],
                ],
                'metadata' => [
                    'webhook_endpoint' => $webhookEndpoint->id, // Replace with the webhook endpoint ID
                ],

            ]);

            if (isset($newSubs->id)) {
                $subscriptionPurchase->api_subscription_id = $newSubs->id;
                $subscriptionPurchase->save();
                return [
                    'status' => 'success'
                ];
            }
        } else {
            return [
                'status' => 'error'
            ];
        }
        return [
            'status' => 'error'
        ];

    }

    public static function ipn($request, $gateway, $deposit = null, $utr = null)
    {
        $secretKey = $gateway->parameters->secret_key;
        $subscriptionPurchase = $deposit->depositable;
        $apiRes = json_decode($request);

        if (isset($apiRes->type) && $apiRes->type == 'payment_intent.succeeded') {
            BasicService::subscriptionUpgrade($deposit);
            $data['status'] = 'success';
            $data['msg'] = 'Transaction was successful.';
            $data['redirect'] = route('success');
        } else {
            $data['status'] = 'error';
            $data['msg'] = 'unsuccessful transaction.';
            $data['redirect'] = route('failed');
        }
        return $data;
    }

    public static function cancelSubscription($subscriptionPurchase)
    {
        $gateway = optional($subscriptionPurchase->deposit)->gateway;
        $secretKey = $gateway->parameters->secret_key;

        $stripe = new \Stripe\StripeClient($secretKey);
        $response = $stripe->subscriptions->cancel(
            $subscriptionPurchase->api_subscription_id,
            []
        );

        if (isset($response->status) && $response->status == 'canceled') {
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
        $secretKey = $gateway->parameters->secret_key;
        $stripe = new \Stripe\StripeClient($secretKey);
        $stripe->plans->update(
            $subscriptionPlan->gateway_plan_id->stripe,
            ['metadata' => ['unit_amount' => ($subscriptionPlan->price * 100),]]
        );

        $res = $stripe->plans->update(
            $subscriptionPlan->gateway_plan_id->stripe,
            [
                'metadata' => ['amount' => ($subscriptionPlan->price * 100),]
            ]
        );
        return 0;
    }

    public static function activatedPlan($gateway, $subscriptionPlan)
    {
        $secretKey = $gateway->parameters->secret_key;
        $stripe = new \Stripe\StripeClient($secretKey);
        $res = $stripe->plans->update(
            $subscriptionPlan->gateway_plan_id->stripe,
            [
                'active' => true,
                'metadata' => ['amount' => ($subscriptionPlan->price * 100),]
            ]
        );
        return 0;
    }

    public static function deActivatedPlan($gateway, $subscriptionPlan)
    {
        $secretKey = $gateway->parameters->secret_key;
        $stripe = new \Stripe\StripeClient($secretKey);
        $res = $stripe->plans->update(
            $subscriptionPlan->gateway_plan_id->stripe,
            [
                'active' => false,
                'metadata' => ['amount' => ($subscriptionPlan->price * 100),]
            ]
        );
        return 0;
    }
}
