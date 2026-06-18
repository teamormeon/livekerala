<?php

namespace App\Jobs;

use App\Models\SupportTicket;
use App\Models\User;
use App\Traits\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UserAllRecordDeleteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Upload;

    public $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::table('claim_businesses')->where('claim_by_id', $this->userId)->orWhere('listing_owner_id', $this->userId)->delete();
        DB::table('claim_business_chatings')->where('userable_type', User::class)->where('userable_id', $this->userId)->delete();
        DB::table('analytics')->where('listing_owner_id', $this->userId)->delete();
        DB::table('contact_messages')->where('user_id', $this->userId)->orWhere('client_id', $this->userId)->delete();
        DB::table('favourites')->where('user_id', $this->userId)->orWhere('client_id', $this->userId)->delete();
        DB::table('followers')->where('user_id', $this->userId)->orWhere('following_id', $this->userId)->delete();
        DB::table('dynamic_forms')->where('user_id', $this->userId)->delete();
        DB::table('listings')->where('user_id', $this->userId)->delete();
        DB::table('products')->where('user_id', $this->userId)->delete();
        DB::table('product_queries')->where('user_id', $this->userId)->orWhere('client_id', $this->userId)->delete();
        DB::table('product_replies')->where('user_id', $this->userId)->orWhere('client_id', $this->userId)->delete();
        DB::table('purchase_packages')->where('user_id', $this->userId)->delete();
        DB::table('user_reviews')->where('user_id', $this->userId)->delete();
        DB::table('user_socials')->where('user_id', $this->userId)->delete();
        DB::table('viewers')->where('user_id', $this->userId)->delete();


        DB::table('deposits')->where('user_id', $this->userId)->delete();
        DB::table('payouts')->where('user_id', $this->userId)->delete();
        DB::table('transactions')->where('user_id', $this->userId)->delete();
        DB::table('user_kycs')->where('user_id', $this->userId)->delete();
        DB::table('user_logins')->where('user_id', $this->userId)->delete();

        SupportTicket::where('user_id', $this->userId)->get()->map(function ($item) {
            $item->messages()->get()->map(function ($message) {
                if (count($message->attachments) > 0) {
                    foreach ($message->attachments as $img) {
                        $this->fileDelete($img->driver, $img->file);
                        $img->delete();
                    }
                }
            });
            $item->messages()->delete();
            $item->delete();
        });


        DB::table('in_app_notifications')->where('in_app_notificationable_id', $this->userId)->where('in_app_notificationable_type', 'App\Models\User')->delete();
    }
}
