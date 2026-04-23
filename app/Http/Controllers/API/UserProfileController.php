<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Models\Language;
use App\Models\User;
use App\Models\UserKyc;
use App\Models\UserSocial;
use App\Models\Viewer;
use App\Traits\ApiResponse;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function Laravel\Prompts\select;

class UserProfileController extends Controller
{
    use ApiResponse,Upload;

    public function profile()
    {
        try {
            $user = User::with(['get_social_links_user'])->withCount(['totalViews','follower', 'following'])->where('id',auth()->id())->first();

            if (!$user){
                return response()->json($this->withError('User not found'));
            }

            $formattedSocialLinks = [];
            $user->get_social_links_user->map(function ($link) use (&$formattedSocialLinks) {
                $formattedSocialLinks[] = [
                    'id' => $link->id,
                    'user_id' => $link->user_id,
                    'social_icon' =>$link->social_icon,
                    'social_url' =>$link->social_url,
                ];
            });

            $formattedUser = [
                'id' => $user->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'username' => $user->username,
                'bio' => $user->bio,
                'website' => $user->website,
                'email' => $user->email,
                'phone_code' => $user->phone_code,
                'phone' => $user->phone,
                'country_code' => $user->country_code,
                'country' => $user->country,
                'language_id' => $user->language_id,
                'image' => getFile($user->image_driver, $user->image),
                'address_one' => $user->address_one,
                'address_two' => $user->address_two,
                'state' => $user->state,
                'city' => $user->city,
                'zip_code' => $user->zip_code,
                'status' => $user->status,
                'joined_at' => $user->created_at,
                'views' => $user->total_views_count,
                'follower' => $user->follower_count,
                'following' => $user->following_count,
                'social_links' => $formattedSocialLinks,
            ];

            $info = [
                'status' => '0 = Inactive, 1 = Active',
                'is_google_map' => '0 = Inactive, 1 = Active',
            ];
            $basic = basicControl();
            $data['base_currency'] = $basic->base_currency;
            $data['currency_symbol'] = $basic->currency_symbol;
            $data['is_google_map'] = $basic->is_google_map;
            $data['google_map_app_key'] = $basic->google_map_app_key;
            $data['google_map_id'] = $basic->google_map_id;
            $data['profile'] = $formattedUser;
            return response()->json($this->withSuccess($data, $info));
        }catch (\Exception $e){
            return response()->json($this->withError($e->getMessage()));
        }
    }

