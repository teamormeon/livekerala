<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class SubscriberController extends Controller
{
    public function subscriber()
    {
        return view('admin.subscriber.list');
    }

    public function subscriberSerach(Request $request)
    {
        $search = $request->search['value']??null;

        $subscribers = Subscriber::query()->orderBy('id', 'DESC')
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('email', 'like', '%' . $search . '%');
            });

        return DataTables::of($subscribers)
            ->addColumn('checkbox', function ($item) {
                return ' <input type="checkbox" id="chk-' . $item->id . '"
                                       class="form-check-input row-tic tic-check" name="check" value="' . $item->id . '"
                                       data-id="' . $item->id . '">';
            })
            ->addColumn('email', function ($item) {
                return $item->email;
            })
            ->addColumn('joined', function ($item) {
                return dateTime($item->created_at, 'd M Y h:i A');
            })
            ->rawColumns(['checkbox', 'email', 'joined'])
            ->make(true);
    }

    public function subscriberDeleteMultiple(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select User.');
            return response()->json(['error' => 1]);
        } else {
            Subscriber::whereIn('id', $request->strIds)->get()->map(function ($item) {
                $item->Delete();
            });
            session()->flash('success', 'Subscriber has been deleted successfully');
            return response()->json(['success' => 1]);
        }
    }


    public function subscriberSendEmailForm()
    {
        return view('admin.subscriber.send_email_form');
    }

    public function subscriberSendEmail(Request $request)
    {
        $rules = [
            'subject' => 'required',
            'message' => 'required',
        ];
        $request->validate($rules);
        $basic = basicControl();
        $email_from = $basic->sender_email;
        $requestMessage = $request->message;
        $subject = $request->subject;
        $email_body = $basic->email_description;
        if (!Subscriber::first()) return back()->withInput()->with('error', 'No subscribers to send email.');
        $subscribers = Subscriber::all();
        foreach ($subscribers as $subscriber) {
            $name = explode('@', $subscriber->email)[0];
            $message = str_replace("[[name]]", $name, $email_body);
            $message = str_replace("[[message]]", $requestMessage, $message);
            @Mail::to($subscriber->email)->queue(new SendMail($email_from, $subject, $message));
        }
        return back()->with('success', 'Email has been sent to subscribers.');
    }
}
