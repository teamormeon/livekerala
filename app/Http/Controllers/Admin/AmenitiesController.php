<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\AmenityDetails;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class AmenitiesController extends Controller
{
    public function amenities()
    {
        return view('admin.amenities.index');
    }

    public function amenitiesSearch(Request $request)
    {
        $search = $request->search['value']??null;
        $filterStatus = $request->filterStatus;
        $filterDate = explode('-', $request->filterDate);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;

        $amenities = Amenity::query()->with(['details'])
            ->orderBy('id', 'desc')
            ->when(!empty($search), function ($query) use ($search) {
                return $query->whereHas('details', function ($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%$search%");
                });
            })
            ->when(isset($filterStatus), function ($query) use ($filterStatus) {
                if ($filterStatus == 'all') {
                    return $query->where('status', '!=', null);
                }
                return $query->where('status', $filterStatus);
            })
            ->when(!empty($request->filterDate) && $endDate == null, function ($query) use ($startDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $query->whereDate('created_at', $startDate);
            })
            ->when(!empty($request->filterDate) && $endDate != null, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $endDate = Carbon::createFromFormat('d/m/Y', trim($endDate));
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });

        return DataTables::of($amenities)
            ->addColumn('checkbox', function ($item) {
                return ' <input type="checkbox" id="chk-' . $item->id . '"
                           class="form-check-input row-tic tic-check" name="check" value="' . $item->id . '"
                           data-id="' . $item->id . '">';
            })
            ->addColumn('amenities', function ($item) {
                return '<div class="d-flex align-items-center">
                            <i class="'.$item->icon.'"></i>
                            <span class="d-block mb-0 ps-3">'.optional($item->details)->title.'</span>
                        </div>';
            })
            ->addColumn('status', function ($item) {
                $badgeClass =  $item->status == 1 ? 'success text-success' : 'danger text-danger';
                $legendBgClass =  $item->status == 1 ? 'success' : 'danger';
                $status = $item->status == 1 ? 'Active' : 'Deactive';
                return '<span class="badge bg-soft-'.$badgeClass.'"><span class="legend-indicator bg-'.$legendBgClass.'"></span>'.$status.'</span>';
            })
            ->addColumn('action', function ($item) {
                $EditUrl = route('admin.amenities.edit', $item->id);
                $deleteUrl = route('admin.amenities.delete', $item->id);

                $canEdit = adminAccessRoute(config('role.amenities.access.edit'));
                $canDelete = adminAccessRoute(config('role.amenities.access.delete'));

                $actions = '';
                if ($canEdit) {
                    $actions .= '<a class="btn btn-white btn-sm" href="'.$EditUrl.'">
                                <i class="bi-pencil-fill me-1"></i>'.trans("Edit").'
                            </a>';
                }
                if ($canDelete) {
                    $actions .= '<div class="btn-group">
                                <button type="button" class="btn btn-white btn-icon btn-sm dropdown-toggle dropdown-toggle-empty" id="productsEditDropdown1" data-bs-toggle="dropdown" aria-expanded="false"></button>

                                <div class="dropdown-menu dropdown-menu-end mt-1" aria-labelledby="productsEditDropdown1" style="">
                                    <a class="dropdown-item deleteBtn" href="javascript:void(0)" data-route="'.$deleteUrl.'" data-amenities-name="'.optional($item->details)->title.'" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="bi-trash dropdown-item-icon"></i> '.trans("Delete").'
                                    </a>
                                </div>
                            </div>
                        </div>';
                }
                return $actions ?: '-';
            })
            ->rawColumns(['checkbox','amenities','status', 'action'])
            ->make(true);
    }

    public function amenitiesCreate()
    {
        $languages = Language::all();
        return view('admin.amenities.create', compact('languages'));
    }

    public function amenitiesStore(Request $request, $language)
    {
        $rules = [
            'name.*' => 'required|max:100|unique:amenity_details,title',
            'icon' => 'required|max:100',
        ];
        $message = [
            'name.*.required' => __('Amenity name field is required'),
            'icon.required' => __('Icon field is required'),
        ];
        $request->validate($rules, $message);

        DB::beginTransaction();
        try {
            $amenity = new Amenity();
            $amenity->icon = $request->icon;
            $amenity->status = $request->status;
            $amenity->save();

            $amenity->details()->create([
                'language_id' => $language,
                'title' => $request["name"][$language],
            ]);
            DB::commit();
            return back()->with('success', __('Amenity saved Successfully'));
        }catch (\Exception $exception){
            DB::rollBack();
            return back()->with('error', __('Something went wrong'));
        }
    }


    public function amenitiesEdit($id)
    {
        $data['id'] = $id;
        $data['languages'] = Language::orderBy('default_status', 'desc')->get();
        $data['amenityDetails'] = AmenityDetails::with('amenity')->where('amenity_id', $id)->get()->groupBy('language_id');
        return view('admin.amenities.edit', $data);
    }



    public function amenitiesUpdate(Request $request, $id, $language_id)
    {
        $rules = [
            'name.*' => [
                'required',
                'max:100',
                Rule::unique('amenity_details', 'title')
                    ->where(function ($query) use ($language_id) {
                        return $query->where('language_id', $language_id);
                    })
                    ->ignore($id, 'amenity_id')
            ],
            'icon' => 'sometimes|max:100',
        ];


        $message = [
            'name.*.required' => ('Amenity name field is required'),
            'icon.required' => ('Icon field is required'),
        ];

        $request->validate($rules, $message);

        DB::beginTransaction();
        try {
            $amenity = Amenity::findOrFail($id);
            $language = Language::select('id','default_status')->findOrFail($language_id);

            if ($language->default_status){
                $amenity->icon = $request->icon;
                $amenity->status = $request->status;
                $amenity->save();
            }

            $amenity->details()->updateOrCreate([
                'amenity_id' => $id,
                'language_id' => $language_id
            ],
                [
                    'title' => $request["name"][$language_id],
                ]
            );
            DB::commit();
            return back()->with('success', ('Amenity Updated Successfully'));
        }catch (\Exception $exception){
            DB::rollBack();
            return back()->with('error', ('Something went wrong'));
        }
    }


    public function amenitiesDelete($id)
    {
        $amenity = Amenity::findOrFail($id);
        $amenity->details()->delete();
        $amenity->delete();
        return back()->with('success', __('Amenity has been deleted'));
    }

    public function amenitiesDeleteMultiple(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select any Item.');
            return response()->json(['error' => 1]);
        } else {
            foreach ($request->strIds as $id) {
                $ListingCategory = Amenity::with('details')->findOrFail($id);
                $ListingCategory->details()->delete();
                $ListingCategory->delete();
            }
            session()->flash('success', 'Amenity has been Deleted');
            return response()->json(['success' => 1]);
        }
    }
}
