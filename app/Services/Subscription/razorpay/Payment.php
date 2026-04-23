<?php

namespace App\Services\Subscription\razorpay;

use App\Models\Deposit;
use App\Models\PurchasePackage;
use App\Models\SubscriptionPurchase;
use Carbon\Carbon;
use Facades\App\Services\BasicService;

class Payment
{
	public static function createPlan($gateway, $subscriptionPlan)
	{
		$key_id = $gateway->parameters->key_id;
		$secret = $gateway->parameters->key_secret;
        $gatewayCurrency = $gateway->supported_currency[0] ?? null;
        $convertRate = 1;
        foreach ($gateway->receivable_currencies as $currency) {
            if ($currency->currency_symbol === $gatewayCurrency) {
                $convertRate = $currency->conversion_rate;
                break;
            }
        }

		$amount = round($subscriptionPlan->price * $convertRate);

		$params = [
			'period' => $subscriptionPlan->payment_frequency,
			'interval' => 1,
			'item' => [
				'name' => $subscriptionPlan->details->title,
				'amount' => (int)$amount * 100,
				'currency' => $gatewayCurrency,
				'description' => 'Subscription via Razorpay',
			],
			'notes' => [
				'key1' => 'subscription plan',
			]
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/plans');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
		]);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $key_id . ':' . $secret);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

		$response = curl_exec($ch);
		curl_close($ch);
		$res = json_decode($response);

		if (isset($res) && isset($res->id)) {
			makeSubscriptionProduct($subscriptionPlan, $res->id, $gateway->code);
		}

		return 0;
	}

	public static function createSubscription($deposit, $gateway, $request = null)
	{
		$key_id = $gateway->parameters->key_id;
		$secret = $gateway->parameters->key_secret;
		$subscription = $deposit->depositable;
        $subscriptionPurchase = $subscription->purchasePackages;

		$postParams = [
			"plan_id" => $subscription->gateway_plan_id->razorpay,
			"total_count" => 1,
			"customer_notify" => 1,
			"addons" => [
				[
				]
			],
			"notes" => [
				"notes_key_1" => "Subscription",
			],
			"notify_info" => [
				"notify_email" => $subscriptionPurchase->user->email ?? null,
			]
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/subscriptions');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
		]);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $key_id . ':' . $secret);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postParams));

		$response = curl_exec($ch);

		curl_close($ch);
		$res = json_decode($response);

		if (isset($res) && isset($res->id) && isset($res->short_url)) {
			$subscriptionPurchase->api_subscription_id = $res->id;
			$subscriptionPurchase->save();

			return [
				'status' => 'success',
				'redirect_url' => $res->short_url
			];
		}

		return [
			'status' => 'error'
		];

	}

	public static function ipn($request, $gateway, $deposit = null, $utr = null)
	{
		$apiRes = json_decode($request);

		if (isset($apiRes->event) && $apiRes->event == 'subscription.charged') {
			$purchasePlanId = $apiRes->payload->subscription->entity->plan_id ?? null;
			$subsPurchase = PurchasePackage::where('api_subscription_id', $purchasePlanId)->latest()->first();
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
		$gateway = $subscriptionPurchase->deposit->gateway;
		$key_id = $gateway->parameters->key_id;
		$secret = $gateway->parameters->key_secret;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/subscriptions/' . $subscriptionPurchase->api_subscription_id . '/cancel');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
		]);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $key_id . ':' . $secret);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"cancel_at_cycle_end\": 1\n}");

		$response = curl_exec($ch);

		curl_close($ch);
		$response = json_decode($response);

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