    public function updateProfile(Request $request)
    {
        DB::beginTransaction();
        try {
            $languages = Language::all()->toBase()->map(function ($item) {
                return $item->id;
            });
            $req = $request->except('_method', '_token');
            $user = Auth::user();
            $rules = [
                'first_name' => 'nullable|string|min:4',
                'last_name' => 'nullable|string|min:4',
                'username' => "sometimes|nullable|alpha_dash|min:5|unique:users,username," . $user->id,
                'email' => 'email:rfc,dns|unique:users,email,'. $user->id,
                'phone' => 'nullable',
                'address_one' => 'nullable',
                'city' => 'nullable',
                'state' => 'nullable',
                'zip_code' => 'nullable',
                'image' => 'image|mimes:jpg,png,jpeg|max:4096',
                'cover_image' => 'image|mimes:jpg,png,jpeg|max:4096',
                'language_id' => Rule::in($languages),
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json($this->withError(collect($validator->errors())->collapse()));
            }
            if ($request->hasFile('image')) {
                $image = $this->fileUpload($request->image, config('filelocation.userProfile.path'), null, null, 'webp', 99, $user->image, $user->image_driver);
                if ($image) {
                    $profileImage = $image['path'];
                    $ImageDriver = $image['driver'];
                }
            }

            if ($request->hasFile('cover_image')) {
                $coverImage = $this->fileUpload($request->cover_image, config('filelocation.userProfile.path'), null, null, 'webp', 99, $user->cover_image, $user->cover_image_driver);
                if ($image) {
                    $coverImagePath = $coverImage['path'];
                    $coverImageDriver = $coverImage['driver'];
                }
            }

            $user->image = $profileImage ?? $user->image;
            $user->image_driver = $ImageDriver ?? $user->image_driver;

            $user->cover_image = $coverImagePath ?? $user->cover_image;
            $user->cover_image_driver = $coverImageDriver ?? $user->cover_image_driver;

            $user->firstname = $req['first_name'];
            $user->lastname = $req['last_name'];
            $user->username = $req['username'];
            $user->bio = $req['bio'];
            $user->email = $req['email'];
            $user->phone_code = $req['phone_code'];
            $user->phone = $req['phone'];
            $user->address_one = $req['address_one'];
            $user->address_two = $req['address_two'];
            $user->city = $req['city'];
            $user->state = $req['state'];
            $user->zip_code = $req['zip_code'];
            $user->country_code = $req['country_code'];
            $user->country = $req['country'];
            $user->language_id = $req['language_id'];
            $user->save();

            if ($request->social_icon) {
                UserSocial::where('user_id', $user->id)->delete();
                foreach ($request->social_icon as $key => $value) {
                    UserSocial::create([
                        'user_id' => $user->id,
                        'social_icon' => $request->social_icon[$key],
                        'social_url' => $request->social_url[$key],
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
            DB::commit();
            return response()->json($this->withSuccess('Updated Successfully'));
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json($this->withError($e->getMessage()));
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $rules = [
                'current_password' => "required",
                'password' => "required|min:5|confirmed",
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json($this->withError(collect($validator->errors())->collapse()));
            }
            $user = Auth::user();
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = bcrypt($request->password);
                $user->save();
                return response()->json($this->withSuccess('Password Updated Successfully'));
            } else {
                return response()->json($this->withError('Current password did not match'));
            }
        }catch (\Exception $e){
            return response()->json($this->withError($e->getMessage()));
        }
    }

    public function getKycVerification($id = null)
    {
        try {
            $kyc = Kyc::with(['userKyc' => function ($query) {
                $query->where('user_id', auth()->id());
            }])->where('status', 1)
                ->when(isset($id), function ($query) use ($id) {
                    return $query->where('id', $id);
                })
                ->select('id', 'name', 'slug', 'input_form', 'status')
                ->get();

            $formated_kyc = $kyc->map(function ($kyc) {
                return [
                    'id' => $kyc->id,
                    'name' => $kyc->name,
                    'slug' => $kyc->slug,
                    'input_form' => $kyc->input_form,
                    'status' => $kyc->status,
                    'submitable' => $kyc->userKyc->isNotEmpty() ? true : false,
                    'submit_status' => $kyc->userKyc->isNotEmpty()
                        ? $kyc->userKyc->sortByDesc('created_at')->first()->status
                        : 0,
                ];
            });

            $data['kyc_list'] = $formated_kyc;

            $info = [
                'status' => '0 = Inactive, 1 = Active',
                'submit_status' => '0 = Pending, 1 = Verified, 2 = Rejected',
            ];
            return response()->json($this->withSuccess($data, $info));
        } catch (\Exception $e) {
            return response()->json($this->withError($e->getMessage()));
        }
    }

    public function kycVerificationSubmit(Request $request)
    {
        try {
            $checkKyc = UserKyc::where('user_id', auth()->id())->where('kyc_id',$request->type)->select('id','user_id','kyc_id','kyc_type','status')->latest()->first();

            if (!empty($checkKyc) && ($checkKyc->status == 0 || $checkKyc->status == 1)) {
                return response()->json($this->withError('KYC has already been submitted'));
            }

            $kyc = Kyc::where('id', $request->type)->where('status', 1)->first();
            if (!$kyc) {
                return response()->json($this->withError('KYC not found.'));
            }
            $params = $kyc->input_form;
            $reqData = $request->except('_token', '_method');
            $rules = [];
            if ($params != null) {
                foreach ($params as $key => $cus) {
                    $rules[$key] = [$cus->validation == 'required' ? $cus->validation : 'nullable'];
                    if ($cus->type == 'file') {
                        $rules[$key][] = 'image';
                        $rules[$key][] = 'mimes:jpeg,jpg,png';
                        $rules[$key][] = 'max:2048';
                    } elseif ($cus->type == 'text') {
                        $rules[$key][] = 'max:191';
                    } elseif ($cus->type == 'number') {
                        $rules[$key][] = 'numeric';
                    } elseif ($cus->type == 'textarea') {
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
            foreach ($request->except('_token', '_method', 'type') as $k => $v) {
                foreach ($params as $inKey => $inVal) {
                    if ($k == $inKey) {
                        if ($inVal->type == 'file' && $request->hasFile($inKey)) {
                            try {
                                $file = $this->fileUpload($request[$inKey], config('filelocation.kyc.path'));
                                $reqField[$inKey] = [
                                    'field_name' => $inVal->field_name,
                                    'field_label' => $inVal->field_label,
                                    'field_value' => $file['path'],
                                    'field_driver' => $file['driver'],
                                    'validation' => $inVal->validation,
                                    'type' => $inVal->type,
                                ];
                            } catch (\Exception $exp) {
                                return response()->json($this->withError("Could not upload your {$inKey}"));
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
            UserKyc::create([
                'user_id' => auth()->id(),
                'kyc_id' => $kyc->id,
                'kyc_type' => $kyc->name,
                'kyc_info' => $reqField
            ]);

            return response()->json($this->withSuccess("KYC Submitted Successfully"));
        } catch (\Exception $e) {
            return response()->json($this->withError($e->getMessage()));
        }
    }

    public function kycVerificationSubmitList()
    {
        try {
            $userKycs = UserKyc::where('user_id', Auth::id())->get();
            $info = [
                'status' => '0 = Pending, 1 = Verified, 2 = Rejected',
            ];
            return response()->json($this->withSuccess($userKycs, $info));
        } catch (\Exception $e) {
            return response()->json($this->withError($e->getMessage()));
        }
    }
}
