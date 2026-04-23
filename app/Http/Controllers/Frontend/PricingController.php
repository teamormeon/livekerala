<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContentDetails;
use App\Models\Gateway;
use App\Models\Package;
use App\Models\Page;
use App\Models\PurchasePackage;
use App\Traits\Notify;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PricingController extends Controller
{
    use Notify;
    public function __construct()
    {
        $this->theme = template();
    }

    public function index()
    {
        $selectedTheme = getTheme();
        $pageSeo = Page::where('template_name', $selectedTheme)->where('slug', 'pricing')->first();
        $pageSeo['breadcrumb_image'] = $pageSeo?->breadcrumb_status == 1 ?  getFile($pageSeo->breadcrumb_image_driver, $pageSeo->breadcrumb_image) : null;
        $singleContent = ContentDetails::with('content')->whereHas('content', function ($query) use($selectedTheme) {
            $query->where('theme', $selectedTheme)
                ->where('name', 'pricing')
                ->where('type', 'single');
        })->first();
        $packages = Package::with('details', 'purchasePackages')->where('status', 1)->orderBy('price', 'ASC')->get();
        return view(template(). 'frontend.pricing.index', compact('pageSeo','singleContent', 'packages'));
    }

    public function pricingPlanPayment($id, $type = null, $purchase_id = null){
        $data['plan_id'] = $id;
        $data['purchase_id'] = $purchase_id;
        $package = Package::with('details')->where('status',1)->findOrFail($id);

        if ($package->isFreePurchase() == 'true' && $type != 'renew'){
            return back()->with('error', 'This package already purchased');
        }

        if ($type == 'renew') {
            return $this->handleRenewal($package, $data, $purchase_id);
        }
        return $this->handlePurchase($package, $data);
    }





    protected function handleRenewal($package, $data, $purchase_id)
    {
        $existingPurchasePackage = PurchasePackage::where('user_id', Auth::id())
            ->where('id', $purchase_id)
            ->where('package_id', $package->id)
            ->first();
        if (!$existingPurchasePackage || $existingPurchasePackage->is_renew != 1) {
            return back()->with('error', 'No previous purchase found for this package.');
        }
        if ($existingPurchasePackage->price == null || $existingPurchasePackage->price == 0) {
            $this->updatePurchasePackage($existingPurchasePackage, $package, 'Renew');
            $this->sendNotification($package, 'RENEWED_PACKAGE_BY_USER');
            return redirect()->back()->with('success', '`' . optional($package->details)->title . '` package has been Renewed');
        }
        return $this->showPaymentPage($package, $data, 'renew');
    }

    protected function handlePurchase($package, $data)
    {
        if ($package->price == null || $package->price == 0) {
            $purchasePackage = new PurchasePackage();
            $this->updatePurchasePackage($purchasePackage, $package, 'Purchase');
            $this->sendNotification($package, 'PURCHASE_PACKAGE_BY_USER');
            return redirect()->back()->with('success', '`' . optional($package->details)->title . '` package has been purchased');
        }
        return $this->showPaymentPage($package, $data);
    }

    protected function updatePurchasePackage($purchasePackage, $package, $type)
    {
        $purchasePackage->fill([
            'user_id' => Auth::id(),
            'package_id' => $package->id,
            'price' => $package->price,
            'is_renew' => $package->is_renew,
            'is_image' => $package->is_image,
            'is_video' => $package->is_video,
            'is_amenities' => $package->is_amenities,
            'is_product' => $package->is_product,
            'is_create_from' => $package->is_create_from,
            'is_business_hour' => $package->is_business_hour,
            'no_of_listing' => $package->no_of_listing,
            'no_of_img_per_listing' => $package->no_of_img_per_listing,
            'no_of_categories_per_listing' => $package->no_of_categories_per_listing,
            'no_of_amenities_per_listing' => $package->no_of_amenities_per_listing,
            'no_of_product' => $package->no_of_product,
            'no_of_img_per_product' => $package->no_of_img_per_product,
            'seo' => $package->seo,
            'is_whatsapp' => $package->is_whatsapp,
            'is_messenger' => $package->is_messenger,
            'status' => 1,
            'type' => $type,
            'purchase_date' => Carbon::now(),
            'expire_date' => $this->getExpiryDate($package)
        ]);
        $purchasePackage->save();
    }

    protected function getExpiryDate($package)
    {
        if ($package->expiry_time_type == 'Days' || $package->expiry_time_type == 'Day') {
            return Carbon::now()->addDays((int)$package->expiry_time);
        } elseif ($package->expiry_time_type == 'Months' || $package->expiry_time_type == 'Month') {
            return Carbon::now()->addMonths((int)$package->expiry_time);
        } elseif ($package->expiry_time_type == 'Years' || $package->expiry_time_type == 'Year') {
            return Carbon::now()->addYears((int)$package->expiry_time);
        }
        return null;
    }


    protected function sendNotification($package, $notificationType)
    {
        $senderName = Auth::user()->firstname . ' ' . Auth::user()->lastname;
        $senderImg = getFile(Auth::user()->image_driver, Auth::user()->image);
        $msg = [
            'package' => optional($package->details)->title,
            'from'    => $senderName,
        ];
        $action = [
            "link" => route('admin.purchase.package'),
            "image" => $senderImg,
            "icon" => "fa fa-money-bill-alt text-white"
        ];
        $this->adminPushNotification($notificationType, $msg, $action);
    }

    protected function showPaymentPage($package, $data, $type = null)
    {
        $selectedTheme = getTheme();
        $pageSeo = Page::where('template_name', $selectedTheme)->where('slug', 'pricing-payment')->first();
        $pageSeo['breadcrumb_image'] = $pageSeo?->breadcrumb_status == 1 ? getFile($pageSeo->breadcrumb_image_driver, $pageSeo->breadcrumb_image) : null;
        $user = Auth::user();
        $data['gateways'] = Gateway::where('status', 1)->get();
        return view(template() . 'frontend.pricing.payment_page', $data, compact('package', 'pageSeo', 'user', 'type'));
    }

}
