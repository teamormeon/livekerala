<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\ListingCategory;
use App\Models\ListingCategoryDetails;
use App\Models\Package;
use App\Models\PurchasePackage;
use App\Traits\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ListingCategoryController extends Controller
{
    use Upload;
    public function listingCategory()
    {
        return view('admin.listing_category.index');
    }

    public function listingCategorySearch(Request $request)
    {
        $search = $request->search['value']??null;
        $filterStatus = $request->filterStatus;
        $filterDate = explode('-', $request->filterDate);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;

        $listingCategories = ListingCategory::query()->with(['details'])
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

        return DataTables::of($listingCategories)
            ->addColumn('checkbox', function ($item) {
                return ' <input type="checkbox" id="chk-' . $item->id . '"
                           class="form-check-input row-tic tic-check" name="check" value="' . $item->id . '"
                           data-id="' . $item->id . '">';
            })
            ->addColumn('category', function ($item) {
                return '<div class="d-flex align-items-center">
                            <i class="'.$item->icon.'"></i>
                            <span class="d-block mb-0 ps-3">'.optional($item->details)->name.'</span>
                        </div>';
            })
            ->addColumn('status', function ($item) {
                $badgeClass =  $item->status == 1 ? 'success text-success' : 'danger text-danger';
                $legendBgClass =  $item->status == 1 ? 'success' : 'danger';
                $status = $item->status == 1 ? 'Active' : 'Deactive';
                return '<span class="badge bg-soft-'.$badgeClass.'"><span class="legend-indicator bg-'.$legendBgClass.'"></span>'.$status.'</span>';
            })
            ->addColumn('action', function ($item) {
                $EditUrl = route('admin.listing.category.edit', $item->id);
                $deleteUrl = route('admin.listing.category.delete', $item->id);

                $canEdit = adminAccessRoute(config('role.listing_category.access.edit'));
                $canDelete = adminAccessRoute(config('role.listing_category.access.delete'));

                $actions = '';
                if ($canEdit) {
                    $actions .= '<a class="btn btn-white btn-sm" href="'.$EditUrl.'">
                                <i class="bi-pencil-fill me-1"></i>'.trans("Edit").'
                            </a>';
                }
                if ($canDelete) {
                    $actions .= '<div class="btn-group">
                            <button type="button" class="btn btn-white btn-icon btn-sm dropdown-toggle dropdown-toggle-empty"
                                    id="productsEditDropdown1" data-bs-toggle="dropdown" aria-expanded="false">
                            </button>
                            <div class="dropdown-menu dropdown-menu-end mt-1" aria-labelledby="productsEditDropdown1">
                                <a class="dropdown-item deleteBtn" href="javascript:void(0)" data-route="'.$deleteUrl.'" data-category-name="'.optional($item->details)->name.'" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="bi-trash dropdown-item-icon"></i> '.trans("Delete").'
                                </a>
                            </div>
                          </div>';
                }
                return $actions ?: '-';
            })
            ->rawColumns(['checkbox','category','status', 'action'])
            ->make(true);
    }

    public function listingCategoryCreate()
    {
        $languages = Language::all();
        return view('admin.listing_category.create', compact('languages'));
    }

    public function listingCategoryStore(Request $request, $language)
    {
        $rules = [
            'name.*' => 'required|max:100|unique:listing_category_details,name',
            'icon' => 'required|max:100',
            'mobile_app_image' => 'nullable|mimes:jpg,jpeg,png'
        ];
        $message = [
            'name.*.required' => __('Category name field is required'),
            'icon.required' => __('Icon field is required'),
        ];
        $request->validate($rules, $message);

        DB::beginTransaction();
        try {
            $listingCategory = new ListingCategory();

            if ($request->hasFile('mobile_app_image')) {
                try {
                    $image = $this->fileUpload($request->mobile_app_image, config('filelocation.listing_category.path'), null, null, 'webp', 99);
                    if ($image) {
                        $listingCategory->mobile_app_image = $image['path'];
                        $listingCategory->image_driver = $image['driver'];
                    }
                } catch (\Exception $exp) {
                    return back()->with('error', __('Image could not be uploaded.'));
                }
            }

            $listingCategory->icon = $request->icon;
            $listingCategory->status = $request->status;
            $listingCategory->save();

            $listingCategory->details()->create([
                'language_id' => $language,
                'name' => $request["name"][$language],
            ]);
            DB::commit();
            return back()->with('success', __('Listing Category saved Successfully'));
        }catch (\Exception $exception){
            DB::rollBack();
            return back()->with('error', __('Something went wrong'));
        }
    }

    public function listingCategoryEdit($id)
    {
        $data['id'] = $id;
        $data['languages'] = Language::orderBy('default_status', 'desc')->get();
        $data['listingCategoryDetails'] = ListingCategoryDetails::with('category')->where('listing_category_id', $id)->get()->groupBy('language_id');
        return view('admin.listing_category.edit', $data);
    }

    public function listingCategoryUpdate(Request $request, $id, $language_id)
    {
        $rules = [
            'name.*' => 'required|max:100|unique:listing_category_details,name,'.$id.',listing_category_id',
            'icon' => 'sometimes|max:100',
            'mobile_app_image' => 'nullable|mimes:jpg,jpeg,png'
        ];

        $message = [
            'name.*.required' => __('Category name field is required'),
            'icon.required' => __('Icon field is required'),
        ];

        $request->validate($rules, $message);

        DB::beginTransaction();
        try {
            $listingCategory = ListingCategory::findOrFail($id);

            $language = Language::select('id','default_status')->findOrFail($language_id);

            if ($language->default_status){
                if ($request->hasFile('mobile_app_image')) {
                    try {
                        $image = $this->fileUpload($request->mobile_app_image, config('filelocation.listing_category.path'), null, null, 'webp', 99,$listingCategory->mobile_app_image,$listingCategory->image_driver);
                        if ($image) {
                            $listingCategory->mobile_app_image = $image['path'];
                            $listingCategory->image_driver = $image['driver'];
                        }
                    } catch (\Exception $exp) {
                        return back()->with('error', __('Image could not be uploaded.'));
                    }
                }
                $listingCategory->icon = $request->icon;
                $listingCategory->status = $request->status;
                $listingCategory->save();
            }

            $listingCategory->details()->updateOrCreate(
                [
                    'language_id' => $language_id
                ],
                [
                    'listing_category_id' => $id,
                    'name' => $request["name"][$language_id],
                ]
            );
            DB::commit();
            return back()->with('success', __('Listing Category Updated Successfully'));
        }catch (\Exception $exception){
            DB::rollBack();
            return back()->with('error', __('Something went wrong'));
        }
    }


    public function listingCategoryDelete($id)
    {
        DB::beginTransaction();
        try {
            $listingCategory = ListingCategory::findOrFail($id);
            $listingCategory->details()->delete();
            $this->fileDelete($listingCategory->image_driver, $listingCategory->mobile_app_image);
            $listingCategory->delete();
            DB::commit();
            return back()->with('success', __('Listing category has been deleted'));
        }catch (\Exception $exception){
            DB::rollBack();
            return back()->with('error', __('Something went wrong'));
        }
    }

    public function listingCategoryDeleteMultiple(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select any Item.');
            return response()->json(['error' => 1]);
        } else {
            foreach ($request->strIds as $id) {
                $listingCategory = ListingCategory::with('details')->findOrFail($id);
                $listingCategory->details()->delete();
                $this->fileDelete($listingCategory->image_driver, $listingCategory->mobile_app_image);
                $listingCategory->delete();
            }
            session()->flash('success', 'Listing category has been Deleted');
            return response()->json(['success' => 1]);
        }
    }


}
