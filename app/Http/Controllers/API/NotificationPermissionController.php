<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\NotificationPermission;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationPermissionController extends Controller
{
    use ApiResponse;

    public function notificationPermission()
    {
        try {
            $notifications = NotificationTemplate::where('notify_for', 0)
                ->where(function ($query) {
                    $query->where('template_key', '!=', 'SUPPORT_TICKET_CREATE')
                        ->where('template_key', '!=', 'DEDUCTED_BALANCE')
                        ->where('template_key', '!=', 'ADD_FUND_USER_USER')
                        ->where('template_key', '!=', 'PAYOUT_REQUEST_FROM');
                })
                ->toBase()->get();

            if (!$notifications){
                return response()->json($this->withError('Notification template not found'));
            }

            $formattedNotifications = $notifications->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'key' => $item->template_key,
                'status' => json_decode($item->status),
            ]);

            $user = auth()->user();
            $data['notifications'] = $formattedNotifications;
            $data['userHasPermission'] = $user->notifypermission ?? null;

            $info = [
                'status' => '0 = Inactive, 1 = Active',
            ];
            return response()->json($this->withSuccess($data, $info));
        } catch (\Exception $e) {
            return response()->json($this->withError($e->getMessage()));
        }
    }

    public function notificationPermissionUpdate(Request $request)
    {
        try {
            $user = Auth::user();
            $rules = [
                'email_key' => 'required',
                'sms_key' => 'required',
                'in_app_key' => 'required',
                'push_key' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json($this->withError(collect($validator->errors())->collapse()));
            }

            $userTemplate = NotificationPermission::firstOrNew(
                ['notifyable_id' => $user->id, 'notifyable_type' => User::class]
            );
            if (!$userTemplate) {
                return response()->json($this->withError('Record not found'));
            }

            $userTemplate->template_email_key = $request->email_key;
            $userTemplate->template_sms_key = $request->sms_key;
            $userTemplate->template_in_app_key = $request->in_app_key;
            $userTemplate->template_push_key = $request->push_key;
            $userTemplate->save();
            return response()->json($this->withSuccess('Notification Permission Updated Successfully.'));
        } catch (\Exception $e) {
            return response()->json($this->withError($e->getMessage()));
        }
    }

    public function pusherConfig()
    {
        try {
            $data['apiKey'] = env('PUSHER_APP_KEY');
            $data['cluster'] = env('PUSHER_APP_CLUSTER');
            $data['channel'] = 'user-notification.' . Auth::id();
            $data['event'] = 'UserNotification';
            $data['chattingChannel'] = 'offer-chat-notification.' . Auth::id();
            $data['chattingEvent'] = 'OfferChatNotification.WT6GU58XFEHX';
            $data['productQueryChannel'] = 'user.chat.2';
            $data['productQueryEvent'] = 'ChatEvent.client_id';

            return response()->json($this->withSuccess($data));
        } catch (\Exception $e) {
            return response()->json($this->withError($e->getMessage()));
        }
    }
}
