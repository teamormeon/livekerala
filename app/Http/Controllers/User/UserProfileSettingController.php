<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Models\Language;
use App\Models\Listing;
use App\Models\User;
use App\Models\UserSocial;
use App\Models\Viewer;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserProfileSettingController extends Controller
{
    use Upload;

    public function profile(Request $request)
    {
        $validator = Validator::make($request->all(), []);
        $data['user'] = Auth::user();
        $data['all_viewers_count'] = Viewer::where('user_id', $data['user']->id)->count();
        $data['user_information'] = User::with(['follower.get_follwer_user', 'following.get_following_user'])->findOrFail($data['user']->id);

        $data['social_links'] = UserSocial::where('user_id', $data['user']->id)->get();
        $data['listing_infos'] = Listing::where('user_id', $data['user']->id)->get();
        $data['languages'] = Language::all();
        $data['identityFormList'] = Kyc::where('status', 1)->get();
        if ($request->has('identity_type')) {
            $validator->errors()->add('identity', '1');
            $data['identity_type'] = $request->identity_type;
            $data['identityForm'] = Kyc::where('slug', trim($request->identity_type))->where('status', 1)->firstOrFail();
            return view('user_panel.user.profile.my_profile', $data)->withErrors($validator);
        }

        return view('user_panel.user.profile.my_profile', $data);
    }

    public function profileImageUpdate(Request $request)
    {
        $allowedExtensions = array('jpg', 'png', 'jpeg');
        $image = $request->profile_image;
        $this->validate($request, [
            'profile_image' => [
                'required',
                'max:4096',
                function ($fail) use ($image, $allowedExtensions) {
                    $ext = strtolower($image->getClientOriginalExtension());
                    if (($image->getSize() / 1000000) > 2) {
                        throw ValidationException::withMessages(['image' => "Images MAX  2MB ALLOW!"]);
                    }
                    if (!in_array($ext, $allowedExtensions)) {
                        throw ValidationException::withMessages(['image' => "Only png, jpg, jpeg images are allowed"]);
                    }
                }
            ]
        ]);
        $user = Auth::user();
        if ($request->hasFile('profile_image')) {
            $image = $this->fileUpload($request->profile_image, config('filelocation.userProfile.path'), null, null, 'webp', 99, $user->image, $user->image_driver);
            if ($image) {
                $profileImage = $image['path'];
                $ImageDriver = $image['driver'];
            }
        }
        $user->image = $profileImage ?? $user->image;
        $user->image_driver = $ImageDriver ?? $user->image_driver;
        $user->save();
        $src = getFile($user->image_driver, $user->image);
        return response()->json(['src' => $src,'message' => 'Updated Successfully.']);
    }

    public function profileCoverImageUpdate(Request $request)
    {
        $allowedExtensions = array('jpg', 'png', 'jpeg');
        $image = $request->user_cover_photo;
        $this->validate($request, [
            'user_cover_photo' => [
                'required',
                'max:4096',
                function ($fail) use ($image, $allowedExtensions) {
                    $ext = strtolower($image->getClientOriginalExtension());
                    if (($image->getSize() / 1000000) > 2) {
                        throw ValidationException::withMessages(['image' => "Images MAX  2MB ALLOW!"]);
                    }
                    if (!in_array($ext, $allowedExtensions)) {
                        throw ValidationException::withMessages(['image' => "Only png, jpg, jpeg images are allowed"]);
                    }
                }
            ]
        ]);
        $user = Auth::user();
        if ($request->hasFile('user_cover_photo')) {
            $image = $this->fileUpload($request->user_cover_photo, config('filelocation.userProfile.path'), null, null, 'webp', 99, $user->cover_image, $user->cover_image_driver);
            if ($image) {
                $profileCoverImage = $image['path'];
                $ImageDriver = $image['driver'];
            }
        }
        $user->cover_image = $profileCoverImage ?? $user->cover_image;
        $user->cover_image_driver = $ImageDriver ?? $user->cover_image_driver;
        $user->save();
        $src = getFile($user->cover_image_driver, $user->cover_image);
        return response()->json(['src' => $src,'message' => 'Updated Successfully.']);
    }

    public function profileUpdate(Request $request)
    {
        $languages = Language::all()->map(function ($item) {
            return $item->id;
        });
        throw_if(!$languages, 'Language not found.');

        $req = $request->except('_method', '_token');
        $user = Auth::user();
        $rules = [
            'firstname' => 'required|string|min:1|max:100',
            'lastname' => 'required|string|min:1|max:100',
            'email' => 'required|email:rfc,dns',
            'phone' => 'required|min:1|max:50',
            'username' => "sometimes|required|alpha_dash|min:5|unique:users,username," . $user->id,
            'address_one' => 'required|string|min:2|max:500',
            'language_id' => Rule::in($languages),
        ];

        $validator = Validator::make($req, $rules);

        if ($validator->fails()) {
            $validator->errors()->add('profile', '1');
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $response = $user->update([
                'language_id' => $req['language_id'],
                'firstname' => $req['firstname'],
                'lastname' => $req['lastname'],
                'username' => $req['username'],
                'email' => $req['email'],
                'phone' => $req['phone'],
                'website' => $req['website'],
                'address_one' => $req['address_one'],
                'address_two' => $req['address_two'],
                'bio' => $req['bio'],
            ]);
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
            throw_if(!$response, 'Something went wrong, While updating profile data');
            DB::commit();
            return back()->with('success', 'Profile updated Successfully.');
        } catch (\Exception $exception) {
            DB::rollBack();
            return back()->with('error', $exception->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        $rules = [
            'current_password' => "required",
            'password' => "required|min:5|confirmed",
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $user = Auth::user();
        try {
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = bcrypt($request->password);
                $user->save();
                return back()->with('success', 'Password Changes successfully.');
            } else {
                throw new \Exception('Current password did not match');
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


}
