<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\ClaimBusiness;
use App\Models\ClaimBusinessChating;
use App\Models\User;
use App\Traits\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatNotificationController extends Controller
{
    use Notify;

    public function show(Request $request, $uuid)
    {
        $claimRequest = ClaimBusiness::where('uuid', $uuid)
            ->firstOrFail();
        $siteNotifications = ClaimBusinessChating::whereHasMorph(
            'userable',
            [
                User::class,
                Admin::class,
            ],
            function ($query) use ($claimRequest) {
                $query->where([
                    'listing_id' => $claimRequest->listing_id,
                    'claim_business_id' => $claimRequest->id
                ]);
            }
        )->with('userable')->get();
        return $siteNotifications;
    }

    public function newMessage(Request $request)
    {
        $rules = [
            'listing_id' => ['required'],
            'claim_business_id' => ['required'],
            'message' => ['required']
        ];

        $request->validate($rules);
        $user = Auth::user();

        $claimBusiness = ClaimBusiness::where('id', $request->claim_business_id)
            ->where('listing_id', $request->listing_id)
            ->firstOrFail();

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

        event(new \App\Events\OfferChatNotification($data, $claimBusiness->uuid));
        return response(['success' => true], 200);
    }


    public function claimBusinessConversationShowByAdmin($uuid)
    {
        $claimRequest = ClaimBusiness::where('uuid', $uuid)
            ->firstOrFail();

        $siteNotifications = ClaimBusinessChating::whereHasMorph(
            'userable',
            [
                User::class,
                Admin::class
            ],
            function ($query) use ($claimRequest) {
                $query->where([
                    'listing_id' => $claimRequest->listing_id,
                    'claim_business_id' => $claimRequest->id
                ]);
            }
        )->with('userable:id,username,image,image_driver')->get();

        return $siteNotifications;
    }


    public function claimBusinessConversationNewMessageByAdmin(Request $request)
    {
        $rules = [
            'listing_id' => ['required'],
            'claim_business_id' => ['required'],
            'message' => ['required']
        ];

        $req = $request->all();
        $validator = Validator::make($req, $rules);
        if ($validator->fails()) {
            return response(['errors' => $validator->messages()], 200);
        }

        $user = auth::guard('admin')->user();
        $claimBusiness = ClaimBusiness::where('id', $request->claim_business_id)
            ->where('listing_id', $request->listing_id)
            ->firstOrFail();

        $chat = new ClaimBusinessChating();
        $chat->description = $req['message'];
        $chat->listing_id = $claimBusiness->listing_id;
        $chat->claim_business_id = $claimBusiness->id;
        $chat->userable()->associate($user);
        $chat->save();
        $log = $chat;


        $uuid = $claimBusiness->uuid;
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
        event(new \App\Events\OfferChatNotification($data, $uuid));

        return response(['success' => true], 200);
    }

}
