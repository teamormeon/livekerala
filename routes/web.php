<?php

use App\Http\Controllers\Auth\LoginController as UserLoginController;
use App\Http\Controllers\ChatNotificationController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\CommonController;
use App\Http\Controllers\Frontend\ListingController;
use App\Http\Controllers\Frontend\PricingController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\User\AnalyticsController;
use App\Http\Controllers\User\ClaimBusinessController;
use App\Http\Controllers\User\FavouriteController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\DepositController;
use App\Http\Controllers\User\MyListingController;
use App\Http\Controllers\User\MyPackagesController;
use App\Http\Controllers\User\NotificationPermissionController;
use App\Http\Controllers\ManualRecaptchaController;
use App\Http\Controllers\khaltiPaymentController;
use App\Http\Controllers\User\ProductQueryController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\SendMessageController;
use App\Http\Controllers\User\SubscriptionController;
use App\Http\Controllers\User\TransactionController;
use App\Http\Controllers\User\UserProfileSettingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InAppNotificationController;
use App\Http\Controllers\User\SupportTicketController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\User\VerificationController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\User\KycVerificationController;
use App\Http\Controllers\TwoFaSecurityController;
use App\Http\Controllers\API\ApiPaymentController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('payment/view/{deposit_id}', [ApiPaymentController::class, 'paymentView'])->name('paymentView');
$basicControl = basicControl();
Route::get('language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('lang', $locale);
    return redirect()->back();
})->name('language');


Route::get('maintenance-mode', function () {
    if (!basicControl()->is_maintenance_mode) {
        return redirect(route('page'));
    }
    $data['maintenanceMode'] = \App\Models\MaintenanceMode::first();
    return view(template() . 'maintenance', $data);
})->name('maintenance');

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPassword'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset')->middleware('guest');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset.update');

Route::get('instruction/page', function () {
    return view('instruction-page');
})->name('instructionPage');

