<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class ContactMessageController extends Controller
{
    public function contactMessage()
    {
        return view('admin.listingContactMessages.list');
    }

    public function contactMessageSearch(Request $request)
    {
        $search = $request->search['value'] ?? null;
        $filterName = $request->filterName;
        $filterDate = explode('-', $request->filterDate);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;

        $allMessages = ContactMessage::query()->with('get_client', 'get_user')->orderBy('id', 'DESC')
            ->when(!empty($search), function ($query) use ($search) {
                return $query->whereHas('get_client', function ($qq) use ($search) {
                    $qq->where('email', 'LIKE', "%{$search}%")
                        ->orWhere('username', 'LIKE', "%{$search}%")
                        ->orWhere('firstname', 'LIKE', "%{$search}%")
                        ->orWhere('lastname', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                })->orWhereHas('get_user', function ($qq) use ($search) {
                    $qq->where('email', 'LIKE', "%{$search}%")
                        ->orWhere('username', 'LIKE', "%{$search}%")
                        ->orWhere('firstname', 'LIKE', "%{$search}%")
                        ->orWhere('lastname', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            })
            ->when(isset($filterName) && !empty($filterName), function ($query) use ($filterName) {
                return $query->whereHas('get_client', function ($qq) use ($filterName) {
                    $qq->where('username', 'LIKE', "%{$filterName}%")
                        ->orWhere('firstname', 'LIKE', "%{$filterName}%")
                        ->orWhere('lastname', 'LIKE', "%{$filterName}%");
                })->orWhereHas('get_user', function ($qq) use ($filterName) {
                    $qq->where('username', 'LIKE', "%{$filterName}%")
                        ->orWhere('firstname', 'LIKE', "%{$filterName}%")
                        ->orWhere('lastname', 'LIKE', "%{$filterName}%");
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

        return DataTables::of($allMessages)
            ->addColumn('checkbox', function ($item) {
                return ' <input type="checkbox" id="chk-' . $item->id . '"
                                       class="form-check-input row-tic tic-check" name="check" value="' . $item->id . '"
                                       data-id="' . $item->id . '">';
            })
            ->addColumn('from', function ($item) {
                $url = route('admin.user.view.profile', optional($item->get_client)->id);
                return '<a class="d-flex align-items-center me-2" href="' . $url . '">
                                <div class="flex-shrink-0">
                                  ' . $item->get_client->profilePicture() . '
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <h5 class="text-hover-primary mb-0">' . optional($item->get_client)->firstname . ' ' . optional($item->get_client)->lastname . '</h5>
                                  <span class="fs-6 text-body">@' . optional($item->get_client)->username . '</span>
                                </div>
                              </a>';

            })
            ->addColumn('to', function ($item) {
                $url = route('admin.user.view.profile', optional($item->get_user)->id);
                return '<a class="d-flex align-items-center me-2" href="' . $url . '">
                                <div class="flex-shrink-0">
                                  ' . $item->get_user->profilePicture() . '
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <h5 class="text-hover-primary mb-0">' . optional($item->get_user)->firstname . ' ' . optional($item->get_user)->lastname . '</h5>
                                  <span class="fs-6 text-body">@' . optional($item->get_user)->username . '</span>
                                </div>
                              </a>';

            })
            ->addColumn('message', function ($item) {
                return '<div class="text-wrap" style="width: 18rem;">
                    <p>' . trans(\Illuminate\Support\Str::limit($item->message)) . '</p>
                  </div>';
            })
            ->addColumn('date-time', function ($item) {
                return dateTime($item->created_at);
            })
            ->addColumn('action', function ($item) {
                return '<div class="btn-group" role="group">
                      <a href="javascript:void(0)" class="btn btn-white btn-sm showMessage"
                          data-bs-toggle="modal" data-bs-target="#messageViewModalModal"
                          data-from="' . optional($item->get_client)->firstname . ' ' . optional($item->get_client)->lastname . '"
                          data-to="' . optional($item->get_user)->firstname . ' ' . optional($item->get_user)->lastname . '"
                          data-message="' . $item->message . '"
                          data-time="' . dateTime($item->created_at) . '">
                          <i class="bi-eye me-1"></i> ' . trans("View") . '
                      </a>
                  </div>';
            })->rawColumns(['checkbox', 'from', 'to', 'message', 'date-time', 'action'])
            ->make(true);
    }

    public function contactMessageDeleteMultiple(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select User.');
            return response()->json(['error' => 1]);
        } else {
            ContactMessage::whereIn('id', $request->strIds)->get()->map(function ($message) {
                $message->Delete();
            });
            session()->flash('success', 'Message has been deleted successfully');
            return response()->json(['success' => 1]);
        }
    }
}
