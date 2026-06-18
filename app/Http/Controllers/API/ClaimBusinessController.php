<?php

namespace App\Http\Controllers\API;

use App\Events\OfferChatNotification;
use App\Events\UserNotification;
use App\Http\Controllers\Controller;
use App\Models\ClaimBusiness;
use App\Models\ClaimBusinessChating;
use App\Models\Listing;
use App\Traits\ApiResponse;
use App\Traits\Notify;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClaimBusinessController extends Controller
{
    use ApiResponse, Notify;

    public function claimBusiness(Request $request, $listing_id)
    {
        $rules = [
            'claimer_name' => 'required|max:50',
            'claim_message' => 'required',
        ];
        $message = [
            'claimer_name.required' => __('Please write your name'),
            'claim_message.required' => __('Please Write your message'),
        ];

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return response()->json($this->withError(collect($validator->errors())->collapse()));
        }

        $user = auth()->user();
        $listing = Listing::select('id','user_id','title')->find($listing_id);

        if (!$listing){
            return response()->json($this->withError('Listing not found'));
        }

        if ($listing->user_id == $user->id) {
            return response()->json($this->withError('You can not claim your own business!'));
        } else {
            DB::beginTransaction();
            try {
                $claim = new ClaimBusiness();
                $claim->claim_by_id = $user->id;
                $claim->listing_id = $listing_id;
                $claim->listing_owner_id = $listing->user_id;
                $claim->uuid = strRandom();
                $claim->save();

                $claimChat = new ClaimBusinessChating();
                $claimChat->claim_business_id = $claim->id;
                $claimChat->listing_id = $listing_id;
                $claimChat->message = $request->claim_message;
                $claimChat->userable()->associate(auth()->user());
                $claimChat->save();

                $senderName = $user->firstname . ' ' . $user->lastname;
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
                return response()->json($this->withSuccess('Claimed successfully'));
            }catch (\Exception $exception){
                DB::rollBack();
                return response()->json($this->withError('Something went wrong'));
            }
        }
    }

    public function myClaimBusinessList(Request $request, $type = null)
    {
        $types = ['customer-claim', 'my-claim'];
        if (!in_array($type, $types)){
            return response()->json($this->withError('Invalid type! use customer-claim or my-claim only'));
        }

        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $claimBusiness = ClaimBusiness::whereHas('get_listing_owner')
            ->whereHas('get_client')
            ->whereHas('get_listing_owner')
            ->with(['get_listing:id,user_id,title,slug',
            'get_listing_owner:id,firstname,lastname,username,email,image,image_driver',
            'get_client:id,firstname,lastname,username,email,image,image_driver'])
            ->select('id','claim_by_id','listing_id','listing_owner_id','uuid','is_chat_start','status','created_at')
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

        $formatedClaimBusiness = $claimBusiness->getCollection()->map(function ($item){
            return [
                'id' => $item->id,
                'claim_by_id' => $item->claim_by_id,
                'listing_id' => $item->listing_id,
                'listing_owner_id' => $item->listing_owner_id,
                'uuid' => $item->uuid,
                'is_chat_start' => $item->is_chat_start,
                'status' => $item->status,
                'created_at' => $item->created_at,
                'listing' => [
                    'title' => $item->get_listing->title,
                    'slug' => $item->get_listing->slug,
                ],
                'listing_owner' => [
                    'firstname' => $item->get_listing_owner->firstname,
                    'lastname' => $item->get_listing_owner->lastname,
                    'username' => $item->get_listing_owner->username,
                    'email' => $item->get_listing_owner->email,
                    'image' => $item->get_listing_owner->imgPath,
                ],
                'listing_claimer' => [
                    'firstname' => $item->get_client->firstname,
                    'lastname' => $item->get_client->lastname,
                    'username' => $item->get_client->username,
                    'email' => $item->get_client->email,
                    'image' => $item->get_client->imgPath,
                ],
            ];
        });

        $claimBusiness->setCollection($formatedClaimBusiness);

        $info = [
            'is_chat_start' => '0 = Inactive, 1 = Active',
            'status' => '0 = Pending, 1 = Approved, 2 = Rejected',
            'Action button condition' => 'if is_chat_start = 0 then cannot see or reply conversation',
        ];
        return response()->json($this->withSuccess($claimBusiness, $info));
    }

    public function myClaimBusinessConversation($uuid)
    {
        $auth = auth()->user();
        $claimRequest = ClaimBusiness::with(['claimBusinessChat:id,claim_business_id,userable_type,userable_id,message,description,created_at',
            'get_listing:id,title,slug,thumbnail,thumbnail_driver,status,is_active',
            'get_client:id,firstname,lastname,username,email,website,image,image_driver,address_one,address_two',
            'get_listing_owner:id,firstname,lastname,username,email,website,image,image_driver,address_one,address_two'])->where('uuid', $uuid)
            ->select('id','claim_by_id','listing_id','listing_owner_id','uuid','is_chat_enable','is_chat_start','status','created_at')
            ->first();

        $isAuthor = false;
        if (Auth::check() && $claimRequest->listing_owner_id == $auth->id) {
            $isAuthor = true;
        }
        $persons = ClaimBusinessChating::where([
            'listing_id' => $claimRequest->listing_id,
            'claim_business_id' => $claimRequest->id
        ])->with('userable')->get()->unique('userable')->pluck('userable')->select('id','username','imgPath');

        $formatedClaimRequest =
             [
                'id' => $claimRequest->id,
                'claim_by_id' => $claimRequest->claim_by_id,
                'listing_id' => $claimRequest->listing_id,
                'listing_owner_id' => $claimRequest->listing_owner_id,
                'uuid' => $claimRequest->uuid,
                'is_chat_enable' => $claimRequest->is_chat_enable,
                'is_chat_start' => $claimRequest->is_chat_start,
                'status' => $claimRequest->status,
                'isListingAuthor' => $isAuthor,
                'created_at' => $claimRequest->created_at,
                'conversation_person' => $persons,
                'listing' => [
                    'id' => $claimRequest->get_listing->id,
                    'title' => $claimRequest->get_listing->title,
                    'slug' => $claimRequest->get_listing->slug,
                    'thumbnail' => getFile($claimRequest->get_listing->thumbnail_driver, $claimRequest->get_listing->thumbnail),
                    'status' => $claimRequest->get_listing->status,
                    'is_active' => $claimRequest->get_listing->is_active,
                ],
                'listing_owner' => [
                    'id' => $claimRequest->get_listing_owner->id,
                    'firstname' => $claimRequest->get_listing_owner->firstname,
                    'lastname' => $claimRequest->get_listing_owner->lastname,
                    'username' => $claimRequest->get_listing_owner->username,
                    'email' => $claimRequest->get_listing_owner->email,
                    'website' => $claimRequest->get_listing_owner->website,
                    'image' => $claimRequest->get_listing_owner->imgPath,
                    'fullAddress' => $claimRequest->get_listing_owner->fullAddress,
                ],
                'listing_client' => [
                    'id' => $claimRequest->get_client->id,
                    'firstname' => $claimRequest->get_client->firstname,
                    'lastname' => $claimRequest->get_client->lastname,
                    'username' => $claimRequest->get_client->username,
                    'email' => $claimRequest->get_client->email,
                    'website' => $claimRequest->get_client->website,
                    'image' => $claimRequest->get_client->imgPath,
                    'fullAddress' => $claimRequest->get_client->fullAddress,
                ],
                'messages' => $claimRequest->claimBusinessChat->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'userable_type' => $item->userable_type,
                        'userable_id' => $item->userable_id,
                        'title' => $item->message,
                        'description' => $item->description,
                        'created_at' => $item->created_at,
                    ];
                })
            ];

        $info = [
            'uuid' => 'pass uuid when add reply',
            'isListingAuthor' => 'if isListingAuthor = true then show the listing_client information into conversation page otherwize show listing_owner information',
            'status' => '0 = Pending, 1 = Approved, 2 = Rejected',
            'is_chat_enable' => 'if is_chat_enable = 0 then hide the chat typing input and submit button otherwise show those',
            'is_chat_start' => 'if is_chat_start = 0 then cannot see or reply conversation',
        ];
        return response()->json($this->withSuccess($formatedClaimRequest, $info));
    }

    public function newMessage(Request $request)
    {
        $rules = [
            'listing_id' => ['required'],
            'claim_business_id' => ['required'],
            'message' => ['required']
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($this->withError(collect($validator->errors())->collapse()));
        }

        $user = Auth::user();
        $claimBusiness = ClaimBusiness::where('id', $request->claim_business_id)
            ->where('listing_id', $request->listing_id)
            ->first();

        if (!$claimBusiness) {
            return response()->json($this->withError("Claim business not found"));
        }

        $chat = new ClaimBusinessChating();
        $chat->description = $request->message;
        $chat->claim_business_id = $claimBusiness->id;
        $chat->listing_id = $claimBusiness->listing_id;
        $chat->userable()->associate($user);
        $chat->save();
        $log = $chat;

        $data['id'] = $log->id;
        $data['userable_id'] = $log->userable_id;
        $data['userable_type'] = $log->userable_type;
        $data['userable'] = [
            'id' => $log->userable->id,
            'fullname' => $log->userable->fullname,
            'username' => $log->userable->username,
            'imgPath' => $log->userable->imgPath,
        ];
        $data['description'] = $log->description;
        $data['is_read'] = $log->is_read;
        $data['is_read_admin'] = $log->is_read_admin;
        $data['formatted_date'] = $log->formatted_date;
        $data['created_at'] = $log->created_at;

        $this->sendRealTimeMessageThrowFirebase($claimBusiness->get_client, $data, $claimBusiness->uuid);
        $this->sendRealTimeMessageThrowFirebase($claimBusiness->get_listing_owner, $data, $claimBusiness->uuid);

        event(new OfferChatNotification($data, $claimBusiness->uuid));
        return response()->json($this->withSuccess('Message Send'));
    }

}
