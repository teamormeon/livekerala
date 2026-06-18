<?php

namespace App\Services\Subscription\paypal;

require 'vendor/autoload.php';

use App\Models\Deposit;
use App\Models\PurchasePackage;
use App\Models\SubscriptionPurchase;
use Carbon\Carbon;
use Facades\App\Services\BasicService;


class Payment
{
    public static function createPlan($gateway, $subscriptionPlan)
    {
        $clientId = $gateway->parameters->cleint_id ?? '';
        $clientSecret = $gateway->parameters->secret ?? '';
        $gatewayCurrency = $gateway->supported_currency[0] ?? null;
        $convertRate = 1;
        foreach ($gateway->receivable_currencies as $currency) {
            if ($currency->currency_symbol === $gatewayCurrency) {
                $convertRate = $currency->conversion_rate;
                break;
            }
        }

        if ($gateway->environment == 'test') {
            $baseUrl = 'https://api-m.sandbox.paypal.com/v1/';
        } else {
            $baseUrl = 'https://api.paypal.com/v1/';
        }

        $productParams = [
            "name" => $subscriptionPlan->details->title ?? "abc",
            "description" => "Subscription via Paypal",
            "type" => "SERVICE",
            "category" => "SOFTWARE",
            "image_url" => getFile(basicControl()->logo_driver, basicControl()->logo),
            "home_url" => url('/'),
            //"image_url" => "https://bugfinder.net/aa.png",
            //"home_url" => "https://bugfinder.net/"
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $baseUrl . "catalogs/products");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($productParams));

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            'Authorization:Basic ' . base64_encode("{$clientId}:{$clientSecret}"),
            "PayPal-Request-Id: REQUEST-ID"
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            echo 'cURL Error: ' . curl_error($ch);
        } else {
            $res = json_decode($response);
        }
        curl_close($ch);

        $postParams = [
            "product_id" => $res->id ?? null,
            "name" => $subscriptionPlan->plan_name ?? 'abc',
            "description" => $subscriptionPlan->plan_name ?? 'abc',
            "billing_cycles" => [
                [
                    "frequency" => [
                        "interval_unit" => $subscriptionPlan->expiry_time == 1 && $subscriptionPlan->expiry_time_type == 'Month' ? 'MONTH' : 'YEAR',
                        "interval_count" => 1
                    ],
                    "tenure_type" => "REGULAR",
                    "sequence" => 1,
                    "total_cycles" => 0,
                    "pricing_scheme" => [
                        "fixed_price" => [
                            "value" => $subscriptionPlan->price * $convertRate,
                            "currency_code" => $gatewayCurrency
                        ]
                    ]
                ]
            ],
            "payment_preferences" => [
                "auto_bill_outstanding" => true,
                "setup_fee" => [
                    "value" => "0",
                    "currency_code" => $gatewayCurrency
                ],
                "setup_fee_failure_action" => "CONTINUE",
                "payment_failure_threshold" => 3
            ],
            "taxes" => [
                "percentage" => "0",
                "inclusive" => false
            ]
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $baseUrl . "billing/plans");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postParams));

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            'Authorization:Basic ' . base64_encode("{$clientId}:{$clientSecret}"),
            "Content-Type: application/json",
            "PayPal-Request-Id: REQUEST-ID"
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            echo 'cURL Error: ' . curl_error($ch);
        } else {
            $res = json_decode($response);
        }

        if (isset($res->id)) {
            makeSubscriptionProduct($subscriptionPlan, $res->id, $gateway->code);

            $webhookParams = [
                "url" => route('subscription.ipn', $gateway->code),
                "event_types" => [
                    [
                        "name" => "PAYMENT.SALE.COMPLETED"
                    ],
                    [
                        "name" => "BILLING.SUBSCRIPTION.CREATED"
                    ],
                    [
                        "name" => "BILLING.SUBSCRIPTION.ACTIVATED"
                    ],
                    [
                        "name" => "BILLING.SUBSCRIPTION.RE-ACTIVATED"
                    ],
                    [
                        "name" => "BILLING.SUBSCRIPTION.SUSPENDED"
                    ],
                    [
                        "name" => "BILLING.SUBSCRIPTION.UPDATED"
                    ],
                    [
                        "name" => "BILLING.SUBSCRIPTION.PAYMENT.FAILED"
                    ],
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $baseUrl . 'notifications/webhooks');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization:Basic ' . base64_encode("{$clientId}:{$clientSecret}"),
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookParams));

            $response = curl_exec($ch);

            curl_close($ch);
            $res = json_decode($response);
        }
        return 0;
    }

    public static function createSubscription($deposit, $gateway, $request = null)
    {
        $subscriptionPurchase = $deposit->depositable;

        $subscriptionPurchase->api_subscription_id = $request->token;
        $subscriptionPurchase->save();
        return [
            'status' => 'success'
        ];
    }

    public static function ipn($request, $gateway, $deposit = null, $utr = null)
    {
        $apiRes = json_decode($request);
        if ($apiRes->resource_type == 'subscription' && $apiRes->event_type == 'PAYMENT.SALE.COMPLETED') {
            $purchasePlanId = $apiRes->resource->id;
            $subsPurchase = PurchasePackage::where('api_subscription_id', $purchasePlanId)->first();
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
        $clientId = $gateway->parameters->cleint_id ?? '';
        $clientSecret = $gateway->parameters->secret ?? '';

        if ($gateway->environment == 'test') {
            $baseUrl = 'https://api-m.sandbox.paypal.com/v1/';
        } else {
            $baseUrl = 'https://api.paypal.com/v1/';
        }

        // API endpoint for subscription cancellation
        $cancelEndpoint = $baseUrl . 'billing/subscriptions/' . $subscriptionPurchase->api_subscription_id . '/cancel';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $cancelEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            'Authorization:Basic ' . base64_encode("{$clientId}:{$clientSecret}"),
            "Content-Type: application/json",
            "PayPal-Request-Id: REQUEST-ID"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"reason\": \"Not satisfied with the service\"\n}");

        $response = curl_exec($ch);

        curl_close($ch);
        $res = json_decode($response);
        // Check if cancellation was successful
        if (isset($res) && $res != null && !isset($res->details[0]->issue)) {
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
        $clientId = $gateway->parameters->cleint_id ?? '';
        $clientSecret = $gateway->parameters->secret ?? '';
        $gatewayCurrency = $gateway->supported_currency[0] ?? null;

        if ($gateway->environment == 'test') {
            $baseUrl = 'https://api-m.sandbox.paypal.com/v1/';
        } else {
            $baseUrl = 'https://api.paypal.com/v1/';
        }

        $postParams = [
            "pricing_schemes" => [
                [
                    "billing_cycle_sequence" => 1,
                    "pricing_scheme" => [
                        "fixed_price" => [
                            "value" => $subscriptionPlan->price,
                            "currency_code" => $gatewayCurrency
                        ]
                    ]
                ],
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . 'billing/plans/' . $subscriptionPlan->gateway_plan_id->paypal . '/update-pricing-schemes');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            'Authorization:Basic ' . base64_encode("{$clientId}:{$clientSecret}"),
            "Content-Type: application/json",
            "PayPal-Request-Id: REQUEST-ID"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postParams));

        $response = curl_exec($ch);

        curl_close($ch);
        return 0;
    }

    public static function activatedPlan($gateway, $subscriptionPlan)
    {
        $clientId = $gateway->parameters->cleint_id ?? '';
        $clientSecret = $gateway->parameters->secret ?? '';

        if ($gateway->environment == 'test') {
            $baseUrl = 'https://api-m.sandbox.paypal.com/v1/';
        } else {
            $baseUrl = 'https://api.paypal.com/v1/';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . 'billing/plans/' . $subscriptionPlan->gateway_plan_id->paypal . '/activate');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            'Authorization:Basic ' . base64_encode("{$clientId}:{$clientSecret}"),
            "Content-Type: application/json",
            "PayPal-Request-Id: REQUEST-ID"
        ]);

        $response = curl_exec($ch);

        curl_close($ch);
        return 0;
    }

    public static function deActivatedPlan($gateway, $subscriptionPlan)
    {
        $clientId = $gateway->parameters->cleint_id ?? '';
        $clientSecret = $gateway->parameters->secret ?? '';

        if ($gateway->environment == 'test') {
            $baseUrl = 'https://api-m.sandbox.paypal.com/v1/';
        } else {
            $baseUrl = 'https://api.paypal.com/v1/';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . 'billing/plans/' . $subscriptionPlan->gateway_plan_id->paypal . '/deactivate');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            'Authorization:Basic ' . base64_encode("{$clientId}:{$clientSecret}"),
            "Content-Type: application/json",
            "PayPal-Request-Id: REQUEST-ID"
        ]);

        $response = curl_exec($ch);

        curl_close($ch);
        return 0;
    }

}
