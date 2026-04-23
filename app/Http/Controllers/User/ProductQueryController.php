<?php

namespace App\Http\Controllers\User;

use App\Events\ChatEvent;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Product;
use App\Models\ProductQuery;
use App\Models\ProductReply;
use App\Models\User;
use App\Traits\Notify;
use App\Traits\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductQueryController extends Controller
{
    use Notify, Upload;

    public function sendProductQuery(Request $request)
    {
        $rules = [
            'message' => 'required',
        ];
        $message = [
            'message.required' => __('Please write your message'),
        ];
        $request->validate($rules, $message);

        $listing = Listing::findOrFail($request->listing_id);
        if ($listing->user_id == auth()->id()) {
            return back()->with('error', __('You cannot make any queries on your own product'));
        } else {
            $productQuery = new ProductQuery();
            $productQuery->user_id = $listing->user_id;
            $productQuery->client_id = auth()->id();
            $productQuery->listing_id = $request->listing_id;
            $productQuery->product_id = $request->product_id;
            $productQuery->message = $request->message;
            $productQuery->save();

            $product = Product::findOrFail($request->product_id);

            $senderName = Auth::user()->firstname . ' ' . Auth::user()->lastname;
            $msg = [
                'productTitle' => $product->product_title,
                'from' => $senderName,
            ];

            $action = [
                "link" => route('user.product.queries'),
                "icon" => "fa fa-money-bill-alt text-white"
            ];

            $adminAction = [
                "link" => route('listing.details', $listing->slug),
                "icon" => "fa fa-money-bill-alt text-white"
            ];

            $user = User::findOrFail($listing->user_id);

            $this->userPushNotification($user, 'PRODUCT_QUERY_FOR_USER', $msg, $action);
            $this->adminPushNotification('PRODUCT_QUERY_FOR_ADMIN', $msg, $adminAction);

            $this->sendMailSms($user, 'PRODUCT_QUERY_FOR_USER', [
                'productTitle' => $product->product_title ?? null,
                'from' => $senderName,
            ]);
            return back()->with('success', __('Your query has been sent'));
        }
    }

    public function productQueries(Request $request, $type = null)
    {
        $types = ['customer-enquiry', 'my-enquiry'];
        abort_if(!in_array($type, $types), 404);

        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $data['customerEnquery'] = ProductQuery::where('user_id', auth()->id())->where('customer_enquiry', 0)->get();

        $data['customerReply'] = ProductQuery::has('unseenReplies')
            ->where('user_id', auth()->id())
            ->count();

        $data['myReply'] = ProductQuery::has('unseenReplies')
            ->where('client_id', auth()->id())
            ->count();

        $data['type'] = $type;
        $data['productQueries'] = ProductQuery::with(['get_user', 'get_client', 'get_listing', 'get_product'])
            ->when(isset($search['name']), function ($query) use ($search) {
                return $query->whereHas('get_listing', function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search['name']}%");
                })->orWhereHas('get_product', function ($q2) use ($search) {
                    $q2->where('product_title', 'LIKE', "%{$search['name']}%");
                });
            })
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate,$toDate) {
                return $q2->whereBetween('created_at', [$fromDate,$toDate]);
            })
            ->when($type == 'customer-enquiry', function ($query) {
                return $query->where('user_id', auth()->id())->where('client_id', '!=', auth()->id());
            })
            ->when($type == 'my-enquiry', function ($query) {
                return $query->where('client_id', auth()->id())->where('user_id', '!=', auth()->id());
            })
            ->latest()
            ->paginate(basicControl()->paginate);
        return view('user_panel.user.product_enquiries.queries', $data);
    }

    public function productQueryReply($id)
    {
        $all_unseen_messages = ProductReply::where('product_query_id', $id)->where('client_id', auth()->id())->where('status', 0)->get();
        foreach ($all_unseen_messages as $message) {
            ProductReply::findOrFail($message->id)->update([
                'status' => 1,
            ]);
        }

        $data['singleProductQuery'] = ProductQuery::with(['get_user', 'get_client', 'get_product', 'get_listing.get_user'])->findOrFail($id);

        if (Auth::user()->id == $data['singleProductQuery']->user_id) {
            ProductQuery::findOrFail($id)->update([
                'customer_enquiry' => 1,
            ]);
        }
        return view('user_panel.user.product_enquiries.queryReply', $data, compact('id'));
    }

    public function productQueryReplyMessage(Request $request)
    {
        $request->validate([
            'file' => 'nullable|mimes:jpg,png,jpeg,PNG|max:3072',
        ]);

        $productReply = new ProductReply();
        $productReply->user_id = auth()->id();
        $productReply->client_id = $request->client_id;
        $productReply->product_query_id = $request->product_query_id;
        $productReply->reply = $request->reply;

        if($request->hasFile('file')){
            $image = $this->fileUpload($request->file, config('filelocation.productQueryMessage.path'), null, null, 'webp', 99);
            if ($image) {
                $productReply->file = $image['path'];
                $productReply->driver = $image['driver'];
            }
            $fileImage = getFile($productReply->driver, $image);
        } else{
            $fileImage = null;
        }
        $productReply->save();

        $sender_image = getFile(auth()->user()->image_driver, auth()->user()->image);

        $response = [
            'user_id' => $productReply->user_id,
            'client_id' => $productReply->client_id,
            'product_query_id' => $productReply->product_query_id,
            'reply' => $productReply->reply,
            'fileImage' => $fileImage,
            'sender_image' => $sender_image,
        ];
        ChatEvent::dispatch((object) $response);
        return response()->json($response);
    }

    public function productQueryReplyMessageRender(Request $request)
    {
        $messages = ProductReply::with('get_user', 'get_client')
            ->where('product_query_id', $request->productId)
            ->orderBy('id', 'ASC')
            ->get()
            ->map(function ($item) {
                $image = getFile($item->get_user->image_driver, $item->get_user->image);
                $item['sender_image'] = $image;
                return $item;
            })
            ->map(function ($item) {
                $image = getFile($item->get_client->image_driver, $item->get_client->image);
                $item['receiver_image'] = $image;
                return $item;
            })
            ->map(function ($item) {
                if (isset($item->file)){
                    $file = getFile($item->driver, $item->file);
                    $item['fileImage'] = $file;
                }
                return $item;
            });
        $messages->push(auth()->user());
        return response()->json($messages);
    }

    public function productQueryDelete($id)
    {
        $productQuery = ProductQuery::with('replies')->where('user_id', auth()->id())->findOrFail($id);
        $productQuery->replies()->delete();
        $productQuery->delete();
        return back()->with('success', __('Deleted Successful!'));
    }

}