Route::group(['middleware' => ['maintenanceMode']], function () use ($basicControl) {
    Route::group(['middleware' => ['guest']], function () {
        Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [UserLoginController::class, 'login'])->name('login.submit');
    });

    Route::group(['middleware' => ['auth'], 'prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('check', [VerificationController::class, 'check'])->name('check');
        Route::get('resend_code', [VerificationController::class, 'resendCode'])->name('resend.code');
        Route::post('mail-verify', [VerificationController::class, 'mailVerify'])->name('mail.verify');
        Route::post('sms-verify', [VerificationController::class, 'smsVerify'])->name('sms.verify');
        Route::post('twoFA-Verify', [VerificationController::class, 'twoFAverify'])->name('twoFA-Verify');

        Route::middleware('userCheck')->group(function () {

            Route::middleware('kyc')->group(function () {
                Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');
                Route::get('calender', [HomeController::class, 'calender'])->name('calender');
                Route::post('save-token', [HomeController::class, 'saveToken'])->name('save.token');

                Route::get('transaction-list', [HomeController::class, 'index'])->name('transaction');
                Route::get('transaction-search', [HomeController::class, 'search'])->name('transaction.search');

                /* ===== Manage Two Step ===== */
                Route::get('two-step-security', [TwoFaSecurityController::class, 'twoStepSecurity'])->name('twostep.security');
                Route::post('twoStep-enable', [TwoFaSecurityController::class, 'twoStepEnable'])->name('twoStepEnable');
                Route::post('twoStep-disable', [TwoFaSecurityController::class, 'twoStepDisable'])->name('twoStepDisable');
                Route::post('twoStep/re-generate', [TwoFaSecurityController::class, 'twoStepRegenerate'])->name('twoStepRegenerate');


                /* ===== Push Notification ===== */
                Route::get('push-notification-show', [InAppNotificationController::class, 'show'])->name('push.notification.show');
                Route::get('push.notification.readAll', [InAppNotificationController::class, 'readAll'])->name('push.notification.readAll');
                Route::get('push-notification-readAt/{id}', [InAppNotificationController::class, 'readAt'])->name('push.notification.readAt');

                Route::group(['prefix' => 'ticket', 'as' => 'ticket.'], function () {
                    Route::get('/', [SupportTicketController::class, 'index'])->name('list');
                    Route::get('/create', [SupportTicketController::class, 'create'])->name('create');
                    Route::post('/create', [SupportTicketController::class, 'store'])->name('store');
                    Route::get('/view/{ticket}', [SupportTicketController::class, 'ticketView'])->name('view');
                    Route::put('/reply/{ticket}', [SupportTicketController::class, 'reply'])->name('reply');
                    Route::get('/download/{ticket}', [SupportTicketController::class, 'download'])->name('download');
                });

                Route::get('packages/{id?}', [MyPackagesController::class, 'myPackages'])->name('myPackages');
                Route::get('payment-history/{id}', [MyPackagesController::class, 'paymentHistory'])->name('paymentHistory');


                Route::get('listings/{type?}', [MyListingController::class, 'listings'])->name('listings');
                Route::get('add-listing/{id}', [MyListingController::class, 'addListing'])->name('addListing');
                Route::post('store-listing/{id}', [MyListingController::class, 'storeListing'])->name('storeListing');
                Route::get('edit-listing/{id}', [MyListingController::class, 'editListing'])->name('editListing');
                Route::post('update-listing/{id}', [MyListingController::class, 'updateListing'])->name('updateListing')->middleware('demo');
                Route::post('update-listing-slug', [MyListingController::class, 'updateListingSlug'])->name('updateListingSlug')->middleware('demo');
                Route::delete('delete-listing/{id}', [MyListingController::class, 'deleteListing'])->name('deleteListing')->middleware('demo');
                Route::get('reviews/{id?}', [MyListingController::class, 'reviews'])->name('reviews');
                Route::get('dynamic-form-data/{id}', [MyListingController::class, 'dynamicFormData'])->name('dynamic.form.data');

                Route::any('listing/import-csv', [MyListingController::class, 'listingImportCsv'])->name('listing.import.csv');
                Route::get('listing/import-csv/sample/download', [MyListingController::class, 'listingImportCsvSampleDownload'])->name('listing.import.csv.sample.download');


                Route::post('/profile-follow/{id?}', [ProfileController::class, 'profileFollow'])->name('profile.follow');
                Route::post('/profile-unfollow/{id?}', [ProfileController::class, 'profileUnfollow'])->name('profile.unfollow');

                Route::post('/viewer-send-message-to-user/{id}', [SendMessageController::class, 'viewerSendMessageToUser'])->name('viewer.send.message.to.user');
                Route::post('/send-listing-message/{id}', [SendMessageController::class, 'sendListingMessage'])->name('send.listing.message');

                Route::get('/transaction', [TransactionController::class, 'transaction'])->name('transaction');

                Route::get('/analytics/{id?}', [AnalyticsController::class, 'analytics'])->name('analytics');
                Route::get('/listing/analytics/show/{id?}', [AnalyticsController::class, 'listingAnalyticsShow'])->name('analytics.show');

                Route::post('/add-to-wish-list', [FavouriteController::class, 'addToWishList'])->name('add.to.wish.list');
                Route::get('/wish-list', [FavouriteController::class, 'wishList'])->name('wish.list');
                Route::delete('/wish-list/delete/{id}', [FavouriteController::class, 'wishListDelete'])->name('wish.list.delete');

                Route::post('/listing-details/review-push', [ReviewController::class, 'reviewPush'])->name('review.push');

                Route::post('/claim-business/{id}', [ClaimBusinessController::class, 'claimBusiness'])->name('claim.business');
                Route::get('/claim-business-list/{type?}', [ClaimBusinessController::class, 'myClaimBusinessList'])->name('claim.business.list');
                Route::get('/claim-business/conversation/{uuid}', [ClaimBusinessController::class, 'myClaimBusinessConversation'])->name('claim.business.conversation');
                Route::get('/claim-business/push-chat-show/{uuId}', [ChatNotificationController::class, 'show'])->name('claim.business.push.chat.show');
                Route::post('/claim-business/push-chat/new-message', [ChatNotificationController::class, 'newMessage'])->name('claim.business.push.chat.new.message');

                Route::post('/send-product-query', [ProductQueryController::class, 'sendProductQuery'])->name('send.product.query');
                Route::get('/product-enquiries/{type?}', [ProductQueryController::class, 'productQueries'])->name('product.queries');
                Route::get('/product-enquiry/reply/{id}', [ProductQueryController::class, 'productQueryReply'])->name('product.query.reply');
                Route::post('/product-enquiry/reply/message', [ProductQueryController::class, 'productQueryReplyMessage'])->name('product.query.reply.message');
                Route::post('/product-enquiry/reply/message/render', [ProductQueryController::class, 'productQueryReplyMessageRender'])->name('product.query.reply.message.render');
                Route::delete('/product-enquiry/delete/{id}', [ProductQueryController::class, 'productQueryDelete'])->name('product.query.delete');

                Route::any('/subscription-process/{utr}', [SubscriptionController::class, 'subsConfirm'])->name('subscription.process');
                Route::post('/subscription-cancel/{id}', [SubscriptionController::class, 'subsCancel'])->name('subscription.cancel');

                Route::get('notification-permission', [NotificationPermissionController::class, 'notificationPermission'])->name('notification.permission');
                Route::post('notification-permission/update', [NotificationPermissionController::class, 'notificationPermissionUpdate'])->name('notification.permission.update');

            });

            Route::get('verification/kyc', [KycVerificationController::class, 'kyc'])->name('verification.kyc');
            Route::get('verification/kyc-form/{id}', [KycVerificationController::class, 'kycForm'])->name('verification.kyc.form');
            Route::post('verification/kyc/submit', [KycVerificationController::class, 'verificationSubmit'])->name('kyc.verification.submit');
            Route::get('verification/kyc/history', [KycVerificationController::class, 'history'])->name('verification.kyc.history');


            Route::get('profile', [UserProfileSettingController::class, 'profile'])->name('profile');
            Route::post('profile-image/update', [UserProfileSettingController::class, 'profileImageUpdate'])->name('profile.image.update');
            Route::post('profile-cover-image/update', [UserProfileSettingController::class, 'profileCoverImageUpdate'])->name('profile.cover.image.update');
            Route::put('profile-update', [UserProfileSettingController::class, 'profileUpdate'])->name('profile.update');
            Route::post('update/password', [UserProfileSettingController::class, 'updatePassword'])->name('updatePassword');

            Route::get('/pricing/plan/payment/{id}/{type?}/{purchase_id?}', [PricingController::class, 'pricingPlanPayment'])->name('pricing.plan.payment');

        });
    });


    Route::post('/get-states-of-country', [MyListingController::class, 'getStates'])->name('get.states');
    Route::post('/get-cities-of-state', [MyListingController::class, 'getCities'])->name('get.cities');

    Route::get('captcha', [ManualRecaptchaController::class, 'reCaptCha'])->name('captcha');

    /* Manage User Deposit */
    Route::get('supported-currency', [DepositController::class, 'supportedCurrency'])->name('supported.currency');
    Route::post('payment-request', [DepositController::class, 'paymentRequest'])->name('payment.request');
    Route::get('deposit-check-amount', [DepositController::class, 'checkAmount'])->name('deposit.checkAmount');
    Route::get('deposit-check-convert-amount', [DepositController::class, 'checkConvertAmount'])->name('deposit.checkConvertAmount');

    Route::get('payment-process/{trx_id}', [PaymentController::class, 'depositConfirm'])->name('payment.process');
    Route::post('addFundConfirm/{trx_id}', [PaymentController::class, 'fromSubmit'])->name('addFund.fromSubmit');
    Route::match(['get', 'post'], 'success', [PaymentController::class, 'success'])->name('success');
    Route::match(['get', 'post'], 'failed', [PaymentController::class, 'failed'])->name('failed');
    Route::match(['get', 'post'], 'payment/{code}/{trx?}/{type?}', [PaymentController::class, 'gatewayIpn'])->name('ipn');
    Route::get('paddle-subscription/{subPurId?}/{trx_id?}', function ($subPurId, $trx_id) {
        Facades\App\Services\Subscription\paddle\Payment::createSubscription($subPurId, $trx_id);
        return redirect()->route('success');
    })->name('paddleSubscription');

    Route::post('khalti/payment/verify/{trx}', [\App\Http\Controllers\khaltiPaymentController::class, 'verifyPayment'])->name('khalti.verifyPayment');
    Route::post('khalti/payment/store', [khaltiPaymentController::class, 'storePayment'])->name('khalti.storePayment');


    Route::get('pricing', [PricingController::class, 'index'])->name('pricing');

    Route::get('category', [CategoryController::class, 'index'])->name('category');
    Route::post('category/search', [CategoryController::class, 'categorySearch'])->name('category.search');

    Route::get('blogs', [BlogController::class, 'blogs'])->name('blogs');
    Route::get('blog/{slug?}', [BlogController::class, 'blogDetails'])->name('blog.details');

    Route::get('/profile/{user_name?}', [ProfileController::class, 'profile'])->name('profile');



    Route::get('/listings/{id?}/{type?}', [ListingController::class, 'listings'])->name('listings');
    Route::get('/listing/{slug}', [ListingController::class, 'listingDetails'])->name('listing.details');
    Route::get('/listing-reviews/{id?}', [ListingController::class, 'listingReviewsGet'])->name('listing.reviews.get');
    Route::post('/collect-listing-form-data', [ListingController::class, 'collectListingFormData'])->name('collect.listing.form.data');



    Route::post('/getFilePath', [CommonController::class, 'getFilePath'])->name('getFilePath');

    Route::post('/contact', [CommonController::class, 'contactSend'])->name('contact.send');
    Route::get('/cookie-policy', [CommonController::class, 'cookiePolicy'])->name('cookie-policy');
    Route::post('/subscribe', [CommonController::class, 'subscribe'])->name('subscribe');
    Auth::routes();


    //Social Login
    Route::get('auth/{socialite}', [SocialiteController::class, 'socialiteLogin'])->name('socialiteLogin');
    Route::get('auth/callback/{socialite}', [SocialiteController::class, 'socialiteCallback'])->name('socialiteCallback');

    /*= Frontend Manage Controller =*/
    Route::get("/{slug?}", [FrontendController::class, 'page'])->name('page')->middleware('track.visitors');
});


