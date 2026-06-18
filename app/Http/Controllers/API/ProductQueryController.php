<?php

namespace App\Http\Controllers\API;

use App\Events\ChatEvent;
use App\Events\UserNotification;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Product;
use App\Models\ProductQuery;
use App\Models\ProductReply;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Traits\Notify;
use App\Traits\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductQueryController extends Controller
{
    use ApiResponse, Notify, Upload;

    public function sendProductQuery(Request $request)
    {
        $rules = [
            'message' => 'required',
        ];
        $message = [
            'message.required' => __('Please write your message'),
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return response()->json($this->withError(collect($validator->errors())->collapse()));
        }

        $listing = Listing::select('id','user_id','slug')->toBase()->find($request->listing_id);
        if (!$listing){
            return response()->json($this->withError('Listing not found'));
        }

        $user = auth()->user();
        if ($listing->user_id == $user->id) {
            return response()->json($this->withError('You cannot make any queries on your own product'));
        } else {
            $productQuery = new ProductQuery();
            $productQuery->user_id = $listing->user_id;
            $productQuery->client_id = $user->id;
            $productQuery->listing_id = $request->listing_id;
            $productQuery->product_id = $request->product_id;
            $productQuery->message = $request->message;
            $productQuery->save();

            $product = Product::select('id','user_id','listing_id','product_title')->toBase()->find($request->product_id);

            $senderName = $user->firstname . ' ' . $user->lastname;
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

            $listingOwner = User::find($listing->user_id);

            $this->userPushNotification($listingOwner, 'PRODUCT_QUERY_FOR_USER', $msg, $action);
            $this->adminPushNotification('PRODUCT_QUERY_FOR_ADMIN', $msg, $adminAction);

            $this->sendMailSms($listingOwner, 'PRODUCT_QUERY_FOR_USER', [
                'productTitle' => $product->product_title ?? null,
                'from' => $senderName,
            ]);
            return response()->json($this->withSuccess('Product Query successfully sent.'));
        }
    }

    public function productQueries(Request $request, $type = null)
    {
        $types = ['customer-enquiry', 'my-enquiry'];
        if (!in_array($type, $types)){
            return response()->json($this->withError('Invalid type! use customer-enquiry or my-enquiry only'));
        }

        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $productQueries = ProductQuery::with(['get_listing:id,title,slug','get_product:id,product_title',
            'get_user:id,firstname,lastname,username,email,image,image_driver',
            'get_client:id,firstname,lastname,username,email,image,image_driver'])
            ->whereHas('get_client')
            ->select('id','user_id','client_id','listing_id','product_id','customer_enquiry','my_enquiry','created_at')
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

        $formatedProductQueries = $productQueries->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'client_id' => $item->client_id,
                'listing_id' => $item->listing_id,
                'product_id' => $item->product_id,
                'customer_enquiry' => $item->customer_enquiry,
                'my_enquiry' => $item->my_enquiry,
                'created_at' => $item->created_at,
                'listing' => [
                    'title' => $item->get_listing->title,
                    'slug' => $item->get_listing->slug,
                ],
                'product' => [
                    'title' => $item->get_product->product_title,
                ],
                'listing_owner' => [
                    'firstname' => $item->get_user->firstname,
                    'lastname' => $item->get_user->lastname,
                    'username' => $item->get_user->username,
                    'email' => $item->get_user->email,
                    'imgPath' => $item->get_user->imgPath,
                ],
                'listing_client' => [
                    'firstname' => $item->get_client->firstname,
                    'lastname' => $item->get_client->lastname,
                    'username' => $item->get_client->username,
                    'email' => $item->get_client->email,
                    'imgPath' => $item->get_client->imgPath,
                ],
            ];
        });
        $productQueries->setCollection($formatedProductQueries);
        return response()->json($this->withSuccess($productQueries));
    }

    public function productQueryReply($product_enquiry_id)
    {
        $all_unseen_messages = ProductReply::where('client_id', auth()->id())
            ->where('product_query_id', $product_enquiry_id)
            ->where('status', 0)
            ->select('id')
            ->pluck('id');
        if ($all_unseen_messages->isNotEmpty()) {
            ProductReply::whereIn('id', $all_unseen_messages)
                ->update(['status' => 1]);
        }

        $singleProductQuery = ProductQuery::with(['get_listing:id,title,slug',
            'get_product:id,product_title,product_price,product_thumbnail,driver',
            'get_user:id,firstname,lastname,username,website,email,image,image_driver,address_one,address_two',
            'get_client:id,firstname,lastname,username,website,email,image,image_driver,address_one,address_two',
            'replies:id,user_id,client_id,product_query_id,reply,file,driver,status,created_at'])->find($product_enquiry_id);

        if (Auth::user()->id == $singleProductQuery->user_id) {
            ProductQuery::where('id', $product_enquiry_id)
                ->update(['customer_enquiry' => 1]);
        }

        $formatedProductQuery = [
            'id' => $singleProductQuery->id,
            'user_id' => $singleProductQuery->user_id,
            'client_id' => $singleProductQuery->client_id,
            'listing_id' => $singleProductQuery->listing_id,
            'product_id' => $singleProductQuery->product_id,
            'message' => $singleProductQuery->message,
            'customer_enquiry' => $singleProductQuery->customer_enquiry,
            'my_enquiry' => $singleProductQuery->my_enquiry,
            'created_at' => $singleProductQuery->created_at,
            'listing' => [
                'title' => $singleProductQuery->get_listing->title,
                'slug' => $singleProductQuery->get_listing->slug,
            ],
            'product' => [
                'title' => $singleProductQuery->get_product->product_title,
                'price' => $singleProductQuery->get_product->product_price,
                'thumbnail' => getFile($singleProductQuery->get_product->driver, $singleProductQuery->get_product->product_thumbnail),
            ],
            'user' => [
                'firstname' => $singleProductQuery->get_user->firstname,
                'lastname' => $singleProductQuery->get_user->lastname,
                'username' => $singleProductQuery->get_user->username,
                'website' => $singleProductQuery->get_user->website,
                'email' => $singleProductQuery->get_user->email,
                'address' => $singleProductQuery->get_user->fullAddress,
                'imgPath' => $singleProductQuery->get_user->imgPath,
            ],
            'client' => [
                'firstname' => $singleProductQuery->get_client->firstname,
                'lastname' => $singleProductQuery->get_client->lastname,
                'username' => $singleProductQuery->get_client->username,
                'website' => $singleProductQuery->get_client->website,
                'email' => $singleProductQuery->get_client->email,
                'address' => $singleProductQuery->get_client->fullAddress,
                'imgPath' => $singleProductQuery->get_client->imgPath,
            ],
            'replies' => $singleProductQuery->replies->map(function ($item) {
                return [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'client_id' => $item->client_id,
                    'reply' => $item->reply,
                    'file' => $item->file ? getFile($item->driver, $item->file) : null,
                    'status' => $item->status,
                    'created_at' => $item->created_at,
                    'sent_at' => $item->sent_at,
                ];
            }),
        ];

        $info = [
            'replies status' => '0 = unseen, 1 = seen'
        ];

        return response()->json($this->withSuccess($formatedProductQuery, $info));
    }

    public function productQueryNewMessage(Request $request)
    {
        $rules = [
            'client_id' => 'required',
            'product_query_id' => 'required',
            'message' => 'required',
            'file' => 'nullable|mimes:jpg,png,jpeg,PNG|max:3072',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($this->withError(collect($validator->errors())->collapse()));
        }

        $productReply = new ProductReply();
        $productReply->user_id = auth()->id();
        $productReply->client_id = $request->client_id;
        $productReply->product_query_id = $request->product_query_id;
        $productReply->reply = $request->message;

        if($request->hasFile('file')){
            $image = $this->fileUpload($request->file, config('filelocation.productQueryMessage.path'), null, null, 'webp', 99);
            if ($image) {
                $productReply->file = $image['path'];
                $productReply->driver = $image['driver'];
            }
        }
        $productReply->save();

        $fileImage = $productReply->file ? getFile($productReply->driver, $productReply->file) : null;
        $sender_image = getFile(auth()->user()->image_driver, auth()->user()->image);

        $response = [
            'user_id' => $productReply->user_id,
            'client_id' => $productReply->client_id,
            'product_query_id' => $productReply->product_query_id,
            'reply' => $productReply->reply,
            'fileImage' => $fileImage,
            'sender_image' => $sender_image,
        ];

        $this->sendRealTimeProductEnquiryThrowFirebase($productReply->get_user, $response);
        $this->sendRealTimeProductEnquiryThrowFirebase($productReply->get_client, $response);

//        event(new UserNotification($response, $productReply->user_id));
        ChatEvent::dispatch((object) $response);
        return response()->json($this->withSuccess('Message Send'));
    }

    public function productQueryDelete($product_enquiry_id)
    {
        $productQuery = ProductQuery::with('replies')->where('user_id', auth()->id())->find($product_enquiry_id);
        if (!$productQuery){
            return response()->json($this->withError('Product Query not found'));
        }
        $productQuery->replies()->delete();
        $productQuery->delete();
        return response()->json($this->withSuccess('Query Deleted Successfully'));
    }



}
