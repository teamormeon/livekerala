<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Favourite;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class FavouriteController extends Controller
{
    public function wishList()
    {
        return view('admin.wishlist.index');
    }

    public function wishListSearch(Request $request)
    {
        $search = $request->search['value']??null;
        $filterSearch = $request->filterSearch;
        $filterDate = explode('-', $request->filterDate);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;

        $favourites = Favourite::query()->with('get_listing', 'get_listing.get_user', 'get_user')->orderBy('id', 'DESC')
            ->when(!empty($search), function ($query) use ($search) {
                return $query->whereHas('get_listing', function ($qq) use ($search) {
                    $qq->where('title', 'LIKE', "%{$search}%");
                })->orWhereHas('get_listing.get_user', function ($query) use ($search) {
                    $query->where('firstname', 'LIKE', "%{$search}%")
                    ->orWhere('lastname', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
                })->orWhereHas('get_user', function ($query) use ($search) {
                    $query->where('firstname', 'LIKE', "%{$search}%")
                        ->orWhere('lastname', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                });
            })
            ->when(isset($filterSearch) && !empty($filterSearch), function ($query) use ($filterSearch) {
                return $query->whereHas('get_listing', function ($qq) use ($filterSearch) {
                    $qq->where('title', 'LIKE', "%{$filterSearch}%");
                })->orWhereHas('get_listing.get_user', function ($query) use ($filterSearch) {
                    $query->where('firstname', 'LIKE', "%{$filterSearch}%")
                        ->orWhere('lastname', 'LIKE', "%{$filterSearch}%")
                        ->orWhere('email', 'LIKE', "%{$filterSearch}%");
                })->orWhereHas('get_user', function ($query) use ($filterSearch) {
                    $query->where('firstname', 'LIKE', "%{$filterSearch}%")
                        ->orWhere('lastname', 'LIKE', "%{$filterSearch}%")
                        ->orWhere('email', 'LIKE', "%{$filterSearch}%");
                });
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

        return DataTables::of($favourites)
            ->addColumn('checkbox', function ($item) {
                return ' <input type="checkbox" id="chk-' . $item->id . '"
                                       class="form-check-input row-tic tic-check" name="check" value="' . $item->id . '"
                                       data-id="' . $item->id . '">';
            })
            ->addColumn('listing', function ($item) {
                $listingUrl = route('listing.details', optional($item->get_listing)->slug);
                return '<a href="'.$listingUrl.'" target="_blank">
                            <span class="d-block h5 mb-0">' . trans(\Illuminate\Support\Str::limit($item->get_listing->title, 50)) . '</span>
                        </a>';
            })
            ->addColumn('category', function ($item) {
                return '<span class="d-block h5 mb-0">' . trans(optional($item->get_listing)->getCategoriesName()) . '</span>';
            })
            ->addColumn('owner', function ($item) {
                $url = route('admin.user.view.profile', optional(optional($item->get_listing)->get_user)->id);
                return '<a class="d-flex align-items-center me-2" href="' . $url . '">
                                <div class="flex-shrink-0">
                                  ' . optional(optional($item->get_listing)->get_user)->profilePicture() . '
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <h5 class="text-hover-primary mb-0">' . optional(optional($item->get_listing)->get_user)->firstname . ' ' . optional(optional($item->get_listing)->get_user)->lastname . '</h5>
                                  <span class="fs-6 text-body">' . optional(optional($item->get_listing)->get_user)->email . '</span>
                                </div>
                              </a>';

            })
            ->addColumn('wishList-added', function ($item) {
                $url = route('admin.user.view.profile', optional($item->get_user)->id);
                return '<a class="d-flex align-items-center me-2" href="' . $url . '">
                                <div class="flex-shrink-0">
                                  ' . $item->get_user->profilePicture() . '
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <h5 class="text-hover-primary mb-0">' . optional($item->get_user)->firstname . ' ' . optional($item->get_user)->lastname . '</h5>
                                  <span class="fs-6 text-body">@' . optional($item->get_user)->email . '</span>
                                </div>
                              </a>';

            })

            ->addColumn('added-at', function ($item) {
                return dateTime($item->created_at);
            })
            ->addColumn('action', function ($item) {
                $canDelete = adminAccessRoute(config('role.listing_wishlist.access.delete'));
                $actions = '';
                if ($canDelete) {
                    $actions .= '<div class="btn-group" role="group">
                      <a href="javascript:void(0)" class="btn btn-white btn-sm deleteBtn"
                          data-bs-toggle="modal" data-bs-target="#deleteModal"
                          data-route="'.route('admin.wishList.delete', $item->id).'">
                          <i class="bi bi-trash"></i> '.trans("Delete").'
                      </a>
                  </div>';
                }
                return $actions ?: '-';
            })->rawColumns(['checkbox', 'listing', 'category', 'owner', 'wishList-added', 'added-at', 'action'])
            ->make(true);
    }

    public function wishListDelete($id)
    {
        Favourite::findOrfail($id)->delete();
        return back()->with('success', __('Deleted Successful!'));
    }

    public function wishListDeleteMultiple(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select any Item.');
            return response()->json(['error' => 1]);
        } else {
            foreach ($request->strIds as $id) {
                Favourite::findOrfail($id)->delete();
            }
            session()->flash('success', 'WishList has been Deleted');
            return response()->json(['success' => 1]);
        }
    }
}
