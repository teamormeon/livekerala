<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Models\Page;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CommonController extends Controller
{
    public function __construct()
    {
        $this->theme = template();
    }

    public function getFilePath(Request $request)
    {
        $driver = $request->driver;
        $path = $request->path;
        $fileUrl = getFile($driver, $path);
        return response()->json(['fileUrl' => $fileUrl]);
    }

    public function contactSend(Request $request)
    {
        $rules = [
            'name' => 'required|max:50',
            'email' => 'required|email|max:91',
            'subject' => 'required|max:100',
            'message' => 'required|max:1000',
        ];
        $request->validate($rules);

        $name = $request['name'];
        $email_from = $request['email'];
        $subject = $request['subject'];
        $message = $request['message'] . "<br>Regards<br>" . $name;
        $from = $email_from;

        Mail::to(basicControl()->sender_email)->send(new SendMail($from, $subject, $message));
        return back()->with('success', __('Mail has been sent'));
    }

    public function cookiePolicy()
    {
        $pageSeo = Page::where('slug', 'cookie-policy')->first();
        $pageSeo['breadcrumb_image'] = $pageSeo?->breadcrumb_status == 1 ?  getFile($pageSeo->breadcrumb_image_driver, $pageSeo->breadcrumb_image) : null;
        return view(template().'.cookie_policy', compact('pageSeo'));
    }

    public function subscribe(Request $request)
    {
        $request->validate(['email' => 'required|email|max:255|unique:subscribers']);
        $data = new Subscriber();
        $data->email = $request->email;
        $data->save();
        return redirect()->back()->with('success', __('Subscribed Successfully'));
    }
}
