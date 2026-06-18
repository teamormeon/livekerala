<?php

use App\Http\Controllers\API\AnalyticsController;
use App\Http\Controllers\API\ApiPaymentController;
use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ClaimBusinessController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\Frontend\ListingController;
use App\Http\Controllers\API\MyListingController;
use App\Http\Controllers\API\MyPackagesController;
use App\Http\Controllers\API\NotificationPermissionController;
use App\Http\Controllers\API\PricingController;
use App\Http\Controllers\API\ProductQueryController;
use App\Http\Controllers\API\SupportTicketController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\TwoFaSecurityController;
use App\Http\Controllers\API\UserAuthController;
use App\Http\Controllers\API\UserProfileController;
use App\Http\Controllers\API\VerificationController;
use App\Http\Controllers\API\WishListController;
use App\Http\Controllers\User\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::any('subscription/{code}/{utr?}', [SubscriptionController::class, 'subscriptionIpn'])->name('subscription.ipn');

Route::match(['get', 'post'], 'payment/{code}/{trx?}/{type?}', [PaymentController::class, 'gatewayIpn'])->name('ipn');


//user authentication
Route::post('/register', [UserAuthController::class, 'register']);
Route::post('/login', [UserAuthController::class, 'login']);
Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:sanctum');
Route::delete('/delete-account/{id}', [UserAuthController::class, 'deleteAccount'])->middleware('auth:sanctum');
Route::post('/password-reset/email', [UserAuthController::class, 'getEmailForResetPassword']);
Route::post('/password-reset/code', [UserAuthController::class, 'getCodeForResetPassword']);
Route::post('/password-reset', [UserAuthController::class, 'passwordReset']);


