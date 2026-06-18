<?php

namespace App\Services\Subscription\square;

use App\Models\Deposit;
use App\Models\PurchasePackage;
use App\Models\SubscriptionPurchase;
use Carbon\Carbon;
use Facades\App\Services\BasicService;
use GuzzleHttp\Client;
use Illuminate\Support\Str;


class Payment
{
    public static function createPlan($gateway, $subscriptionPlan)
    {
        $accessToken = $gateway->parameters->access_token ?? '';
        $gatewayCurrency = $gateway->supported_currency[0] ?? null;
        $convertRate = 1;
        foreach ($gateway->receivable_currencies as $currency) {
            if ($currency->currency_symbol === $gatewayCurrency) {
                $convertRate = $currency->conversion_rate;
                break;
            }
        }

        if ($gateway->environment == 'test') {
            $url = "https://connect.squareupsandbox.com/v2/catalog/object";
        } else {
            $url = "https://connect.squareup.com/v2/catalog/object";
        }

        try {
            $client = new Client();

            $response = $client->post($url, [
                'headers' => [
                    'Square-Version' => '2023-12-13',
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'idempotency_key' => Str::random(10),
                    'object' => [
                        'type' => 'SUBSCRIPTION_PLAN',
                        'id' => '#' . Str::random(3),
                        'subscription_plan_data' => [
                            'name' => $subscriptionPlan->details->title,
                            'all_items' => true,
                        ]
                    ]
                ]
            ]);

            $responseBody = $response->getBody()->getContents();
            $result = json_decode($responseBody, true);
            $subscription_plan_id = $result['catalog_object']['id'] ?? null;

            if ($subscription_plan_id) {
                $response = $client->post($url, [
                    'headers' => [
                        'Square-Version' => '2023-12-13',
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'idempotency_key' => Str::random(10),
                        'object' => [
                            'type' => 'SUBSCRIPTION_PLAN_VARIATION',
                            'id' => '#' . Str::random(2),
                            'subscription_plan_variation_data' => [
                                'name' => $subscriptionPlan->details->title,
                                'phases' => [
                                    [
                                        'cadence' => $subscriptionPlan->expiry_time == 1 && $subscriptionPlan->expiry_time_type == 'Month' ? "MONTHLY" : 'ANNUAL',
                                        'ordinal' => 0,
                                        'pricing' => [
                                            'price_money' => [
                                                'amount' => $subscriptionPlan->price * $convertRate,
                                                'currency' => $gatewayCurrency
                                            ],
                                            'type' => 'STATIC'
                                        ]
                                    ]
                                ],
                                'subscription_plan_id' => $subscription_plan_id
                            ]
                        ]
                    ]
                ]);
                $responseBody = $response->getBody()->getContents();
                $result = json_decode($responseBody, true);
                $plan_variation_id = $result['catalog_object']['id'] ?? null;
                if (isset($plan_variation_id) && $plan_variation_id) {
                    makeSubscriptionProduct($subscriptionPlan, $plan_variation_id, $gateway->code);
                }
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public static function createSubscription($deposit, $gateway, $request = null)
    {

        $subscription = $deposit->depositable;
        $subscriptionPurchase = $subscription->purchasePackages;
        $accessToken = $gateway->parameters->access_token ?? '';
        $locationId = $gateway->parameters->location_id ?? '';

        $client = new Client();
        if ($gateway->environment == 'test') {
            $url = "https://connect.squareupsandbox.com/v2/";
        } else {
            $url = "https://connect.squareup.com/v2/";
        }

        $response = $client->post($url . 'customers', [
            'headers' => [
                'Square-Version' => '2023-12-13',
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ],

            'json' => [
                'given_name' => optional($deposit->user)->first_name ?? 'Unknown',
                'family_name' => optional($deposit->user)->last_name ?? 'Unknown',
                'email_address' => optional($deposit->user)->email ?? 'example@gmail.com',
                'address' => [
                    'address_line_1' => optional($deposit->user)->address ?? '500 Electric Ave',
                    'address_line_2' => optional($deposit->user)->address_two ?? 'Suite 600',
                    'locality' => optional($deposit->user)->city ?? 'New York',
                    'administrative_district_level_1' => optional($deposit->user)->state ?? 'NY',
                    'postal_code' => optional($deposit->user)->zip_code ?? '10003',
                    'country' => optional($deposit->user)->country_code ?? 'US'
                ],
                'phone_number' => optional($deposit->user)->phone_code . optional($deposit->user)->phone ?? '+1-212-555-4240',
                'reference_id' => strRandom(10),
                'note' => 'a customer'
            ]
        ]);

        $responseBody = $response->getBody()->getContents();
        $result = json_decode($responseBody, true);

        if (isset($result) && isset($result['customer']['id'])) {
            $customerId = $result['customer']['id'];

            $response = $client->post($url . 'subscriptions', [
                'headers' => [
                    'Square-Version' => '2023-12-13',
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'idempotency_key' => strRandom(10),
                    'location_id' => $locationId,
                    'plan_variation_id' => $subscription->gateway_plan_id->square,
                    'customer_id' => $customerId, //SAT1J3W3S3SSBGVX5F463QQTJC
                    'source' => [
                        'name' => 'My Application'
                    ]
                ]
            ]);

            $responseBody = $response->getBody()->getContents();
            $result = json_decode($responseBody, true);
            if (isset($result) && isset($result['subscription']['id'])) {
                $subscriptionPurchase->api_subscription_id = $result['subscription']['id'];
                $subscriptionPurchase->save();
                return [
                    'status' => 'success'
                ];
            }
        }
    }

    public static function ipn($request, $gateway, $deposit = null, $utr = null)
    {
        $apiRes = json_decode($request);
        if ($apiRes->type == 'invoice.payment_made' && $apiRes->data->object->invoice->status == 'PAID' && $apiRes->data->object->invoice->subscription_id) {
            $purchasePlanId = $apiRes->data->object->invoice->subscription_id;
            $subsPurchase = PurchasePackage::where('api_subscription_id', $purchasePlanId)->first();
            if ($subsPurchase) {
                $deposit = Deposit::where('id', $subsPurchase->deposit_id)->latest()->first();
                if ($deposit) {
                    BasicService::subscriptionUpgrade($deposit);
                }
            }
        }

        return "ok";
    }

    public static function cancelSubscription($subscriptionPurchase)
    {
        $gateway = $subscriptionPurchase->deposit->gateway;
        $accessToken = $gateway->parameters->access_token ?? '';

        if ($gateway->environment == 'test') {
            $url = "https://connect.squareupsandbox.com/v2/";
        } else {
            $url = "https://connect.squareup.com/v2/";
        }

        $client = new Client();

        $response = $client->post($url . 'subscriptions/' . $subscriptionPurchase->api_subscription_id . '/cancel', [
            'headers' => [
                'Square-Version' => '2023-12-13',
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ]
        ]);
        $responseBody = $response->getBody()->getContents();
        $result = json_decode($responseBody, true);

        if (isset($result) && !isset($result['subscription']['status']) && $result['subscription']['status'] == 'CANCELED') {
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
