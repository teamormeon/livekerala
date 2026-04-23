<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Listing;
use App\Models\User;
use App\Traits\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SendMessageController extends Controller
{
    use Notify;
    public function viewerSendMessageToUser(Request $request, $id)
    {
        $req = $request->except('_token', '_method');
        $rules = [
            'name' => 'required|max:50',
            'message' => 'required',
        ];
        $message = [
            'name.required' => __('Please write your name'),
            'message.required' => __('Please Write your message'),
        ];

        $validate = Validator::make($req, $rules, $message);

        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }

        $user = User::findOrFail($id);

        $senderName = Auth::user()->firstname . ' ' . Auth::user()->lastname;

        $contactMessage = new ContactMessage();
        $contactMessage->user_id = $id;
        $contactMessage->client_id = Auth::id();
        $contactMessage->message = $req['message'];
        $contactMessage->save();

        $msg = [
            'from' => $senderName ?? null,
        ];

        $userAction = [
            "link" => route('profile', $user->username),
            "icon" => "fa fa-money-bill-alt text-white"
        ];

        $adminAction = [
            "link" => route('admin.contact.message'),
            "icon" => "fa fa-money-bill-alt text-white"
        ];

        $this->userPushNotification($user, 'VIEWER_MESSAGE_TO_USER', $msg, $userAction);
        $this->sendMailSms($user, 'VIEWER_MESSAGE_TO_USER');
        $this->adminPushNotification( 'VIEWER_MESSAGE_TO_ADMIN', $msg, $adminAction);
        return back()->with('success', __('Message has been sent'));
    }


    public function sendListingMessage(Request $request, $id)
    {
        $req = $request->except('_token', '_method');
        $rules = [
            'name' => 'required|max:50',
            'message' => 'required',
        ];
        $message = [
            'name.required' => __('Please write your name'),
            'message.required' => __('Please Write your message'),
        ];

        $validate = Validator::make($req, $rules, $message);

        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }

        $listing = Listing::with('get_user')->findOrFail($id);
        $user = $listing->get_user;
        $senderName = Auth::user()->firstname . ' ' . Auth::user()->lastname;

        $contactMessage = new ContactMessage();
        $contactMessage->user_id = $user->id;
        $contactMessage->client_id = Auth::user()->id;
        $contactMessage->listing_id = $id;
        $contactMessage->message = $request->message;
        $contactMessage->save();

        $msg = [
            'from' => $senderName ?? null,
        ];

        $userAction = [
            "link" => route('profile', $user->username),
            "icon" => "fa fa-money-bill-alt text-white"
        ];

        $adminAction = [
            "link" => route('admin.contact.message'),
            "icon" => "fa fa-money-bill-alt text-white"
        ];

        $this->userPushNotification($user, 'VIEWER_MESSAGE_TO_USER', $msg, $userAction);

        $details = [
            'sub' => '[' . config('basic.site_title') . ']' . ' Contact Message sent from ' . $senderName,
            'replyToEmail' => Auth::user()->email,
            'replyToName' => $senderName,
            'message' => $request->message,
        ];

        Mail::to($user->email)->send(new \App\Mail\UserContact($details));

        $this->adminPushNotification( 'VIEWER_MESSAGE_TO_ADMIN', $msg, $adminAction);
        return back()->with('success', __('Message has been sent'));
    }


}
