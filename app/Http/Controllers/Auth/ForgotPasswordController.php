<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Models\Page;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function showLinkRequestForm()
    {
        $pageSeo = Page::where('template_name',getTheme())->where('slug', 'reset-password')->first();
        $pageSeo['breadcrumb_image'] = $pageSeo?->breadcrumb_status == 1 ?  getFile($pageSeo->breadcrumb_image_driver, $pageSeo->breadcrumb_image) : null;
        return view(template().'auth.passwords.email', compact('pageSeo'));
    }

    public function submitForgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        try {
            $token = Str::random(64);
            $userEmail = $request->email;
            $user = User::where('email', $userEmail)->firstOrFail();
            $passwordResetTable = config('auth.passwords.users.table', 'password_reset_tokens');

            DB::table($passwordResetTable)->updateOrInsert(
                ['email' => $userEmail],
                [
                    'token' => $token,
                    'created_at' => Carbon::now(),
                ]
            );

            $resetLink = url('password/reset', $token) . '?email=' . urlencode($userEmail);
            $basic = basicControl();
            $subject = 'Password Reset';
            $message = str_replace(
                ['[[name]]', '[[message]]'],
                [
                    $user->username,
                    '<a href="' . $resetLink . '" target="_blank">Click To Reset Password</a>'
                ],
                $basic->email_description
            );

            Mail::to($user)->send(new SendMail($basic->sender_email, $subject, $message));

            return back()->with('success', 'We have e-mailed your password reset link!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

}