Route::middleware('auth:sanctum')->group(function ()  {
    Route::get('/notification-permission', [NotificationPermissionController::class, 'notificationPermission']);
    Route::post('/notification-permission/update', [NotificationPermissionController::class, 'notificationPermissionUpdate']);
    Route::get('/pusher-config', [NotificationPermissionController::class, 'pusherConfig']);

    Route::get('two-step-security', [TwoFaSecurityController::class, 'twoStepSecurity']);
    Route::post('twoStep-enable', [TwoFaSecurityController::class, 'twoStepEnable']);
    Route::post('twoStep-disable', [TwoFaSecurityController::class, 'twoStepDisable']);

    Route::get('get-kyc/{id?}', [UserProfileController::class, 'getKycVerification']);
    Route::post('kyc/submit', [UserProfileController::class, 'kycVerificationSubmit']);
    Route::get('kyc-submit/list', [UserProfileController::class, 'kycVerificationSubmitList']);

    Route::get('check', [VerificationController::class, 'check']);
    Route::get('resend-code', [VerificationController::class, 'resendCode']);
    Route::post('mail-verify', [VerificationController::class, 'mailVerify']);
    Route::post('sms-verify', [VerificationController::class, 'smsVerify']);
    Route::post('twoFA-Verify', [VerificationController::class, 'twoFAverify']);

    Route::middleware(['CheckVerificationApi','kyc_api'])->group(function ()  {
        Route::get('/profile', [UserProfileController::class, 'profile']);
        Route::post('/update-profile', [UserProfileController::class, 'updateProfile']);
        Route::post('/update-password', [UserProfileController::class, 'updatePassword']);

        Route::get('/support-tickets', [SupportTicketController::class, 'index']);
        Route::post('/support-ticket/create', [SupportTicketController::class, 'ticketCreate']);
        Route::get('/support-ticket/view/{ticket}', [SupportTicketController::class, 'ticketView']);
        Route::post('/support-ticket/reply/{ticket}', [SupportTicketController::class, 'ticketReply']);
        Route::post('/support-ticket/reply/{ticket}', [SupportTicketController::class, 'ticketReply']);
        Route::post('/support-ticket/closed/{ticket}', [SupportTicketController::class, 'ticketClose']);

        Route::get('/analytics/{id?}', [AnalyticsController::class, 'analytics']);
        Route::get('/listing/analytic/details/{id?}', [AnalyticsController::class, 'listingAnalyticsShow']);

        Route::get('/transaction', [TransactionController::class, 'transaction']);

        Route::get('/wish-list', [WishListController::class, 'wishList']);
        Route::post('/wish-list/add', [WishListController::class, 'wishListAdd']);
        Route::delete('/wishlist-destroy/{id}', [WishListController::class, 'wishListDestroy']);

        Route::get('purchase-packages/{paginate?}', [MyListingController::class, 'purchasePackages']);
        Route::get('payment-history/{id}', [MyPackagesController::class, 'paymentHistory']);


        Route::get('listings/{type?}', [MyListingController::class, 'listings']);
        Route::post('add-listing/{purchase_package_id}', [MyListingController::class, 'addListing']);
        Route::get('edit-listing/{id}', [MyListingController::class, 'editListing']);
        Route::post('update-listing/{id}', [MyListingController::class, 'updateListing']);
        Route::delete('delete-listing/{id}', [MyListingController::class, 'deleteListing']);
        Route::get('reviews/{id}', [MyListingController::class, 'reviews']);
        Route::get('dynamic-form-data/{id}', [MyListingController::class, 'dynamicFormData']);
        Route::post('listing/import-csv', [MyListingController::class, 'listingImportCsv']);
        Route::get('listing/import-csv/sample/download', [MyListingController::class, 'listingImportCsvSampleDownload']);

        Route::post('/claim-business/{listing_id}', [ClaimBusinessController::class, 'claimBusiness']);
        Route::get('/claim-business-list/{type?}', [ClaimBusinessController::class, 'myClaimBusinessList']);
        Route::get('/claim-business/conversation/{uuid}', [ClaimBusinessController::class, 'myClaimBusinessConversation']);
        Route::post('/claim-business/push-chat/new-message', [ClaimBusinessController::class, 'newMessage']);

        Route::post('/send-product-query', [ProductQueryController::class, 'sendProductQuery']);
        Route::get('/product-enquiries/{type?}', [ProductQueryController::class, 'productQueries']);
        Route::get('/product-enquiry/replies/{product_enquiry_id}', [ProductQueryController::class, 'productQueryReply']);
        Route::post('/product-enquiry/new-message', [ProductQueryController::class, 'productQueryNewMessage']);
        Route::delete('/product-enquiry/delete/{product_enquiry_id}', [ProductQueryController::class, 'productQueryDelete']);

        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::post('/send-listing-message/{id}', [ListingController::class, 'sendListingMessage']);
        Route::post('/collect-listing-form-data', [ListingController::class, 'collectListingFormData']);
        Route::post('/listing-details/add-review', [ListingController::class, 'reviewPush']);

        Route::get('/listing-author-profile/{user_name?}', [ListingController::class, 'listingAuthorProfile']);
        Route::post('/author-profile-follow-or-unfollow/{user_id}', [ListingController::class, 'authorProfileFollowOrUnfollow']);
        Route::post('/send-message-to-listing-author/{user_id}', [ListingController::class, 'sendMessageToListingAuthor']);

        Route::get('packages', [PricingController::class, 'packages']);
        Route::get('package/payment/{id}/{type?}/{purchase_id?}', [PricingController::class, 'pricingPlanPayment']);

        Route::post('payment-request', [PricingController::class, 'paymentRequest']);
        Route::get('payment-webview/{trx_id}', [ApiPaymentController::class, 'paymentWebview']);

        Route::post('addFundConfirm/{trx_id}', [ApiPaymentController::class, 'fromSubmit']);

        Route::post('card-payment', [ApiPaymentController::class, 'cardPayment']);
        Route::post('payment-done', [ApiPaymentController::class, 'paymentDone']);
    });
    Route::get('/frontend/listings/{cat_id?}', [ListingController::class, 'listings']);
});

Route::get('/without-auth/frontend/listings/{cat_id?}', [ListingController::class, 'withoutAuthListings']);
Route::get('/frontend/listing-details/{slug}', [ListingController::class, 'listingDetails']);


Route::get('/app-config', [BaseController::class, 'appConfig']);
Route::get('/languages', [BaseController::class, 'languages']);
Route::get('/gateways', [BaseController::class, 'gateways']);
Route::get('country-list', [BaseController::class, 'countryList']);
Route::get('state-list/{country_id?}', [BaseController::class, 'stateList']);
Route::get('city-list/{state_id?}', [BaseController::class, 'cityList']);
Route::get('listing-cities', [BaseController::class, 'listingCities']);
Route::get('amenities', [BaseController::class, 'amenities']);
Route::post('/contact', [BaseController::class, 'contactSend']);
Route::post('/subscribe', [BaseController::class, 'subscribe']);

Route::get('/listing-categories/{id?}', [CategoryController::class, 'listingCategories']);

Route::get('payment-supported-currency', [PricingController::class, 'supportedCurrency']);
Route::post('payment-convert-amount', [PricingController::class, 'paymentConvertAmount']);
