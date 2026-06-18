<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ClaimBusiness;
use App\Models\ClaimBusinessChating;
use App\Models\Listing;
use App\Traits\Notify;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClaimBusinessController extends Controller
{
    use Notify;

    public function claimBusiness(Request $request, $id)
    {
        $rules = [
            'claim_name' => 'required|max:50',
            'claim_message' => 'required',
        ];
        $message = [
            'claim_name.required' => __('Please write your name'),
            'claim_message.required' => __('Please Write your message'),
        ];
       $request->validate($rules, $message);

        $listing = Listing::findOrFail($id);

        if ($listing->user_id == auth()->id()) {
            return back()->with('warning', __('You can not claim your own business!'));
        } else {
            DB::beginTransaction();
            try {
                $claim = new ClaimBusiness();
                $claim->claim_by_id = auth()->id();
                $claim->listing_id = $id;
                $claim->listing_owner_id = $listing->user_id;
                $claim->uuid = strRandom();
                $claim->save();

                $claimChat = new ClaimBusinessChating();
                $claimChat->claim_business_id = $claim->id;
                $claimChat->listing_id = $id;
                $claimChat->message = $request->claim_message;
                $claimChat->userable()->associate(auth()->user());
                $claimChat->save();

                $senderName = auth()->user()->firstname . ' ' . auth()->user()->lastname;
                $msg = [
                    'listing' => $listing->title ?? null,
                    'from' => $senderName ?? null,
                ];

                $action = [
                    "link" => route('user.claim.business.list','customer-claim'),
                    "icon" => "fa fa-money-bill-alt text-white"
                ];
                $action2 = [
                    "link" => route('admin.claim.business'),
                    "icon" => "fa fa-money-bill-alt text-white"
                ];

                $this->userPushNotification($listing->get_user, 'LISTING_CLAIM', $msg, $action);
                $this->adminPushNotification('LISTING_CLAIM', $msg, $action2);
                DB::commit();
                return back()->with('success', __('Listing Claimed successfully!'));
            }catch (\Exception $exception){
                DB::rollBack();
                return back()->with('warning', __('Something went wrong!'));
            }
        }
    }


    public function myClaimBusinessList(Request $request, $type = null)
    {

        $types = ['customer-claim', 'my-claim'];
        abort_if(!in_array($type, $types), 404);

        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $data['type'] = $type;
        $claimBusiness = ClaimBusiness::with(['get_listing', 'get_listing_owner','get_client'])
            ->when(isset($search['name']), function ($query) use ($search) {
                return $query->whereHas('get_listing', function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search['name']}%");
                })->orWhereHas('get_listing_owner', function ($q2) use ($search) {
                    $q2->where('username', 'LIKE', "%{$search['name']}%")
                        ->orWhere('email', 'LIKE', "%{$search['name']}%")
                        ->orWhere('phone', 'LIKE', "%{$search['name']}%");
                })->orWhereHas('get_client', function ($q3) use ($search) {
                    $q3->where('username', 'LIKE', "%{$search['name']}%")
                        ->orWhere('email', 'LIKE', "%{$search['name']}%")
                        ->orWhere('phone', 'LIKE', "%{$search['name']}%");
                });
            })
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate,$toDate) {
                return $q2->whereBetween('created_at', [$fromDate,$toDate]);
            })
            ->when($type == 'customer-claim', function ($query) {
                return $query->where('listing_owner_id', auth()->id())->where('claim_by_id', '!=', auth()->id());
            })
            ->when($type == 'my-claim', function ($query) {
                return $query->where('claim_by_id', auth()->id())->where('listing_owner_id', '!=', auth()->id());
            })
            ->latest()
            ->paginate(basicControl()->paginate);
        return view('user_panel.user.claim_business.list', $data, compact('claimBusiness'));
    }

    public function myClaimBusinessConversation($uuid)
    {
        $claimRequest = ClaimBusiness::where('uuid', $uuid)
            ->with(['claimBusinessChat', 'get_listing', 'get_client', 'get_listing_owner'])
            ->firstOrFail();

        if (Auth::check() && $claimRequest->listing_owner_id == Auth::id()) {
            $data['isAuthor'] = true;
        } else {
            $data['isAuthor'] = false;
        }

        $data['persons'] = ClaimBusinessChating::where([
            'listing_id' => $claimRequest->listing_id,
            'claim_business_id' => $claimRequest->id
        ])
            ->with('userable')
            ->get()->unique('userable')->pluck('userable');

        return view('user_panel.user.claim_business.claim_reply', $data, compact('claimRequest'));
    }
}
