<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CountryCities;
use App\Models\CountryStates;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CountryController extends Controller
{
    use Upload;

    public function list()
    {
        $allData = Country::selectRaw(
            'COUNT(*) as totalCountry,
                     SUM(status = 1) as totalActiveCountry,
                     SUM(status = 0) as totalInactiveCountry')
            ->first();

        $data['totalCountry'] = $allData->totalCountry;
        $data['totalActiveCountry'] = $allData->totalActiveCountry;
        $data['totalInactiveCountry'] = $allData->totalInactiveCountry;

        $data['activeCountryPercentage'] = ($data['totalCountry'] > 0) ? ($data['totalActiveCountry'] / $data['totalCountry']) * 100 : 0;
        $data['inactiveCountryPercentage'] = ($data['totalCountry'] > 0) ? ($data['totalInactiveCountry'] / $data['totalCountry']) * 100 : 0;

        return view('admin.countries.list' , $data);
    }

    public function countryList(Request $request)
    {

        $countries = Country::when(!empty($request->search['value']), function ($query) use ($request) {
            $query->where('name', 'LIKE', '%' . $request->search['value'] . '%')
                ->orWhere('iso3', 'LIKE', '%' . $request->search['value'] . '%');
        });

        return DataTables::of($countries)
            ->addColumn('no', function () {
                static $counter = 0;
                $counter++;
                return $counter;
            })
            ->addColumn('checkbox', function ($item) {
                return ' <input type="checkbox" id="chk-' . $item->id . '"
                                       class="form-check-input row-tic tic-check" name="check" value="' . $item->id . '"
                                       data-id="' . $item->id . '">';
            })
            ->addColumn('image', function ($item) {
                $image = $item->image;
                if (!$image) {
                    $firstLetter = substr($item->name, 0, 1);
                    return '<div class="avatar avatar-sm avatar-soft-primary avatar-circle">
                                <span class="avatar-initials">' . $firstLetter . '</span>
                            </div>
                            <span class="ms-1 fs-6 text-body">' . optional($item)->name . '</span>';
                } else {
                    $url = getFile($item->image_driver, $item->image);
                    return '<div class="avatar avatar-sm avatar-circle">
                                <img class="avatar-img" src="' . $url . '" alt="Image Description" />
                            </div
                            <span class="ms-1 fs-6 text-body">' . optional($item)->name . '</span>';

                }
            })
            ->addColumn('short_name', function ($item) {
                return '<a class="d-flex align-items-center me-2" href="#">
                            <div class="flex-grow-1 ms-3">
                              <span class="fs-6 text-body">' . optional($item)->iso3 . '</span>
                            </div>
                        </a>';
            })
            ->addColumn('status', function ($item) {
                if ($item->status == 0) {
                    return ' <span class="badge bg-soft-warning text-warning">
                                <span class="legend-indicator bg-warning"></span> ' . trans('InActive') . '
                             </span>';
                } else {
                    return '<span class="badge bg-soft-success text-success">
                                <span class="legend-indicator bg-success"></span> ' . trans('Active') . '
                            </span>';
                }
            })
            ->addColumn('action', function ($item) {

                $editUrl = route('admin.country.edit', $item->id);
                $deleteurl = route('admin.country.delete', $item->id);
                $stateList = route('admin.country.all.state', $item->id);

                return '<div class="btn-group" role="group">
                      <a href="' . $editUrl . '" class="btn btn-white btn-sm edit_user_btn">
                        <i class="bi-pencil-fill me-1"></i> ' . trans("Edit") . '
                      </a>
                    <div class="btn-group">
                      <button type="button" class="btn btn-white btn-icon btn-sm dropdown-toggle dropdown-toggle-empty" id="userEditDropdown" data-bs-toggle="dropdown" aria-expanded="false"></button>
                      <div class="dropdown-menu dropdown-menu-end mt-1" aria-labelledby="userEditDropdown">
                       <a class="dropdown-item" href="' . $stateList . '">
                          <i class="fas fa-city dropdown-item-icon"></i> ' . trans("Manage State") . '
                        </a>
                       <a class="dropdown-item" href="' . $deleteurl . '">
                          <i class="bi-trash dropdown-item-icon"></i> ' . trans("Delete") . '
                        </a>
                      </div>
                    </div>
                  </div>';
            })
            ->rawColumns(['checkbox','short_name', 'status', 'action','image'])
            ->make(true);
    }


    public function countryAdd(){
        return view('admin.countries.add');
    }

    public function countryStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:countries',
            'status' => 'required',
            'iso2' => 'required',
            'iso3' => 'required',
            'phone_code' => 'required|numeric',
            'region' => 'required',
            'subregion' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }else{

            if ($request->hasFile('image')) {
                $photo = $this->fileUpload($request->image, config('filelocation.country.path'), null, null, 'webp', 99);
                $image = $photo['path'];
                $image_driver = $photo['driver'];
            }
            $country = new Country();
            $country->iso2 = $request->iso2;
            $country->name = $request->name;
            $country->status = $request->status;
            $country->image = $image ?? null;
            $country->image_driver = $image_driver ?? null;
            $country->phone_code = $request->phone_code;
            $country->iso3 = $request->iso3;
            $country->region = $request->region;
            $country->subregion = $request->subregion;
            $country->save();
            return back()->with('success','Country Added Successfully.');
        }

    }

    public function countryEdit($id){
        $data['country'] = Country::with('state', 'city')->where('id', $id)->first();
        if ($data['country']){
            return view('admin.countries.edit',$data);
        }else{
            return back()->with('error','Country Not Found');
        }
    }

    public function countryUpdate (Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:countries',
            'status' => 'required',
            'iso2' => 'required',
            'iso3' => 'required',
            'phone_code' => 'required|numeric',
            'region' => 'required',
            'subregion' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $country = Country::with('state','city')->where('id',$id)->firstOr(function () {
            throw new \Exception('This Country is not available now');
        });
        try {
            if ($request->hasFile('image')) {
                $photo = $this->fileUpload($request->image, config('filelocation.country.path'), null, null, 'webp', 99, $country->image, $country->image_driver);
                $image = $photo['path'];
                $image_driver = $photo['driver'];
            }

            $country->update([
                'name'=>$request->name,
                'iso2'=>$request->iso2,
                'iso3'=>$request->iso3,
                'status'=>$request->status,
                'phone_code'=>$request->phone_code,
                'image'=>$image ?? $country->image,
                'image_driver'=>$image_driver ?? $country->image_driver,
                'region'=>$request->region,
                'subregion'=>$request->subregion,
            ]);
            return back()->with('success','Country Updated Successfully.');
        }catch (\Exception $e){
            return back()->with('error', $e->getMessage());
        }
    }

    public function countryDelete($id){
        DB::beginTransaction();
        try {
            $country = Country::with('state','city')->where('id',$id)->firstOr(function () {
                throw new \Exception('This Country is not available now');
            });
            $countryState = $country->state->where('country_id',$id)->all();
            $countryCity = $country->city->where('country_id',$id)->all();

            if ($countryState){
                foreach ($countryState as $item){
                    $item->delete();
                }
            }
            if ($countryCity){
                foreach ($countryCity as $item){
                    $item->delete();
                }
            }
            $this->fileDelete($country->image_driver, $country->image);
            $country->delete();
            DB::commit();
            return back()->with('success','Country Deleted Successfully.');
        }catch (\Exception $e){
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function deleteMultiple(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select any Country.');
            return response()->json(['error' => 1]);
        } else {
            DB::transaction(function () use ($request) {
                $countries = Country::with('state','city')->whereIn('id', $request->strIds)->get();
                foreach ($countries as $country) {
                    $this->fileDelete($country->image_driver, $country->image);
                    $country->state->each->delete();
                    $country->city->each->delete();
                }
                Country::whereIn('id', $request->strIds)->delete();
            });
            session()->flash('success', 'Selected Data deleted successfully');
            return response()->json(['success' => 1]);
        }
    }

    public function statelist($id){
        $allData = CountryStates::selectRaw(
            'COUNT(*) as totalState,
                         SUM(status = 1) as totalActiveState,
                         SUM(status = 0) as totalInactiveState')
            ->where('country_id', $id)
            ->first();

        $data['totalState'] = $allData->totalState;
        $data['totalActiveState'] = $allData->totalActiveState;
        $data['totalInactiveState'] = $allData->totalInactiveState;
        $data['activeStatePercentage'] = ($data['totalState'] > 0) ? ($data['totalActiveState'] / $data['totalState']) * 100 : 0;
        $data['inactiveStatePercentage'] = ($data['totalState'] > 0) ? ($data['totalInactiveState'] / $data['totalState']) * 100 : 0;

        return view('admin.countries.state.list',$data, compact('id'));
    }

    public function countryStateList(Request $request,$country)
    {
        $states = CountryStates::query()->where('country_id',$country);
        if (!empty($request->search['value'])) {
            $states->where('name', 'LIKE', '%' . $request->search['value'] . '%');
        }
        return DataTables::of($states)
            ->addColumn('no', function () {
                static $counter = 0;
                $counter++;
                return $counter;
            })
            ->addColumn('checkbox', function ($item) {
                return ' <input type="checkbox" id="chk-' . $item->id . '"
                                       class="form-check-input row-tic tic-check" name="check" value="' . $item->id . '"
                                       data-id="' . $item->id . '">';
            })
            ->addColumn('name', function ($item) {
                return '<a class="d-flex align-items-center me-2" href="#">
                            <div class="flex-grow-1 ms-3">
                              <span class="fs-6 text-body">' . optional($item)->name . '</span>
                            </div>
                        </a>';
            })
            ->addColumn('code', function ($item) {
                return '<a class="d-flex align-items-center me-2" href="#">
                            <div class="flex-grow-1 ms-3">
                              <span class="fs-6 text-body">' . optional($item)->country_code . '</span>
                            </div>
                        </a>';
            })
            ->addColumn('status', function ($item) {
                if ($item->status == 0) {
                    return ' <span class="badge bg-soft-warning text-warning">
                                <span class="legend-indicator bg-warning"></span> ' . trans('InActive') . '
                             </span>';
                } else {
                    return '<span class="badge bg-soft-success text-success">
                                <span class="legend-indicator bg-success"></span> ' . trans('Active') . '
                            </span>';
                }
            })
            ->addColumn('action', function ($item) {

                $editUrl = route('admin.country.state.edit', [$item->country_id, $item->id ]);
                $deleteurl = route('admin.country.state.delete', [$item->country_id, $item->id ]);
                $cityList = route('admin.country.state.all.city', [$item->country_id, $item->id ]);

                return '<div class="btn-group" role="group">
                      <a href="' . $editUrl . '" class="btn btn-white btn-sm edit_user_btn">
                        <i class="bi-pencil-fill me-1"></i> ' . trans("Edit") . '
                      </a>
                    <div class="btn-group">
                      <button type="button" class="btn btn-white btn-icon btn-sm dropdown-toggle dropdown-toggle-empty" id="userEditDropdown" data-bs-toggle="dropdown" aria-expanded="false"></button>
                      <div class="dropdown-menu dropdown-menu-end mt-1" aria-labelledby="userEditDropdown">
                           <a class="dropdown-item" href="' . $cityList . '">
                              <i class="fas fa-city dropdown-item-icon"></i> ' . trans("Manage City") . '
                           </a>
                           <a class="dropdown-item text-danger" href="' . $deleteurl . '">
                              <i class="bi-trash dropdown-item-icon text-danger"></i> ' . trans("Delete") . '
                           </a>
                      </div>
                    </div>
                  </div>';
            })
            ->rawColumns(['checkbox','name','code', 'status', 'action'])
            ->make(true);
    }

    public function countryAddState($id){
        $data['country'] = Country::with('state','city')->where('id',$id)->first();
        if ($data['country']){
            return view('admin.countries.state.add',$data);
        }else{
            return back()->with('error','Country Not Found');
        }
    }
    public function countryStateStore(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }else{
            $state = new CountryStates();
            $state->country_id = $request->country_id;
            $state->country_code = $request->country_code;
            $state->name = $request->name;
            $state->status = $request->status;
            $state->save();
            return back()->with('success','State Added Successfully.');
        }
    }

    public function countryStateEdit($country,$state)
    {
        $data['state'] = CountryStates::with('country','cities')->where('id',$state)->where('country_id', $country)->first();
        if ($data['state']){
            return view('admin.countries.state.edit',$data);
        }else{
            return back()->with('error','State Not Found');
        }
    }

    public function countryStateUpdate(Request $request,$country,$state)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $state = CountryStates::with('country','cities')->where('country_id',$country)->where('id',$state)->firstOr(function () {
            throw new \Exception('This State is not available now');
        });
        try {
            $state->update([
                'name'=>$request->name,
                'status'=>$request->status,
            ]);
            return back()->with('success','State Updated Successfully.');
        }catch (\Exception $e){
            return back()->with('error', $e->getMessage());
        }
    }

    public function countryStateDelete($country,$state)
    {
        try {
            $State = CountryStates::with('cities')->where('id',$state)->firstOr(function () {
                throw new \Exception('This Country is not available now');
            });
            $stateCity = $State->cities->where('state_id',$state)->all();
            if ($stateCity){
                foreach ($stateCity as $item){
                    $item->delete();
                }
            }
            $State->delete();
            return back()->with('success','State Deleted Successfully.');
        }catch (\Exception $e){
            return back()->with('error', $e->getMessage());
        }

    }

    public function deleteMultipleState(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select any Country.');
            return response()->json(['error' => 1]);
        } else {
            DB::transaction(function () use ($request) {
                $states = CountryStates::with('cities')->whereIn('id', $request->strIds)->get();
                foreach ($states as $item) {
                    $item->cities->each->delete();
                }
                CountryStates::whereIn('id', $request->strIds)->delete();
            });
            session()->flash('success', 'Selected Data deleted successfully');
            return response()->json(['success' => 1]);
        }
    }


    public function citylist($country,$state)
    {
        $allData = CountryCities::selectRaw(
            'COUNT(*) as allCities,
                        SUM(status = 1) as allActiveCities,
                        SUM(status = 0) as allInactiveCities')
            ->where('country_id', $country)
            ->where('state_id', $state)
            ->first();

        $data['allCities'] = $allData->allCities;
        $data['allActiveCities'] = $allData->allActiveCities;
        $data['allInactiveCities'] = $allData->allInactiveCities;

        $data['activeCityPercentage'] = ($data['allCities'] > 0) ? ($data['allActiveCities'] / $data['allCities']) * 100 : 0;
        $data['inactiveCityPercentage'] = ($data['allCities'] > 0) ? ($data['allInactiveCities'] / $data['allCities']) * 100 : 0;

        return view('admin.countries.city.list',$data , compact('country','state'));
    }

    public function countryStateCityList(Request $request,$country,$state)
    {
        $city = CountryCities::query()->with('state')->where('country_id',$country)->where('state_id',$state);
        if (!empty($request->search['value'])) {
            $city->where('name', 'LIKE', '%' . $request->search['value'] . '%');
        }

        return DataTables::of($city)
            ->addColumn('no', function () {
                static $counter = 0;
                $counter++;
                return $counter;
            })
            ->addColumn('checkbox', function ($item) {
                return ' <input type="checkbox" id="chk-' . $item->id . '"
                                       class="form-check-input row-tic tic-check" name="check" value="' . $item->id . '"
                                       data-id="' . $item->id . '">';
            })
            ->addColumn('name', function ($item) {
                return '<a class="d-flex align-items-center me-2" href="#">
                            <div class="flex-grow-1 ms-3">
                              <span class="fs-6 text-body">' . optional($item)->name . '</span>
                            </div>
                        </a>';
            })
            ->addColumn('code', function ($item) {
                return '<a class="d-flex align-items-center me-2" href="#">
                            <div class="flex-grow-1 ms-3">
                              <span class="fs-6 text-body">' . optional($item)->country_code . '</span>
                            </div>
                        </a>';
            })
            ->addColumn('state', function ($item) {
                return '<a class="d-flex align-items-center me-2" href="#">
                            <div class="flex-grow-1 ms-3">
                              <span class="fs-6 text-body">' . optional($item->state)->name . '</span>
                            </div>
                        </a>';
            })
            ->addColumn('status', function ($item) {
                if ($item->status == 0) {
                    return ' <span class="badge bg-soft-warning text-warning">
                                <span class="legend-indicator bg-warning"></span> ' . trans('InActive') . '
                             </span>';
                } else {
                    return '<span class="badge bg-soft-success text-success">
                                <span class="legend-indicator bg-success"></span> ' . trans('Active') . '
                            </span>';
                }
            })
            ->addColumn('action', function ($item) {

                $editUrl = route('admin.country.state.city.edit', [$item->country_id,$item->state_id, $item->id ]);
                $deleteurl = route('admin.country.state.city.delete', [$item->country_id, $item->state_id, $item->id ]);

                return '<div class="btn-group" role="group">
                      <a href="' . $editUrl . '" class="btn btn-white btn-sm edit_user_btn">
                        <i class="bi-pencil-fill me-1"></i> ' . trans("Edit") . '
                      </a>
                    <div class="btn-group">
                      <button type="button" class="btn btn-white btn-icon btn-sm dropdown-toggle dropdown-toggle-empty" id="userEditDropdown" data-bs-toggle="dropdown" aria-expanded="false"></button>
                      <div class="dropdown-menu dropdown-menu-end mt-1" aria-labelledby="userEditDropdown">
                       <a class="dropdown-item text-danger" href="' . $deleteurl . '">
                          <i class="bi-trash dropdown-item-icon text-danger"></i> ' . trans("Delete") . '
                        </a>
                      </div>
                    </div>
                  </div>';
            })
            ->rawColumns(['checkbox','name','code', 'status','state', 'action'])
            ->make(true);
    }

    public function countryStateAddCity($country, $state)
    {
        $data['country'] = Country::with('state','city')->where('id',$country)->first();
        $data['state'] = CountryStates::with('country','cities')->where('id',$state)->first();
        if ($data['country'] && $data['state']){
            return view('admin.countries.city.add',$data);
        }else{
            return back()->with('error','Country or State Not Found');
        }
    }

    public function countryStateStoreCity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }else{
            $city = new CountryCities();
            $city->country_id = $request->country_id;
            $city->state_id = $request->state_id;
            $city->country_code = $request->country_code;
            $city->name = $request->name;
            $city->latitude = $request->latitude;
            $city->longitude = $request->longitude;
            $city->status = $request->status;
            $city->save();
            return back()->with('success','City Added Successfully.');
        }
    }

    public function countryStateCityEdit($country,$state,$city)
    {
        $data['city'] = CountryCities::where('id',$city)->where('country_id', $country)->where('state_id', $state)->first();
        if ($data['city']){
            return view('admin.countries.city.edit',$data);
        }else{
            return back()->with('error','City Not Found');
        }
    }

    public function countryStateCityUpdate(Request $request,$country,$state,$city)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $city = CountryCities::where('country_id',$country)->where('state_id',$state)->where('id',$city)->firstOr(function () {
            throw new \Exception('This City is not available now');
        });
        try {
            $city->update([
                'name'=>$request->name,
                'latitude'=>$request->latitude,
                'longitude'=>$request->longitude,
                'status'=>$request->status,
            ]);
            return back()->with('success','City Updated Successfully.');
        }catch (\Exception $e){
            return back()->with('error', $e->getMessage());
        }
    }

    public function countryStateCityDelete($country,$state,$city)
    {
        try {
            $city = CountryCities::where('country_id',$country)->where('state_id',$state)->where('id',$city)->firstOr(function () {
                throw new \Exception('This Country is not available now');
            });
            $city->delete();
            return back()->with('success','City Deleted Successfully.');
        }catch (\Exception $e){
            return back()->with('error', $e->getMessage());
        }
    }

    public function deleteMultipleStateCity(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select any Country.');
            return response()->json(['error' => 1]);
        } else {
            DB::transaction(function () use ($request) {
                CountryCities::whereIn('id', $request->strIds)->delete();
            });
            session()->flash('success', 'Selected Data deleted successfully');
            return response()->json(['success' => 1]);
        }
    }
}
