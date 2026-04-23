<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserAuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        try {
            $basic = basicControl();
            $rules = [
                'firstname' => 'required|string|max:250',
                'lastname' => 'required|string|max:250',
                'username' => 'required|string|max:250',
                'email' => 'required|string|email|max:250|unique:users,email',
                'phone_code' => 'required',
                'phone' => 'required',
                'country_code' => 'required',
                'country' => 'required',
                'password' => $basic->strong_password == 0 ?
                    ['required', 'confirmed', 'min:6'] :
                    ['required', 'confirmed', $this->strongPassword()],
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json($this->withError(collect($validator->errors())->collapse()));
            }

            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'username' => $request->username,
                'email' => $request->email,
                'phone_code' => $request->phone_code,
                'phone' => $request->phone,
                'country_code' => $request->country_code,
                'country' => $request->country,
                'password' => Hash::make($request->password)
            ]);

            $data['message'] = 'User registered successfully.';
            $data['token'] = $user->createToken($request->email)->plainTextToken;
            return response()->json($this->withSuccess($data));
        }catch (\Exception $e){
            return response()->json($this->withError($e->getMessage()));
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('username', 'email', 'password');
            $validator = Validator::make($credentials, [
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json($this->withError(collect($validator->errors())->collapse()));
            }
            $user = User::where('email', $credentials['username'])
                ->orWhere('username', $credentials['username'])
                ->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json($this->withError('credentials do not match'));
            }
            $data['message'] = 'User logged in successfully.';
            $data['token'] = $user->createToken($user->email)->plainTextToken;

            return response()->json($this->withSuccess($data));
        }catch (\Exception $e){
            return response()->json($this->withError($e->getMessage()));
        }
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json($this->withSuccess('User logged out successfully'));
    }

    public function deleteAccount($id)
    {
        $userData = User::find($id);

        if (config('demo.IS_DEMO')) {
            return response()->json($this->withError('You are not allowed to delete on DEMO Version'));
        }
        if (!$userData){
            return response()->json($this->withError('User not found'));
        }
        $userData->delete();
        return response()->json($this->withSuccess('User has been Deleted Successfully'));
    }

    public function getEmailForResetPassword(Request $request)
    {
        try {
            $rules = [
                'email' => 'required|email',
            ];
            $validateUser = Validator::make($request->all(), $rules);
            if ($validateUser->fails()) {
                return response()->json($this->withError(collect($validateUser->errors())->collapse()));
            }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json($this->withError('Email does not exit on record'));
            }

            $code = rand(100000, 999999);
            $user->verify_code = $code;
            $user->save();

            $basic = basicControl();
            $email_from = $basic->sender_email;
            $subject = "Password Reset";
            $message = 'Your Password Reset Code is ' . $code;
            @Mail::to($request->email)->send(new SendMail($email_from, $subject, $message));

            $data['email'] = $request->email;
            $data['message'] = 'Reset Code has been send';
            return response()->json($this->withSuccess($data));
        } catch (\Exception $e) {
            return response()->json($this->withError($e->getMessage()));
        }
    }

    public function getCodeForResetPassword(Request $request)
    {
        try {
            $Rules = [
                'code' => 'required',
                'email' => 'required|email',
            ];
            $validateUser = Validator::make($request->all(), $Rules);

            if ($validateUser->fails()) {
                return response()->json($this->withError(collect($validateUser->errors())->collapse()));
            }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json($this->withError('Email does not exit on record'));
            }

            if ($user->verify_code == $request->code && $user->updated_at > Carbon::now()->subMinutes(5)) {
                $token = Str::random(60);
                $user->verify_code = null;
                $user->password_reset_token = $token;
                $user->save();

                $data = [
                    'message' => 'Code Matched',
                    'token' => $token,
                ];
                return response()->json($this->withSuccess($data));
            }
            return response()->json($this->withError('Invalid Code'));
        } catch (\Exception $e) {
            return response()->json($this->withError($e->getMessage()));
        }
    }

    public function passwordReset(Request $request)
    {
        try {
            $basic = basicControl();
            $rules = [
                'email' => 'required|email|exists:users,email',
                'token' => 'required',
                'password' => $basic->strong_password == 0 ?
                    ['required', 'confirmed', 'min:6'] :
                    ['required', 'confirmed', $this->strongPassword()],
                'password_confirmation' => 'required| min:6',
            ];
            $message = [
                'email.exists' => 'Email does not exist on record'
            ];
            $validateUser = Validator::make($request->all(), $rules, $message);
            if ($validateUser->fails()) {
                return response()->json($this->withError(collect($validateUser->errors())->collapse()));
            }

            $user = User::where('email', $request->email)->toBase()->first();
            if (!$user){
                return response()->json($this->withError('Email does not exit on record'));
            }

            if ($user->password_reset_token != null && $user->password_reset_token == $request->token){
                $user->password = Hash::make($request->password);
                $user->password_reset_token = null;
                $user->save();

                return response()->json($this->withSuccess('Password Reset Successfully'));
            } elseif ($user->reset_code_token == null){
                return response()->json($this->withError('Password reset token not found!'));
            }
            else{
                return response()->json($this->withError('Password reset token does not match!'));
            }
        }catch (\Exception $e){
            return response()->json($this->withError($e->getMessage()));
        }
    }



    private function strongPassword()
    {
        return Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                        ->uncompromised();
    }
}
