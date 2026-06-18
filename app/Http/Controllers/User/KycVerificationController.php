<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Models\UserKyc;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KycVerificationController extends Controller
{
    use Upload;

    public function kyc()
    {
        $data['kyc'] = Kyc::orderBy('id', 'asc')->where('status', 1)->get();
        return view(template() . 'user.verification_center.index', $data);
    }

    public function kycForm($id)
    {
        $data['kyc'] = Kyc::findOrFail($id);
        $data['userKyc'] = UserKyc::where('user_id', auth()->id())->where('kyc_id', $id)
            ->where('status', '!=', 2)
            ->first();
        return view(template() . 'user.verification_center.kyc_form', $data);
    }

    public function verificationSubmit(Request $request)
    {
        $kyc = Kyc::where('slug', $request->type)->where('status', 1)->firstOrFail();
        $params = $kyc->input_form;
        $reqData = $request->except('_token', '_method');
        $rules = [];

        if ($params !== null) {
            foreach ($params as $key => $cus) {
                $rules[$key] = [$cus->validation == 'required' ? $cus->validation : 'nullable'];
                if ($cus->type == 'file') {
                    $rules[$key][] = 'image';
                    $rules[$key][] = 'mimes:jpeg,jpg,png';
                    $rules[$key][] = 'max:2048';
                } elseif ($cus->type == 'text') {
                    $rules[$key][] = 'max:191';
                } elseif ($cus->type == 'number') {
                    $rules[$key][] = 'integer';
                } elseif ($cus->type == 'textarea') {
                    $rules[$key][] = 'min:3';
                    $rules[$key][] = 'max:300';
                }
            }
        }

        $params = $kyc->input_form;
        $validator = Validator::make($reqData, $rules);
        if ($validator->fails()) {
            $validator->errors()->add('kyc', 'Your unique error message for the kyc field');
            return back()->withErrors($validator)->withInput();
        }

        $reqField = [];
        foreach ($request->except('_token', '_method', 'type') as $k => $v) {
            foreach ($params as $inKey => $inVal) {
                if ($k == $inKey) {
                    if ($inVal->type == 'file' && $request->hasFile($inKey)) {
                        try {
                            $file = $this->fileUpload($request[$inKey], config('filelocation.kyc.path'), null, null, 'webp', 99);
                            $reqField[$inKey] = [
                                'field_name' => $inVal->field_name,
                                'field_label' => $inVal->field_label,
                                'field_value' => $file['path'],
                                'field_driver' => $file['driver'],
                                'validation' => $inVal->validation,
                                'type' => $inVal->type,
                            ];
                        } catch (\Exception $exp) {
                            session()->flash('error', 'Could not upload your ' . $inKey);
                            return back()->withInput();
                        }
                    } else {
                        $reqField[$inKey] = [
                            'field_name' => $inVal->field_name,
                            'field_label' => $inVal->field_label,
                            'validation' => $inVal->validation,
                            'field_value' => $v,
                            'type' => $inVal->type,
                        ];
                    }
                }
            }
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();
            UserKyc::create([
                'user_id' => $user->id,
                'kyc_id' => $kyc->id,
                'kyc_type' => $kyc->name,
                'kyc_info' => $reqField
            ]);

            if ($request->type == 'address-verification'){
                $user->address_verify = 1;
            }else{
                $user->identity_verify = 1;
            }
            $user->save();
            DB::commit();
            return back()->with('success', 'KYC Sent Successfully');
        }catch (\Exception $exception){
            DB::rollBack();
            $validator->errors()->add('identity', '1');
            $validator->errors()->add('addressVerification', '1');
            return back()->withErrors($validator)->withInput()->with('error', "Failed to submit request");
        }
    }

    public function history()
    {
        $data['userKyc'] = UserKyc::with(['user', 'kyc'])->where('user_id', auth()->id())->paginate(basicControl()->paginate);
        return view(template() . 'user.verification_center.history', $data);
    }

}
