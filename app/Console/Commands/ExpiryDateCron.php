<?php

namespace App\Console\Commands;


use App\Mail\ExpiryDateMail;
use App\Models\PackageExpiryCron;
use App\Models\PurchasePackage;
use App\Models\User;
use App\Traits\Notify;
use App\Traits\Upload;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ExpiryDateCron extends Command
{
    use Notify, Upload;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expiryDate:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $packageExpiryTimes = PackageExpiryCron::select('id','before_expiry_date')->get();
        $purchasePackages = DB::table('purchase_packages')
            ->join('users', 'purchase_packages.user_id', '=', 'users.id')
            ->join('package_details', 'purchase_packages.package_id', '=', 'package_details.package_id')
            ->select('purchase_packages.id as purchase_package_id', 'purchase_packages.expire_date', 'purchase_packages.last_reminder_at', 'users.id as user_id', 'users.email', 'package_details.title')
            ->whereNotNull('expire_date')->get();

        foreach ($purchasePackages as $package){
            foreach ($packageExpiryTimes as $time){
                if(today()->addDays($time->before_expiry_date)->eq(Carbon::parse($package->expire_date)) && today()->lt($package->last_reminder_at)){
                    $details = [
                        'sub'          => '['.basicControl()->site_title.']'.' Sent you a package Information',
                        'message'      => 'Only ' . $time->before_expiry_date . ' days left for your ' . '`' .$package->title. '`' . ' package to expire',
                    ];
                    Mail::to($package->email)->send(new ExpiryDateMail($details));

                    $msg = [
                        'message' => 'Only ' . $time->before_expiry_date . ' days left for your ' . '`' .$package->title. '`' . ' package to expire',
                    ];

                    $action = [
                        "link" => route('user.myPackages'),
                        "icon" => "fa fa-money-bill-alt text-white"
                    ];

                    $user = User::findOrFail($package->user_id);
                    $this->userPushNotification($user, 'EXPIRY_DATE_NOTIFICATION', $msg, $action);

                    $purchasePackage = PurchasePackage::findOrFail($package->purchase_package_id);
                    $purchasePackage->last_reminder_at = now();
                    $purchasePackage->save();
                }
            }
        }
    }
}
