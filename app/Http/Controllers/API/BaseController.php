<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Models\Amenity;
use App\Models\CountryCities;
use App\Models\Gateway;
use App\Models\Language;
use App\Models\Listing;
use App\Models\Subscriber;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BaseController extends Controller
{
    Use ApiResponse;

    public function appConfig()
    {
        $basic = basicControl();

        $data = [
            'id' => $basic->id,
            'theme' => $basic->theme,
            'site_title' => $basic->site_title,
            'primary_color' => $basic->primary_color,
            'secondary_color' => $basic->secondary_color,
            'time_zone' => $basic->time_zone,
            'base_currency' => $basic->base_currency,
            'currency_symbol' => $basic->currency_symbol,
            'admin_prefix' => $basic->admin_prefix,
            'is_currency_position' => $basic->is_currency_position,
            'paginate' => $basic->paginate,
            'registration' => $basic->registration == 1 ? 'Active' : 'Inactive',
            'fraction_number' => $basic->fraction_number,
            'sender_email' => $basic->sender_email,
            'favicon' => getFile($basic->favicon_driver, $basic->favicon),
            'site_logo' => getFile($basic->logo_driver, $basic->logo),
            'admin_logo_light' => getFile($basic->admin_logo_driver, $basic->admin_logo),
            'admin_logo_dark' => getFile($basic->admin_dark_mode_logo_driver, $basic->admin_dark_mode_logo),
            'paymentSuccessUrl' => route('success'),
            'paymentFailedUrl' => route('failed'),
        ];
        return response()->json($this->withSuccess($data));

    }

    public function languages(Request $request)
    {
        try {
            if (!$request->id) {
                $data['languages'] = Language::select(['id', 'name', 'short_name','flag','flag_driver'])->where('status', 1)->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'short_name' => $item->short_name,
                        'flag' => getFile($item->flag_driver, $item->flag),
                    ];
                });
                return response()->json($this->withSuccess($data));
            }
            $lang = Language::where('status', 1)->find($request->id);
            if (!$lang) {
                return response()->json($this->withError('Record not found'));
            }

            $json = file_get_contents(resource_path('lang/') . $lang->short_name . '.json');
            if (empty($json)) {
                return response()->json($this->withError('File Not Found.'));
            }

            $json = json_decode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return response()->json($this->withSuccess($json));
        } catch (\Exception $e) {
            return response()->json($this->withError($e->getMessage()));
        }
    }

    public function gateways()
    {
        $gateways = Gateway::where('status', 1)->orderby('sort_by','ASC')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'sort_by' => $item->sort_by,
                'image' => getFile($item->driver, $item->image),
                'status' => $item->status,
                'parameters' => $item->parameters,
                'currencies' => $item->currencies,
                'extra_parameters' => $item->extra_parameters,
                'supported_currency' => $item->supported_currency,
                'receivable_currencies' => $item->receivable_currencies,
                'description' => $item->description,
                'currency_type' => $item->currency_type,
                'is_sandbox' => $item->is_sandbox,
                'environment' => $item->environment,
                'is_manual' => $item->is_manual,
                'note' => $item->note,
                'is_subscription' => $item->is_subscription,
                'subscription_on' => $item->subscription_on,
                'created_at' => $item->created_at,
            ];
        });
        if (!$gateways){
            return response()->json($this->withError('Gateway data not found'));
        }
        return response()->json($this->withSuccess($gateways));
    }

    public function countryList()
    {
        $countries = DB::table('countries')->select('id','iso2','iso3','name','latitude','longitude','created_at')->where('status', 1)->get();
        $info = [
            'status' => '0 = Inactive, 1 = Active',
        ];
        return response()->json($this->withSuccess($countries, $info));
    }
    public function stateList($country_id = null)
    {
        $states = DB::table('states')->select('id','country_id','name','status')
            ->when(isset($country_id), function ($query) use ($country_id) {
                return $query->where('country_id', $country_id);
            })
            ->where('status', 1)->get();
        $info = [
            'status' => '0 = Inactive, 1 = Active',
        ];
        return response()->json($this->withSuccess($states, $info));
    }

    public function cityList($state_id = null)
    {
        $cities = CountryCities::select('id','state_id','name','latitude','longitude','status')->where('status', 1)
            ->when(isset($state_id) , function ($query) use($state_id){
                return $query->where('state_id', $state_id);
            })->get();
        $info = [
            'status' => '0 = Inactive, 1 = Active',
        ];
        return response()->json($this->withSuccess($cities, $info));
    }

    public function listingCities()
    {
        $cities = Listing::with('get_cities:id,country_id,state_id,name,latitude,longitude,status')->where('city_id', '!=', null)->get()->pluck('get_cities');
        $info = [
            'status' => '0 = Inactive, 1 = Active',
        ];
        return response()->json($this->withSuccess($cities, $info));
    }

    public function amenities()
    {
        $amenities = Amenity::with('details:id,amenity_id,title')->where('status',1)->get();
        $formatedAmenities = $amenities->map(function ($item) {
            return [
                'id' => $item->id,
                'icon' => $item->icon,
                'name' => $item->details->title,
                'status' => $item->status,
                'created_at' => $item->created_at,
            ];
        });
        $info = [
            'status' => '0 = Inactive, 1 = Active',
        ];
        return response()->json($this->withSuccess($formatedAmenities, $info));
    }

    public function contactSend(Request $request)
    {
        $rules = [
            'name' => 'required|max:50',
            'email' => 'required|email|max:91',
            'subject' => 'required|max:100',
            'message' => 'required|max:1000',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($this->withSuccess(collect($validator->errors())->collapse()));
        }

        $name = $request['name'];
        $email_from = $request['email'];
        $subject = $request['subject'];
        $message = $request['message'] . "<br>Regards<br>" . $name;
        $from = $email_from;

        Mail::to(basicControl()->sender_email)->send(new SendMail($from, $subject, $message));
        return response()->json($this->withSuccess('Mail has been sent'));
    }

    public function subscribe(Request $request)
    {
        $rules = [
            'email' => 'required|email|max:91|unique:subscribers',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($this->withSuccess(collect($validator->errors())->collapse()));
        }

        $data = new Subscriber();
        $data->email = $request->email;
        $data->save();
        return response()->json($this->withSuccess('Subscribed Successfully'));
    }


}
