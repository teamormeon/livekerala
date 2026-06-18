<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketAttachment;
use App\Models\SupportTicketMessage;
use App\Traits\ApiResponse;
use App\Traits\Notify;
use App\Traits\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SupportTicketController extends Controller
{
    use ApiResponse, Upload, Notify;


    public function index()
    {
        try {
            $tickets = SupportTicket::where('user_id', Auth::id())->latest()->paginate(basicControl()->paginate);
            if ($tickets->isEmpty()) {
                return response()->json($this->withError([]));
            }

            $formatedTicket = $tickets->getCollection()->map(function ($item) {
                return  [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'ticket' => $item->ticket,
                    'subject' => $item->subject,
                    'status' => $item->status,
                    'last_reply' => $item->last_reply,
                    'created_at' => $item->created_at,
                ];
            });
            $tickets->setCollection($formatedTicket);
            $info = [
                'status' => '0 = Open, 1 = Answered, 2 = Replied, 3 = Closed',
            ];
            return response()->json($this->withSuccess($tickets, $info));
        }catch (\Exception $exception){
            return response()->json($this->withError($exception->getMessage()));
        }
    }

    public function ticketCreate(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->newTicketValidation($request);
            $random = rand(100000, 999999);
            $ticket = $this->saveTicket($request, $random);
            $message = $this->saveMsgTicket($request, $ticket);

            if (!empty($request->attachments)) {
                $numberOfAttachments = count($request->attachments);
                for ($i = 0; $i < $numberOfAttachments; $i++) {
                    if ($request->hasFile('attachments.' . $i)) {
                        $file = $request->file('attachments.' . $i);
                        $supportFile = $this->fileUpload($file, config('filelocation.ticket.path'), null, null, 'webp', 99);
                        if (empty($supportFile['path'])) {
                            throw new \Exception('File could not be uploaded.');
                        }
                        $this->saveAttachment($message, $supportFile['path'], $supportFile['driver']);
                    }
                }
            }

            $msg = [
                'user' => optional($ticket->user)->username,
                'ticket_id' => $ticket->ticket
            ];
            $action = [
                "name" => optional($ticket->user)->firstname . ' ' . optional($ticket->user)->lastname,
                "image" => getFile(optional($ticket->user)->image_driver, optional($ticket->user)->image),
                "link" => route('admin.ticket.view',$ticket->id),
                "icon" => "fas fa-ticket-alt text-white"
            ];
            $this->adminPushNotification('SUPPORT_TICKET_CREATE', $msg, $action);
            $this->adminMail('SUPPORT_TICKET_CREATE', $msg);
            DB::commit();
            return response()->json($this->withSuccess('Your Ticket has been pending'));
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json($this->withError($exception->getMessage()));
        }
    }

    public function ticketView($ticketId)
    {
        try {
            $user = Auth::user();
            $formattedUser = [
                'id' => $user->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'username' => $user->username,
                'image' => getFile($user->image_driver, $user->image),
            ];
            $data['user'] = $formattedUser;

            $data['ticket'] = SupportTicket::with(['messages' => function ($query) {
                $query->select('id', 'support_ticket_id', 'admin_id', 'message','created_at');
            }, 'messages.attachments:id,support_ticket_message_id,file,driver','messages.admin:id,name,username,image,image_driver'])
                ->select('id', 'user_id', 'ticket', 'subject', 'status', 'last_reply')
                ->where('ticket', $ticketId)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'user_id' => $item->user_id,
                        'ticket' => $item->ticket,
                        'subject' => $item->subject,
                        'status' => $item->status,
                        'last_reply' => $item->last_reply,
                        'messages' => $item->messages->map(function ($message) {
                            return [
                                'id' => $message->id,
                                'support_ticket_id' => $message->support_ticket_id,
                                'admin_id' => $message->admin_id,
                                'admin_image' => $message->admin_id ? $message->admin->imgPath : null,
                                'message' => $message->message,
                                'attachments' => $message->attachments->map(function ($attachment) {
                                    return [
                                        'id' => $attachment->id,
                                        'support_ticket_message_id' => $attachment->support_ticket_message_id,
                                        'file' => getFile($attachment->driver, $attachment->file),
                                    ];
                                }),
                                'created_at' => $message->created_at,
                            ];
                        }),
                    ];
                });

            $info = [
                'status' => '0 = Open, 1 = Answered, 2 = Replied, 3 = Closed',
            ];
            return response()->json($this->withSuccess($data, $info));
        }catch (\Exception $exception){
            return response()->json($this->withError($exception->getMessage()));
        }
    }


    public function ticketReply(Request $request, $ticketId)
    {
        DB::beginTransaction();
        try {
            $ticket = SupportTicket::with('user')->where('ticket' , $ticketId)->first();
            if (!$ticket){
                return response()->json($this->withError('Ticket not found.'));
            }
            $message = new SupportTicketMessage();
            $rules = [
                'message' => 'required|string',
                'attachments.*' => 'max:4096|mimes:jpg,png,jpeg,pdf',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json($this->withError(collect($validator->errors())->collapse()));
            }

            $ticket->status = 2;
            $ticket->last_reply = Carbon::now();
            $ticket->save();

            $message->support_ticket_id = $ticket->id;
            $message->message = $request->message;
            $message->save();

            foreach ($request->file('attachments',[]) as $file) {
                $supportFile = $this->fileUpload($file, config('filelocation.ticket.path'), config('filesystems.default'), null, 'webp', 80);
                if (empty($supportFile['path'])) {
                    return response()->json($this->withError('File could not be uploaded'));
                }
                SupportTicketAttachment::create([
                    'support_ticket_message_id' => $message->id,
                    'file' => $supportFile['path'],
                    'driver' => $supportFile['driver'] ?? 'local',
                ]);
            }

            $msg = [
                'username' => optional($ticket->user)->username,
                'ticket_id' => $ticket->ticket
            ];
            $action = [
                "name" => optional($ticket->user)->firstname . ' ' . optional($ticket->user)->lastname,
                "image" => getFile(optional($ticket->user)->image_driver, optional($ticket->user)->image),
                "link" => route('admin.ticket.view',$ticket->id),
                "icon" => "fas fa-ticket-alt text-white"
            ];
            $this->adminPushNotification('SUPPORT_TICKET_CREATE', $msg, $action);
            DB::commit();
            return response()->json($this->withSuccess('Ticket has been replied'));
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json($this->withError($exception->getMessage()));
        }
    }

    public function ticketClose($ticket)
    {
        $ticketInfo = SupportTicket::where('user_id', Auth::id())->where('ticket', $ticket)->first();
        if (!$ticketInfo){
            return response()->json($this->withError('Ticket not found.'));
        }
        if ($ticketInfo->status == 3) {
            return response()->json($this->withError('Ticket is already Closed.'));
        } else {
            $ticketInfo->status = 3;
            $ticketInfo->save();
            return response()->json($this->withSuccess('Ticket has been closed'));
        }
    }


    public function newTicketValidation(Request $request): void
    {
        $images = $request->file('attachments');
        $allowedExtension = array('jpg', 'png', 'jpeg', 'pdf');

        $this->validate($request, [
            'attachments' => [
                'max:4096',
                function ($attribute, $value, $fail) use ($images, $allowedExtension) {
                    foreach ($images as $img) {
                        $ext = strtolower($img->getClientOriginalExtension());
                        if (($img->getSize() / 1000000) > 2) {
                            return response()->json($this->withError('Images MAX  2MB ALLOW!'));
                        }
                        if (!in_array($ext, $allowedExtension)) {
                            return response()->json($this->withError('Only png, jpg, jpeg, pdf images are allowed'));
                        }
                    }
                    if (count($images) > 5) {
                        return response()->json($this->withError('Maximum 5 images can be uploaded'));
                    }
                },
            ],
            'subject' => 'required|max:100',
            'message' => 'required'
        ]);
    }
    public function saveTicket(Request $request, $random): SupportTicket
    {
        $ticket = new SupportTicket();
        $ticket->user_id = Auth::id();
        $ticket->ticket = $random;
        $ticket->subject = $request->subject;
        $ticket->status = 0;
        $ticket->last_reply = Carbon::now();
        $ticket->save();
        return $ticket;
    }
    public function saveMsgTicket(Request $request, $ticket): SupportTicketMessage
    {
        $message = new SupportTicketMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();
        return $message;
    }
    public function saveAttachment($message, $path, $driver): void
    {
        $attachment = SupportTicketAttachment::create([
            'support_ticket_message_id' => $message->id,
            'file' => $path ?? null,
            'driver' => $driver ?? 'local',
        ]);
    }
}
