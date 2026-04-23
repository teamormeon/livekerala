<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\Page;
use App\Traits\ApiResponse;
use App\Traits\Notify;
use App\Traits\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Facades\App\Services\BasicService;

class ApiPaymentController extends Controller
{
    use ApiResponse, Notify, Upload;

    public function paymentWebview($trx_id)
    {
        try {
            $deposit = Deposit::where('trx_id', $trx_id)->latest()->first();
            if (!$deposit){
                return response()->json($this->withError('Record not found'));
            }

            $val['url'] = route('paymentView', $deposit->id);
            return response()->json($this->withSuccess($val));

        } catch (\Exception $e) {
            return response()->json($this->withError($e->getMessage()));
        }
    }

    public function paymentView($deposit_id)
    {
        try {
            $deposit = Deposit::latest()->find($deposit_id);
            if ($deposit) {
                $getwayObj = 'App\\Services\\Gateway\\' . $deposit->gateway->code . '\\Payment';
                $data = $getwayObj::prepareData($deposit, $deposit->gateway);
                $data = json_decode($data);

                if (isset($data->error)) {
                    $result['status'] = false;
                    $result['message'] = $data->message;
                    return response($result, 200);
                }
                if (isset($data->redirect)) {
                    return redirect($data->redirect_url);
                }

                if ($data->view) {
                    $parts = explode(".", $data->view);
                    $desiredValue = end($parts);
                    $newView = 'mobile-payment.'.$desiredValue;
                    return view($newView, compact('data', 'deposit'));
                }
                abort(404);
            }
        } catch (\Exception $e) {
            return response()->json($this->withError($e->getMessage()));
        }
    }

    public function fromSubmit(Request $request, $trx_id)
    {
        $data = Deposit::with(['gateway', 'user'])->where('trx_id', $trx_id)->orderBy('id', 'DESC')->first();
        if (is_null($data)) {
            return response()->json($this->withError('Invalid Request'));
        }
        if ($data->status != 0) {
            return response()->json($this->withError('Invalid Request'));
        }

        $params = optional($data->gateway)->parameters;
        $reqData = $request->except('_token', '_method');
        $rules = [];
        if ($params !== null) {
            foreach ($params as $key => $cus) {
                $rules[$key] = [$cus->validation == 'required' ? $cus->validation : 'nullable'];
                if ($cus->type === 'file') {
                    $rules[$key][] = 'image';
                } elseif ($cus->type === 'text') {
                    $rules[$key][] = 'max:191';
                } elseif ($cus->type === 'number') {
                    $rules[$key][] = 'integer';
                } elseif ($cus->type === 'textarea') {
                    $rules[$key][] = 'min:3';
                    $rules[$key][] = 'max:300';
                }
            }
        }

        $validator = Validator::make($reqData, $rules);
        if ($validator->fails()) {
            return response()->json($this->withError(collect($validator->errors())->collapse()));
        }

        $reqField = [];
        if ($params != null) {
            foreach ($request->except('_token', '_method', 'type') as $k => $v) {
                foreach ($params as $inKey => $inVal) {
                    if ($k == $inKey) {
                        if ($inVal->type == 'file' && $request->hasFile($inKey)) {
                            try {
                                $file = $this->fileUpload($request[$inKey], config('filelocation.deposit.path'), null, null, 'webp', 99);
                                $reqField[$inKey] = [
                                    'field_name' => $inVal->field_name,
                                    'field_value' => $file['path'],
                                    'field_driver' => $file['driver'],
                                    'validation' => $inVal->validation,
                                    'type' => $inVal->type,
                                ];
                            } catch (\Exception $exp) {
                                return response()->json($this->withError(" Could not upload your {$inKey} "));
                            }
                        } else {
                            $reqField[$inKey] = [
                                'field_name' => $inVal->field_name,
                                'validation' => $inVal->validation,
                                'field_value' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }
        }

        $data->update([
            'information' => $reqField,
            'created_at' => Carbon::now(),
            'status' => 2,
        ]);

        $msg = [
            'username' => optional($data->user)->username,
            'amount' => currencyPosition($data->amount),
            'gateway' => optional($data->gateway)->name
        ];
        $action = [
            "name" => optional($data->user)->firstname . ' ' . optional($data->user)->lastname,
            "image" => getFile(optional($data->user)->image_driver, optional($data->user)->image),
            "link" => route('admin.user.payment', $data->user_id),
            "icon" => "fa fa-money-bill-alt text-white"
        ];

        $this->adminPushNotification('PAYMENT_REQUEST', $msg, $action);
        $this->adminFirebasePushNotification('PAYMENT_REQUEST', $msg, $action);
        $this->adminMail('PAYMENT_REQUEST', $msg);
        return response()->json($this->withSuccess('You request has been taken'));
    }

    public function cardPayment(Request $request)
    {
        try {
            $rules = [
                'trx_id' => 'required',
                'card_number' => 'required',
                'card_name' => 'required',
                'expiry_month' => 'required',
                'expiry_year' => 'required',
                'card_cvc' => 'required',
            ];
            $validate = Validator::make($request->all(), $rules);
            if ($validate->fails()) {
                return response()->json($this->withError(collect($validate->errors())->collapse()));
            }

            $trx = $request->trx_id;
            $deposit = Deposit::with(['gateway', 'user'])->where('trx_id', $trx)->orderBy('id', 'DESC')->first();

            if (!$deposit) {
                return response()->json($this->withError('Invalid Payment Request'));
            }

            $getwayObj = 'App\\Services\\Gateway\\' . $deposit->gateway->code . '\\Payment';
            $data = $getwayObj::ipn($request, $deposit->gateway, $deposit, $trx ?? null, $type ?? null);
            if ($data == 'success') {
                return response()->json($this->withSuccess('Payment has been complete'));
            } else {
                return response()->json($this->withError('unsuccessful transaction'));
            }
        }catch (\Exception $e) {
            return response()->json($this->withError($e->getMessage()));
        }
    }


    public function paymentDone(Request $request)
    {
        $rules = [
            'trx_id' => 'required',
        ];
        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return response()->json($this->withErrors(collect($validate->errors())->collapse()));
        }

        $deposit = Deposit::latest()->where('trx_id', $request->trx_id)->where('status', 0)->first();
        if (!$deposit) {
            return response()->json($this->withError('Record not found'));
        }

        BasicService::preparePaymentUpgradation($deposit);
        return response()->json($this->withSuccess('Payment has been completed'));
    }

}
