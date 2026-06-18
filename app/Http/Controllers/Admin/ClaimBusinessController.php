<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClaimBusiness;
use App\Models\ClaimBusinessChating;
use App\Traits\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ClaimBusinessController extends Controller
{
    use Notify;

    public function claimBusiness()
    {
        return view('admin.businessClaim.list');
    }

    public function claimBusinessSearch(Request $request)
    {
        $search = $request->search['value']??null;
        $filterName = $request->filterName;
        $filterDate = explode('-', $request->filterDate);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;

        $allClaims = ClaimBusiness::query()->with(['get_client', 'get_listing.get_user'])->orderBy('id', 'DESC')
            ->when(!empty($search), function ($query) use ($search) {
                return $query->whereHas('get_client', function ($qq) use ($search) {
                    $qq->where('email', 'LIKE', "%{$search}%")
                        ->orWhere('username', 'LIKE', "%{$search}%")
                        ->orWhere('firstname', 'LIKE', "%{$search}%")
                        ->orWhere('lastname', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                })->orWhereHas('get_listing', function ($qq) use ($search) {
                    $qq->where('title', 'LIKE', "%{$search}%");
                });
            })
            ->when(isset($filterName) && !empty($filterName), function ($query) use ($filterName) {
                return $query->whereHas('get_client', function ($qq) use ($filterName) {
                    $qq->where('username', 'LIKE', "%{$filterName}%")
                        ->orWhere('firstname', 'LIKE', "%{$filterName}%")
                        ->orWhere('lastname', 'LIKE', "%{$filterName}%");
                })->orWhereHas('get_listing', function ($qq) use ($filterName) {
                    $qq->where('title', 'LIKE', "%{$filterName}%");
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

        return DataTables::of($allClaims)
            ->addColumn('checkbox', function ($item) {
                return ' <input type="checkbox" id="chk-' . $item->id . '"
                                       class="form-check-input row-tic tic-check" name="check" value="' . $item->id . '"
                                       data-id="' . $item->id . '">';
            })
            ->addColumn('listing-title', function ($item) {
                return '<span class="d-block h5 mb-0">' . trans(\Illuminate\Support\Str::limit(optional($item->get_listing)->title, 40)) . '</span>';
            })
            ->addColumn('owner', function ($item) {
                $url = route('admin.user.view.profile', optional(optional($item->get_listing)->get_user)->id);
                return '<a class="d-flex align-items-center me-2" href="' . $url . '">
                                <div class="flex-shrink-0">
                                  ' . optional($item->get_listing)->get_user->profilePicture() . '
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <h5 class="text-hover-primary mb-0">' . optional(optional($item->get_listing)->get_user)->firstname . ' ' . optional(optional($item->get_listing)->get_user)->lastname . '</h5>
                                  <span class="fs-6 text-body">@' . optional(optional($item->get_listing)->get_user)->email . '</span>
                                </div>
                              </a>';

            })
            ->addColumn('claim', function ($item) {
                $url = route('admin.user.view.profile', optional($item->get_client)->id);
                return '<a class="d-flex align-items-center me-2" href="' . $url . '">
                                <div class="flex-shrink-0">
                                  ' . optional($item->get_client)->profilePicture() . '
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <h5 class="text-hover-primary mb-0">' . optional($item->get_client)->firstname . ' ' . optional($item->get_client)->lastname . '</h5>
                                  <span class="fs-6 text-body">@' . optional($item->get_client)->email . '</span>
                                </div>
                              </a>';

            })
            ->addColumn('status', function ($item) {
                $badgeClass =  $item->status == 1 ? 'success text-success' : ($item->status == 2 ? 'danger text-danger' : 'warning text-warning');
                $legendBgClass =  $item->status == 1 ? 'success' : ($item->status == 2 ? 'danger' : 'warning');
                $status = $item->status == 1 ? 'Approved' : ($item->status == 2 ? 'Rejected' : 'Pending');
                return '<span class="badge bg-soft-'.$badgeClass.'"><span class="legend-indicator bg-'.$legendBgClass.'"></span>'.$status.'</span>';
            })
            ->addColumn('date-time', function ($item) {
                return dateTime($item->created_at);
            })
            ->addColumn('action', function ($item) {
                $url = route('admin.claim.business.conversation',$item->uuid);
                $startChatUrl = route('admin.claim.business.start.chat',$item->uuid);
                $enableChatUrl = route('admin.claim.business.enable.chat.status',$item->uuid);
                $approveClaimUrl = route('admin.claim.business.approve',$item->id);
                $rejectClaimUrl = route('admin.claim.business.reject',$item->id);
                $startChatBtnText = $item->is_chat_start == 0 ? 'Start Chat' : 'Started Chat';
                $enableBtnText = $item->is_chat_enable == 0 ? 'Enable Chat' : 'Disable Chat';

                $canEdit = adminAccessRoute(config('role.claim_business.access.edit'));
                $canViewConversation = adminAccessRoute(config('role.claim_business_conversation.access.view'));
                $actions = '';

                if ($canViewConversation && $item->is_chat_start == 1){
                    $actions .= '<div class="btn-group" role="group">
                            <a class="btn btn-white btn-sm" href="'.$url.'">
                                <i class="bi bi-chat-dots me-1"></i>'.trans("Conversation").'
                            </a>';
                }

                if ($canEdit){
                    $actions .= '<div class="btn-group">
                                <button type="button" class="btn btn-white btn-icon btn-sm dropdown-toggle dropdown-toggle-empty" id="productsEditDropdown1" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <div class="dropdown-menu dropdown-menu-end mt-1" aria-labelledby="productsEditDropdown1" style="">
                                     '.($item->is_chat_start == 0 ? '<a class="dropdown-item text-secondary chatStartBtn" href="javascript:void(0)"
                                            data-url="'.$startChatUrl.'" data-bs-target="#startChatModal" data-bs-toggle="modal">
                                            <i class="bi-check2-square dropdown-item-icon text-secondary"></i> '.$startChatBtnText.'
                                        </a>' : '').'
                                    <a class="dropdown-item chatEnableBtn" href="javascript:void(0)" data-is_chat_enable="'.$item->is_chat_enable.'"
                                    data-url="'.$enableChatUrl.'" data-bs-target="#enableChatStatus" data-bs-toggle="modal">
                                        <i class="bi-check2-square dropdown-item-icon"></i> '.$enableBtnText.'
                                    </a>
                                    '.($item->status != 1 && $item->status != 2 ? '<a class="dropdown-item approveClaimBtn" href="javascript:void(0)" data-bs-target="#approveClaimBusiness" data-bs-toggle="modal" data-route="'.$approveClaimUrl.'">
                                        <i class="bi-check2-square dropdown-item-icon"></i> '.trans("Approve").'
                                    </a>' : '' ).'
                                    '.($item->status != 1 && $item->status != 2 ? '<a class="dropdown-item deleteBtn rejectClaimBtn" href="javascript:void(0)" data-bs-target="#rejectClaimBusiness" data-bs-toggle="modal" data-route="'.$rejectClaimUrl.'">
                                        <i class="bi-x-square dropdown-item-icon"></i> '.trans("Reject").'
                                    </a>' : '').'
                                </div>
                            </div>
                        </div>';
                }

                return $actions ?: '-';
            })->rawColumns(['checkbox', 'listing-title', 'claim', 'owner', 'status', 'date-time', 'action'])
            ->make(true);
    }

    public function claimBusinessStartChatOption(Request $request, $uuid)
    {
        $claimBusiness = ClaimBusiness::where('uuid', $uuid)
            ->firstOrFail();
        $claimBusiness->is_chat_start = 1;
        $claimBusiness->save();
        return redirect()->back()->with('success', trans("Chat Option started Successfully"));
    }

    public function claimBusinessEnableChatOption(Request $request, $uuid)
    {
        $claimBusiness = ClaimBusiness::where('uuid', $uuid)
            ->firstOrFail();
        $claimBusiness->is_chat_enable = !$claimBusiness->is_chat_enable;
        $claimBusiness->save();
        return redirect()->back()->with('success', trans("Chat Option updated Successfully"));
    }

    public function claimBusinessConversation($uuid)
    {
        $claimBusiness = ClaimBusiness::where('uuid', $uuid)
            ->firstOrFail();

        $data['persons'] = ClaimBusinessChating::where([
            'listing_id' => $claimBusiness->listing_id,
            'claim_business_id' => $claimBusiness->id
        ])
            ->with('userable')
            ->get()->unique('userable')->pluck('userable');
        $data['claimBusiness'] = $claimBusiness;
        return view('admin.businessClaim.conversation', $data);
    }



    public function claimDeleteMultiple(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select any item.');
            return response()->json(['error' => 1]);
        } else {
            ClaimBusiness::whereIn('id', $request->strIds)->get()->map(function ($message) {
                $message->Delete();
            });
            session()->flash('success', 'Claim has been deleted successfully');
            return response()->json(['success' => 1]);
        }
    }


    public function claimBusinessChatStageChange(Request $request)
    {
        $claimBusiness = ClaimBusiness::find($request->claim_id);
        if ($claimBusiness){
            if ($request->type == 'enable'){
                $claimBusiness->is_chat_enable = 1;
                $claimBusiness->save();
                event(new \App\Events\ChatStageChangeEvent('enable', $claimBusiness->uuid));
            }

            if ($request->type == 'disable'){
                $claimBusiness->is_chat_enable = 0;
                $claimBusiness->save();
                event(new \App\Events\ChatStageChangeEvent('disable', $claimBusiness->uuid));
            }
            return response()->json(['success' => 1]);
        }
    }

    public function claimBusinessApprove(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $claimBusiness = ClaimBusiness::findOrFail($id);
            $claimBusiness->status = 1;
            $claimBusiness->save();

            $listing = $claimBusiness->get_listing;
            $listing->is_active = 0;
            $listing->status = 2;
            $listing->rejected_reason = 'Rejected by Admin base on claim evidence';
            $listing->save();

            $admin = Auth::user();
            $claimer = $claimBusiness->get_client;
            $listingOwner = $claimBusiness->get_listing_owner;
            $msg1 = [
                'from' => $admin->name ?? null,
                'uuid' => $claimBusiness->uuid,
                'message' => 'approved your',
            ];
            $msg2 = [
                'from' => $admin->name ?? null,
                'uuid' => $claimBusiness->uuid,
                'message' => 'deactivated your '.$listing->title .' listing base on claim evidence. ',
            ];
            $action1 = [
                "link" => route('user.claim.business.list','my-claim'),
                "icon" => "fa fa-money-bill-alt text-white",
                'image' =>  getFile($admin->image_driver, $admin->image),
            ];
            $action2 = [
                "link" => route('user.claim.business.list','customer-claim'),
                "icon" => "fa fa-money-bill-alt text-white",
                'image' =>  getFile($admin->image_driver, $admin->image),
            ];

            $this->userPushNotification($claimer, 'CLAIM_BUSINESS_APPROVED_BY_ADMIN', $msg1, $action1);
            $this->userPushNotification($listingOwner, 'CLAIM_BUSINESS_APPROVED_BY_ADMIN', $msg2, $action2);
            DB::commit();
            return back()->with('success', "Claim has been approved successfully");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('An error occurred while approving the claim. Please try again.');
        }

    }

    public function claimBusinessReject(Request $request, $id)
    {
        $claimBusiness = ClaimBusiness::findOrFail($id);
        $claimBusiness->status = 2;
        $claimBusiness->rejected_reason = $request->rejected_reason;
        $claimBusiness->save();

        $admin = Auth::user();
        $msg = [
            'from' => $admin->name ?? null,
            'uuid' => $claimBusiness->uuid,
            'rejectReason' => $request->rejectReason,
        ];
        $action = [
            "link" => route('user.claim.business.list','my-claim'),
            "icon" => "fa fa-money-bill-alt text-white",
            'image' =>  getFile($admin->image_driver, $admin->image),
        ];
        $user = $claimBusiness->get_client;
        $this->userPushNotification($user, 'CLAIM_BUSINESS_REJECTED_BY_ADMIN', $msg, $action);
        return back()->with('success', "Claim has been rejected successfully");
    }
}
