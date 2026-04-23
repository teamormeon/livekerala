<?php

namespace App\Services\Gateway\paddle;

use App\Models\Deposit;
use Facades\App\Services\BasicService;

class Payment
{
    // Prepare the payment data and redirect URL to Paddle
    public static function prepareData($deposit, $gateway)
    {
        // Set the Paddle API endpoint depending on environment
        if ($gateway->environment == 'test') {
            $url = "https://sandbox.paddle.com/api/2.0/checkout/generate_payment_link";
        } else {
            $url = "https://vendors.paddle.com/api/2.0/checkout/generate_payment_link";
        }
//        $baseUrl = $gateway->environment == 'test' ? 'https://sandbox-api.paddle.com/' : 'https://api.paddle.com/';

        // Prepare the payment data
        $postParam = [
//            "vendor_id" => $gateway->parameters->vendor_id, // Paddle vendor ID
            "vendor_auth_code" => $gateway->parameters->client_side_token, // Paddle authentication code
            "title" => "Payment for Deposit", // Title or name for the payment
            "price" => $deposit->payable_amount,
            "currency" => $deposit->payment_method_currency,
            "quantity" => 1,
            "redirect_url" => route('success'),
            "cancel_url" => route('failed'),
            "order_id" => $deposit->trx_id, // Transaction ID (for reference)
            "custom_message" => "Payment for deposit #" . $deposit->trx_id
        ];

        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postParam));

        // Set headers
        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute cURL request
        $result = curl_exec($ch);
        curl_close($ch);

        // Decode the response
        $res = json_decode($result);

        // Check if the response is valid
        if (isset($res->success) && $res->success && isset($res->response->url)) {
            $deposit->note = $res->response->order_id;
            $deposit->save();

            $send['redirect'] = true;
            $send['redirect_url'] = $res->response->url; // URL to redirect the user for payment
        } else {
            $send['error'] = true;
            $send['message'] = 'Payment could not be initiated. Please contact support.';
        }

        return json_encode($send);
    }

    // Handle IPN (Instant Payment Notification) from Paddle
    public static function ipn($request, $gateway, $deposit = null, $trx = null, $type = null)
    {
        // Check for the necessary parameters from the request
        $orderId = $request->order_id ?? null;
        $status = $request->status ?? null;

        // Validate the payment based on status
        if ($orderId && $status == 'COMPLETED') {
            // Find the deposit related to this order
            $deposit = Deposit::where('status', 0)->where('note', $orderId)->latest()->first();
            if ($deposit) {
                // Update payment status
                BasicService::preparePaymentUpgradation($deposit);

                $data['status'] = 'success';
                $data['msg'] = 'Transaction was successful.';
                $data['redirect'] = route('success');
                return $data;
            }
        }

        // Handle failed transaction or any other status
        $data['status'] = 'error';
        $data['msg'] = 'Unable to process the payment.';
        $data['redirect'] = route('failed');
        return $data;
    }
}
