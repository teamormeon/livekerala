<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NotificationPermission;
use App\Models\NotificationTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationPermissionController extends Controller
{
    public function notificationPermission()
    {
        try {
            $user = User::with('notifypermission')->findOrFail(Auth::id());
            $allTemplates = NotificationTemplate::where('notify_for', 0)
                ->where(function ($query) {
                    $query->where('template_key', '!=', 'SUPPORT_TICKET_CREATE')
                        ->where('template_key', '!=', 'DEDUCTED_BALANCE')
                        ->where('template_key', '!=', 'ADD_FUND_USER_USER');
                })
                ->get();
            return view('user_panel.user.notification_permission', compact('user','allTemplates'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function notificationPermissionUpdate(Request $request)
    {
        try {
            $user = Auth::user();
            $userTemplate = NotificationPermission::firstOrNew(
                ['notifyable_id' => $user->id, 'notifyable_type' => User::class]
            );
            $userTemplate->template_email_key = $request->email_key;
            $userTemplate->template_sms_key = $request->sms_key;
            $userTemplate->template_push_key = $request->push_key;
            $userTemplate->template_in_app_key = $request->in_app_key;
            $userTemplate->save();
            return back()->with('success', 'Notification Permission Updated Successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
