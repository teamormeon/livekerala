<?php

use App\Http\Controllers\Admin\AmenitiesController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\BasicControlController;
use App\Http\Controllers\Admin\ClaimBusinessController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailConfigController;
use App\Http\Controllers\Admin\FavouriteController;
use App\Http\Controllers\Admin\FirebaseConfigController;
use App\Http\Controllers\Admin\GdprCookieController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\ListingCategoryController;
use App\Http\Controllers\Admin\ListingController;
use App\Http\Controllers\Admin\LogoController;
use App\Http\Controllers\Admin\ManageMenuController;
use App\Http\Controllers\Admin\ManageRolePermissionController;
use App\Http\Controllers\Admin\ManageThemeController;
use App\Http\Controllers\Admin\ManualGatewayController;
use App\Http\Controllers\Admin\MapController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PaymentLogController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PluginController;
use App\Http\Controllers\Admin\PurchasePackageController;
use App\Http\Controllers\Admin\PusherConfigController;
use App\Http\Controllers\Admin\SmsConfigController;
use App\Http\Controllers\Admin\SocialiteController;
use App\Http\Controllers\Admin\StorageController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\TransactionLogController;
use App\Http\Controllers\Admin\TranslateAPISettingController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\ChatNotificationController;
use App\Http\Controllers\InAppNotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminProfileSettingController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\MaintenanceModeController;
use App\Http\Controllers\Admin\NotificationTemplateController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController;

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

Route::get('clear', function () {
    Illuminate\Support\Facades\Artisan::call('optimize:clear');
    $previousUrl = url()->previous();
    if (str_contains($previousUrl, 'push-notification')) {
        return redirect('/')->with('success', 'Cache Cleared Successfully');
    }
    return redirect()->back(fallback: '/')->with('success', 'Cache Cleared Successfully');
})->name('clear');

Route::get('queue-work', function () {
    return Illuminate\Support\Facades\Artisan::call('queue:work', ['--stop-when-empty' => true]);
})->name('queue.work');

Route::get('schedule-run', function () {
    return Illuminate\Support\Facades\Artisan::call('schedule:run');
})->name('schedule:run');

$basicControl = basicControl();
Route::group(['prefix' => $basicControl->admin_prefix ?? 'admin', 'as' => 'admin.'], function () {
    Route::get('/themeMode/{themeType?}', function ($themeType = 'true') {
        session()->put('themeMode', $themeType);
        return $themeType;
    })->name('themeMode');

    /*== Authentication Routes ==*/
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest:admin');
    Route::post('login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request')
        ->middleware('guest:admin');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset')->middleware('guest:admin');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])
        ->name('admin.password.reset.update');


    Route::middleware(['auth:admin', 'permission','demo'])->group(function () {

        Route::get('profile', [AdminProfileSettingController::class, 'profile'])->name('profile');
        Route::put('profile', [AdminProfileSettingController::class, 'profileUpdate'])->name('profile.update');
        Route::put('password', [AdminProfileSettingController::class, 'passwordUpdate'])->name('password.update');
        Route::post('notification-permission', [AdminProfileSettingController::class, 'notificationPermission'])->name('notification.permission');


        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('save-token', [DashboardController::class, 'saveToken'])->name('save.token');

        Route::get('dashboard/monthly-deposit-withdraw', [DashboardController::class, 'monthlyDepositWithdraw'])->name('monthly.deposit.withdraw');
        Route::get('dashboard/chartUserRecords', [DashboardController::class, 'chartUserRecords'])->name('chartUserRecords');
        Route::get('dashboard/chartTicketRecords', [DashboardController::class, 'chartTicketRecords'])->name('chartTicketRecords');
        Route::get('dashboard/chartKycRecords', [DashboardController::class, 'chartKycRecords'])->name('chartKycRecords');
        Route::get('dashboard/chartTransactionRecords', [DashboardController::class, 'chartTransactionRecords'])->name('chartTransactionRecords');

        Route::get('chartBrowserHistory', [DashboardController::class, 'chartBrowserHistory'])->name('chart.browser.history');
        Route::get('chartOsHistory', [DashboardController::class, 'chartOsHistory'])->name('chart.os.history');
        Route::get('chartDeviceHistory', [DashboardController::class, 'chartDeviceHistory'])->name('chart.device.history');

        Route::get('dashboard/sales-revenue-history', [DashboardController::class, 'getSalesRevenueHistory'])->name('get.salesRevenueHistory');
        Route::get('dashboard/total-sales-history', [DashboardController::class, 'totalSalesHistory'])->name('get.totalSalesHistory');
        Route::get('dashboard/visitors-history', [DashboardController::class, 'visitorsHistory'])->name('get.visitorsHistory');

        Route::get('/403', [DashboardController::class, 'forbidden'])->name('403');


        Route::get('role/role-list', [ManageRolePermissionController::class, 'roleList'])->name('role');
        Route::get('get/role-search', [ManageRolePermissionController::class, 'roleListSearch'])->name('role.search');
        Route::get('get/role/{id}', [ManageRolePermissionController::class, 'getRole'])->name('get.role');
        Route::post('role/create', [ManageRolePermissionController::class, 'roleCreate'])->name('role.create');
        Route::post('role/update', [ManageRolePermissionController::class, 'roleUpdate'])->name('role.update');
        Route::delete('role/delete/{id}', [ManageRolePermissionController::class, 'roleDelete'])->name('role.delete');

        Route::get('manage/staffs', [ManageRolePermissionController::class, 'staffList'])->name('role.staff');
        Route::get('get/staffs/list', [ManageRolePermissionController::class, 'getStaffList'])->name('get.staff.list');
        Route::get('edit/staff/{id}', [ManageRolePermissionController::class, 'editStaff'])->name('edit.staff');
        Route::post('manage/staff/update', [ManageRolePermissionController::class, 'staffUpdate'])->name('staff.role.update');
        Route::get('manage/staffs/create', [ManageRolePermissionController::class, 'staffCreate'])->name('staff.create');
        Route::post('manage/staffs/store', [ManageRolePermissionController::class, 'staffStore'])->name('role.usersCreate');
        Route::post('manage/staffs/status/change/{id}', [ManageRolePermissionController::class, 'statusChange'])->name('role.statusChange');
        Route::post('manage/staffs/login/{id}', [ManageRolePermissionController::class, 'userLogin'])->name('role.usersLogin');


        Route::get('/packages', [PackageController::class, 'package'])->name('package');
        Route::get('/package-search', [PackageController::class, 'packageSearch'])->name('package.search');
        Route::get('/package/create', [PackageController::class, 'packageCreate'])->name('package.create');
        Route::post('/package/store/{language?}', [PackageController::class, 'packageStore'])->name('package.store');
        Route::get('/package/edit/{id}', [PackageController::class, 'packageEdit'])->name('package.edit');
        Route::put('/package/update/{id}/{language?}', [PackageController::class, 'packageUpdate'])->name('package.update');
        Route::delete('/package/delete/{id}', [PackageController::class, 'packageDelete'])->name('package.delete');

        Route::get('/purchase/packages', [PurchasePackageController::class, 'purchasePackage'])->name('purchase.package');
        Route::get('/purchase/package-search', [PurchasePackageController::class, 'purchasePackageSearch'])->name('purchase.package.search');
        Route::delete('/purchase/package/subscription-cancel/{id}', [PurchasePackageController::class, 'purchasePackageSubscriptionCancel'])->name('purchase.package.subscription.cancel');
        Route::post('/purchase/package/export-excel', [PurchasePackageController::class, 'purchasePackageExportExcel'])->name('purchase.package.export.excel');
        Route::post('/purchase/package/export-csv', [PurchasePackageController::class, 'purchasePackageExportCsv'])->name('purchase.package.export.csv');
        Route::post('/purchase/package/delete-multiple', [PurchasePackageController::class, 'purchasePackageDeleteMultiple'])->name('purchase.package.delete.multiple');


        Route::get('/listing/category', [ListingCategoryController::class, 'listingCategory'])->name('listing.category');
        Route::get('/listing/category/search', [ListingCategoryController::class, 'listingCategorySearch'])->name('listing.category.search');
        Route::get('/listing/category/create', [ListingCategoryController::class, 'listingCategoryCreate'])->name('listing.category.create');
        Route::post('/listing/category/store/{language?}', [ListingCategoryController::class, 'listingCategoryStore'])->name('listing.category.store');
        Route::get('/listing/category/edit/{id}', [ListingCategoryController::class, 'listingCategoryEdit'])->name('listing.category.edit');
        Route::put('/listing/category/update/{id}/{language?}', [ListingCategoryController::class, 'listingCategoryUpdate'])->name('listing.category.update');
        Route::delete('/listing/category/delete/{id}', [ListingCategoryController::class, 'listingCategoryDelete'])->name('listing.category.delete');
        Route::post('/listing/category/delete-multiple', [ListingCategoryController::class, 'listingCategoryDeleteMultiple'])->name('listing.category.delete.multiple');


        Route::get('/listings', [ListingController::class, 'listings'])->name('listings');
        Route::get('/listings-search', [ListingController::class, 'listingSearch'])->name('listing.search');
        Route::get('/listing/edit/{id}', [ListingController::class, 'listingEdit'])->name('listing.edit');
        Route::post('/listing/update/{id}', [ListingController::class, 'listingUpdate'])->name('listing.update');

        Route::post('/single-listing-approved', [ListingController::class, 'singleListingApproved'])->name('single.listing.approved');
        Route::post('/multi-listing-approved', [ListingController::class, 'multiListingApproved'])->name('multi.listing.approved');
        Route::post('/single-listing-rejected', [ListingController::class, 'singleListingRejected'])->name('single.listing.rejected');
        Route::post('/multi-listing-rejected', [ListingController::class, 'multiListingRejected'])->name('multi.listing.rejected');
        Route::post('/single-listing-active', [ListingController::class, 'singleListingActive'])->name('single.listing.active');
        Route::post('/single-listing-deactive', [ListingController::class, 'singleListingDeactive'])->name('single.listing.deactive');
        Route::delete('/listing/delete/{id}', [ListingController::class, 'listingDelete'])->name('listing.delete');
        Route::post('/listing/delete-multiple', [ListingController::class, 'listingDeleteMultiple'])->name('listing.delete.multiple');

        Route::get('/listing-reviews/{id?}', [ListingController::class, 'listingReviews'])->name('listing.reviews');
        Route::get('/listing-reviews-search/{id?}', [ListingController::class, 'listingReviewsSearch'])->name('listing.reviews.search');
        Route::delete('/listing-reviews/delete/{id}', [ListingController::class, 'listingReviewsDelete'])->name('listing.reviews.delete');
        Route::post('/listing-reviews/delete-multiple', [ListingController::class, 'listingReviewsDeleteMultiple'])->name('listing.reviews.delete.multiple');

        Route::get('/listing-analytic/{id}', [ListingController::class, 'listingAnalytics'])->name('listing.single.analytics');
        Route::get('/listing-analytic/search/{id?}', [ListingController::class, 'listingAnalyticsSearch'])->name('listing.single.analytics.search');

        Route::get('/listing-form-data/{id?}', [ListingController::class, 'listingFormData'])->name('listing.form.data');
        Route::get('/listing-form-data/search/{id?}', [ListingController::class, 'listingFormDataSearch'])->name('listing.form.data.search');
        Route::post('/listing-form-data/details', [ListingController::class, 'listingFormDataDetails'])->name('listing.form.data.details');
        Route::delete('/listing-form-data/delete/{id}', [ListingController::class, 'listingFormDataDelete'])->name('listing.form.data.delete');
        Route::post('/listing-form-data/delete-multiple', [ListingController::class, 'listingFormDataDeleteMultiple'])->name('listing.form.data.delete.multiple');

        Route::get('/listing-settings', [ListingController::class, 'listingSettings'])->name('listing.setting');
        Route::post('/listing-settings-update', [ListingController::class, 'listingSettingsUpdate'])->name('listing.setting.update');


        Route::get('/wish-list', [FavouriteController::class, 'wishList'])->name('wishList');
        Route::get('/wish-list/search', [FavouriteController::class, 'wishListSearch'])->name('wishList.search');
        Route::delete('/wish-list/delete/{id}', [FavouriteController::class, 'wishListDelete'])->name('wishList.delete');
        Route::post('/wish-list/delete-multiple', [FavouriteController::class, 'wishListDeleteMultiple'])->name('wishList.delete.multiple');

        Route::get('/listing-analytics', [AnalyticsController::class, 'listingAnalytics'])->name('listing.analytics');
        Route::get('/listing-analytics/search', [AnalyticsController::class, 'listingAnalyticsSearch'])->name('listing.analytics.search');
        Route::post('/listing-analytics/show', [AnalyticsController::class, 'listingAnalyticsShow'])->name('listing.analytics.show');
        Route::delete('/listing-analytics/delete/{id}', [AnalyticsController::class, 'listingAnalyticsDelete'])->name('listing.analytics.delete');
        Route::post('/listing-analytics/delete-multiple', [ListingController::class, 'listingAnalyticsDeleteMultiple'])->name('listing.analytics.delete.multiple');


        Route::get('/amenities', [AmenitiesController::class, 'amenities'])->name('amenities');
        Route::get('/amenities-search', [AmenitiesController::class, 'amenitiesSearch'])->name('amenities.search');
        Route::get('/amenities/create', [AmenitiesController::class, 'amenitiesCreate'])->name('amenities.create');
        Route::post('/amenities/store/{language?}', [AmenitiesController::class, 'amenitiesStore'])->name('amenities.store');
        Route::get('/amenities/edit/{id}', [AmenitiesController::class, 'amenitiesEdit'])->name('amenities.edit');
        Route::put('/amenities/update/{id}/{language?}', [AmenitiesController::class, 'amenitiesUpdate'])->name('amenities.update');
        Route::delete('/amenities/delete/{id}', [AmenitiesController::class, 'amenitiesDelete'])->name('amenities.delete');
        Route::post('/amenities/delete-multiple', [AmenitiesController::class, 'amenitiesDeleteMultiple'])->name('amenities.delete.multiple');

        // Manage Country
        Route::get('/all-country', [CountryController::class, 'list'])->name('all.country');
        Route::get('/country-list', [CountryController::class, 'countryList'])->name('country.list');
        Route::get('/country-add', [CountryController::class, 'countryAdd'])->name('country.add');
        Route::post('/country-store', [CountryController::class, 'countryStore'])->name('country.store');
        Route::get('/country/{id}/edit', [CountryController::class, 'countryEdit'])->name('country.edit');
        Route::post('/country/{id}/update', [CountryController::class, 'countryUpdate'])->name('country.update');
        Route::get('/country/{id}/delete', [CountryController::class, 'countryDelete'])->name('country.delete');
        Route::post('/country-delete-multiple', [CountryController::class, 'deleteMultiple'])->name('country.delete.multiple');

        // Manage States of Country
        Route::get('/country/{id}/all-states', [CountryController::class, 'statelist'])->name('country.all.state');
        Route::get('/country/{id}/state-list', [CountryController::class, 'countryStateList'])->name('country.state.list');
        Route::get('/country/{country}/add-state', [CountryController::class, 'countryAddState'])->name('country.add.state');
        Route::post('/country/store-state', [CountryController::class, 'countryStateStore'])->name('country.state.store');
        Route::get('/country/{country}/state/{state}/edit', [CountryController::class, 'countryStateEdit'])->name('country.state.edit');
        Route::post('/country/{country}/state/{state}/update', [CountryController::class, 'countryStateUpdate'])->name('country.state.update');
        Route::get('/country/{country}/state/{state}/delete', [CountryController::class, 'countryStateDelete'])->name('country.state.delete');
        Route::post('/country-state-delete-multiple', [CountryController::class, 'deleteMultipleState'])->name('country.delete.multiple.state');

        // Manage cities of State
        Route::get('/country/{country}/state/{state}/all-cities', [CountryController::class, 'citylist'])->name('country.state.all.city');
        Route::get('/country/{country}/state/{state}/city-list', [CountryController::class, 'countryStateCityList'])->name('country.state.city.list');
        Route::get('/country/{country}/state/{state}/add-city', [CountryController::class, 'countryStateAddCity'])->name('country.state.add.city');
        Route::post('/country/state/store-city', [CountryController::class, 'countryStateStoreCity'])->name('country.state.store.city');
        Route::get('/country/{country}/state/{state}/city/{city}/edit', [CountryController::class, 'countryStateCityEdit'])->name('country.state.city.edit');
        Route::post('/country/{country}/state/{state}/city/{city}/update', [CountryController::class, 'countryStateCityUpdate'])->name('country.state.city.update');
        Route::get('/country/{country}/state/{state}/city/{city}/delete', [CountryController::class, 'countryStateCityDelete'])->name('country.state.city.delete');
        Route::post('/country-state-delete-city-multiple', [CountryController::class, 'deleteMultipleStateCity'])->name('country.delete.multiple.state.city');


        Route::get('/claim-business', [ClaimBusinessController::class, 'claimBusiness'])->name('claim.business');
        Route::get('/claim-business/search', [ClaimBusinessController::class, 'claimBusinessSearch'])->name('claim.business.search');
        Route::post('/claim-business/start-chat-option/{uuid}', [ClaimBusinessController::class, 'claimBusinessStartChatOption'])->name('claim.business.start.chat');
        Route::post('/claim-business/enable-or-disable-chat-option/{uuid}', [ClaimBusinessController::class, 'claimBusinessEnableChatOption'])->name('claim.business.enable.chat.status');
        Route::get('/claim-business/conversation/{uuid}', [ClaimBusinessController::class, 'claimBusinessConversation'])->name('claim.business.conversation');
        Route::get('/claim-business/push-chat-show/{uuId}', [ChatNotificationController::class, 'claimBusinessConversationShowByAdmin'])->name('claim.business.conversation.push.chat.show');
        Route::post('/claim-business/push-chat-new-message', [ChatNotificationController::class, 'claimBusinessConversationNewMessageByAdmin'])->name('claim.business.conversation.push.chat.new.message');
        Route::post('/claim-business//chat/stage/change}', [ClaimBusinessController::class, 'claimBusinessChatStageChange'])->name('claim.chat.stage.change');
        Route::post('/claim-business/delete-multiple', [ClaimBusinessController::class, 'claimDeleteMultiple'])->name('claim.business.delete.multiple');

        Route::post('/claim-business/approve/{id}', [ClaimBusinessController::class, 'claimBusinessApprove'])->name('claim.business.approve');
        Route::post('/claim-business/reject/{id}', [ClaimBusinessController::class, 'claimBusinessReject'])->name('claim.business.reject');

        Route::get('/contact-message', [ContactMessageController::class, 'contactMessage'])->name('contact.message');
        Route::get('/contact-message/search', [ContactMessageController::class, 'contactMessageSearch'])->name('contact.message.search');
        Route::post('/contact-message/delete-multiple', [ContactMessageController::class, 'contactMessageDeleteMultiple'])->name('contact.message.delete.multiple');

        Route::get('/subscriber', [SubscriberController::class, 'subscriber'])->name('subscriber');
        Route::get('/subscriber/search', [SubscriberController::class, 'subscriberSerach'])->name('subscriber.search');
        Route::post('/subscriber/delete-multiple', [SubscriberController::class, 'subscriberDeleteMultiple'])->name('subscriber.delete.multiple');
        Route::get('/subscriber/send-email', [SubscriberController::class, 'subscriberSendEmailForm'])->name('subscriber.send.email.form');
        Route::post('/subscriber/send-email', [SubscriberController::class, 'subscriberSendEmail'])->name('subscriber.send.email');


        /*== Control Panel ==*/
        Route::get('settings/{settings?}', [BasicControlController::class, 'index'])->name('settings');
        Route::get('basic-control', [BasicControlController::class, 'basicControl'])->name('basic.control');
        Route::post('basic-control-update', [BasicControlController::class, 'basicControlUpdate'])->name('basic.control.update');
        Route::post('basic-control-activity-update', [BasicControlController::class, 'basicControlActivityUpdate'])->name('basic.control.activity.update');
        Route::get('currency-exchange-api-config', [BasicControlController::class, 'currencyExchangeApiConfig'])->name('currency.exchange.api.config');
        Route::post('currency-exchange-api-config/update', [BasicControlController::class, 'currencyExchangeApiConfigUpdate'])->name('currency.exchange.api.config.update');

        /* ===== ADMIN SOCIALITE ===== */
        Route::get('socialite', [SocialiteController::class, 'index'])->name('socialite.index');
        Route::match(['get', 'post'], 'google-config', [SocialiteController::class, 'googleConfig'])->name('google.control');
        Route::match(['get', 'post'], 'facebook-config', [SocialiteController::class, 'facebookConfig'])->name('facebook.control');
        Route::match(['get', 'post'], 'github-config', [SocialiteController::class, 'githubConfig'])->name('github.control');

        // manage theme
        Route::get('/manage/theme', [ManageThemeController::class, 'manageTheme'])->name('manage.theme');
        Route::get('/manage/theme/select/{val}', [ManageThemeController::class, 'manageThemeSelect'])->name('manage.theme.select');

        // cookies manage
        Route::get('/gdpr-cookie', [GdprCookieController::class, 'gdprCookie'])->name('gdpr.cookie');
        Route::post('/gdpr-cookie-update', [GdprCookieController::class, 'gdprCookieUpdate'])->name('gdpr.cookie.update');

        /* ===== STORAGE ===== */
        Route::get('storage', [StorageController::class, 'index'])->name('storage.index');
        Route::any('storage/edit/{id}', [StorageController::class, 'edit'])->name('storage.edit');
        Route::any('storage/update/{id}', [StorageController::class, 'update'])->name('storage.update');
        Route::post('storage/set-default/{id}', [StorageController::class, 'setDefault'])->name('storage.setDefault');

        /* ===== Maintenance Mode ===== */
        Route::get('maintenance-mode', [MaintenanceModeController::class, 'index'])->name('maintenance.index');
        Route::post('maintenance-mode/update', [MaintenanceModeController::class, 'maintenanceModeUpdate'])->name('maintenance.mode.update');

        /* ===== LOGO, FAVICON UPDATE ===== */
        Route::get('logo-setting', [LogoController::class, 'logoSetting'])->name('logo.settings');
        Route::post('logo-update', [LogoController::class, 'logoUpdate'])->name('logo.update');


        /* ===== FIREBASE CONFIG ===== */
        Route::get('firebase-config', [FirebaseConfigController::class, 'firebaseConfig'])->name('firebase.config');
        Route::post('firebase-config-update', [FirebaseConfigController::class, 'firebaseConfigUpdate'])->name('firebase.config.update');
        Route::post('firebase-config-/file-upload', [FirebaseConfigController::class, 'firebaseConfigFileUpload'])->name('firebase.config.file.upload');
        Route::get('firebase-config-/file-download', [FirebaseConfigController::class, 'firebaseConfigFileDownload'])->name('firebase.config.file.download');

        /* ===== PUSHER CONFIG ===== */
        Route::get('pusher-config', [PusherConfigController::class, 'pusherConfig'])->name('pusher.config');
        Route::post('pusher-config-update', [PusherConfigController::class, 'pusherConfigUpdate'])->name('pusher.config.update');

        /* ===== EMAIL CONFIG ===== */
        Route::get('email-controls', [EmailConfigController::class, 'emailControls'])->name('email.control');
        Route::get('email-config/edit/{method}', [EmailConfigController::class, 'emailConfigEdit'])->name('email.config.edit');
        Route::post('email-config/update/{method}', [EmailConfigController::class, 'emailConfigUpdate'])->name('email.config.update');
        Route::post('email-config/set-as-default/{method}', [EmailConfigController::class, 'emailSetAsDefault'])->name('email.set.default');
        Route::post('test.email', [EmailConfigController::class, 'testEmail'])->name('test.email');


        /* Notification Templates Routes */
        Route::match(['get', 'post'], 'default-template', [NotificationTemplateController::class, 'defaultTemplate'])->name('email.template.default');
        Route::get('email-templates', [NotificationTemplateController::class, 'emailTemplates'])->name('email.templates');
        Route::get('email-template/edit/{id}', [NotificationTemplateController::class, 'editEmailTemplate'])->name('email.template.edit');
        Route::put('email-template/{id?}/{language_id}', [NotificationTemplateController::class, 'updateEmailTemplate'])->name('email.template.update');

        Route::get('sms-templates', [NotificationTemplateController::class, 'smsTemplates'])->name('sms.templates');
        Route::get('sms-template/edit/{id}', [NotificationTemplateController::class, 'editSmsTemplate'])->name('sms.template.edit');
        Route::put('sms-template/{id?}/{language_id}', [NotificationTemplateController::class, 'updateSmsTemplate'])->name('sms.template.update');

        Route::get('in-app-notification-templates', [NotificationTemplateController::class, 'inAppNotificationTemplates'])
            ->name('in.app.notification.templates');
        Route::get('in-app-notification-template/edit/{id}', [NotificationTemplateController::class, 'editInAppNotificationTemplate'])
            ->name('in.app.notification.template.edit');
        Route::put('in-app-notification-template/{id?}/{language_id}', [NotificationTemplateController::class, 'updateInAppNotificationTemplate'])
            ->name('in.app.notification.template.update');
        Route::get('push-notification-templates', [NotificationTemplateController::class, 'pushNotificationTemplates'])->name('push.notification.templates');
        Route::get('push-notification-template/edit/{id}', [NotificationTemplateController::class, 'editPushNotificationTemplate'])->name('push.notification.template.edit');
        Route::put('push-notification-template/{id?}/{language_id}', [NotificationTemplateController::class, 'updatePushNotificationTemplate'])->name('push.notification.template.update');


        /* ===== EMAIL CONFIG ===== */
        Route::get('sms-configuration', [SmsConfigController::class, 'index'])->name('sms.controls');
        Route::get('sms-config-edit/{method}', [SmsConfigController::class, 'smsConfigEdit'])->name('sms.config.edit');
        Route::post('sms-config-update/{method}', [SmsConfigController::class, 'smsConfigUpdate'])->name('sms.config.update');
        Route::post('sms-method-update/{method}', [SmsConfigController::class, 'manualSmsMethodUpdate'])->name('manual.sms.method.update');
        Route::post('sms-config/set-as-default/{method}', [SmsConfigController::class, 'smsSetAsDefault'])->name('sms.set.default');

        /* ===== PLUGIN CONFIG ===== */
        Route::get('plugin', [PluginController::class, 'pluginConfig'])->name('plugin.config');
        Route::get('plugin/tawk', [PluginController::class, 'tawkConfiguration'])->name('tawk.configuration');
        Route::post('plugin/tawk/Configuration/update', [PluginController::class, 'tawkConfigurationUpdate'])->name('tawk.configuration.update');
        Route::get('plugin/fb-messenger-configuration', [PluginController::class, 'fbMessengerConfiguration'])->name('fb.messenger.configuration');
        Route::post('plugin/fb-messenger-configuration/update', [PluginController::class, 'fbMessengerConfigurationUpdate'])->name('fb.messenger.configuration.update');
        Route::get('plugin/google-recaptcha', [PluginController::class, 'googleRecaptchaConfiguration'])->name('google.recaptcha.configuration');
        Route::post('plugin/google-recaptcha/update', [PluginController::class, 'googleRecaptchaConfigurationUpdate'])->name('google.recaptcha.Configuration.update');
        Route::get('plugin/google-analytics', [PluginController::class, 'googleAnalyticsConfiguration'])->name('google.analytics.configuration');
        Route::post('plugin/google-analytics', [PluginController::class, 'googleAnalyticsConfigurationUpdate'])->name('google.analytics.configuration.update');
        Route::get('plugin/manual-recaptcha', [PluginController::class, 'manualRecaptcha'])->name('manual.recaptcha');
        Route::post('plugin/manual-recaptcha/update', [PluginController::class, 'manualRecaptchaUpdate'])->name('manual.recaptcha.update');
        Route::post('plugin/active-recaptcha', [PluginController::class, 'activeRecaptcha'])->name('active.recaptcha');

        /*Map setting*/
        Route::get('map-config', [MapController::class, 'mapConfig'])->name('map.config');
        Route::get('map-config/update/{mapType?}', [MapController::class, 'mapConfigUpdate'])->name('map.config.update');

        /* ===== ADMIN GOOGLE API SETTING ===== */
        Route::get('translate-api-setting', [TranslateAPISettingController::class, 'translateAPISetting'])->name('translate.api.setting');
        Route::get('translate-api-config/edit/{method}', [TranslateAPISettingController::class, 'translateAPISettingEdit'])->name('translate.api.config.edit');
        Route::post('translate-api-setting/update/{method}', [TranslateAPISettingController::class, 'translateAPISettingUpdate'])->name('translate.api.setting.update');
        Route::post('translate-api-setting/set-as-default/{method}', [TranslateAPISettingController::class, 'translateSetAsDefault'])->name('translate.set.default');


        /* ===== ADMIN LANGUAGE SETTINGS ===== */
        Route::get('languages', [LanguageController::class, 'index'])->name('language.index');
        Route::get('language/create', [LanguageController::class, 'create'])->name('language.create');
        Route::post('language/store', [LanguageController::class, 'store'])->name('language.store');
        Route::get('language/edit/{id}', [LanguageController::class, 'edit'])->name('language.edit');
        Route::put('language/update/{id}', [LanguageController::class, 'update'])->name('language.update');
        Route::delete('language-delete/{id}', [LanguageController::class, 'destroy'])->name('language.delete');
        Route::put('change-language-status/{id}', [LanguageController::class, 'changeStatus'])->name('change.language.status');


        Route::get('{short_name}/keywords', [LanguageController::class, 'keywords'])->name('language.keywords');
        Route::post('language-keyword/{short_name}', [LanguageController::class, 'addKeyword'])->name('add.language.keyword');
        Route::put('language-keyword/{short_name}/{key}', [LanguageController::class, 'updateKeyword'])->name('update.language.keyword');
        Route::delete('language-keyword/{short_name}/{key}', [LanguageController::class, 'deleteKeyword'])->name('delete.language.keyword');
        Route::post('language-import-json', [LanguageController::class, 'importJson'])->name('language.import.json');
        Route::put('update-key/{language}', [LanguageController::class, 'updateKey'])->name('language.update.key');
        Route::post('language/keyword/translate', [LanguageController::class, 'singleKeywordTranslate'])->name('single.keyword.translate');
        Route::post('language/all-keyword/translate/{shortName}', [LanguageController::class, 'allKeywordTranslate'])->name('all.keyword.translate');


        /* ===== ADMIN SUPPORT TICKET ===== */
        Route::get('tickets/{status?}', [SupportTicketController::class, 'tickets'])->name('ticket');
        Route::get('tickets-search/{status}', [SupportTicketController::class, 'ticketSearch'])->name('ticket.search');
        Route::get('tickets-view/{id}', [SupportTicketController::class, 'ticketView'])->name('ticket.view');
        Route::put('ticket-reply/{id}', [SupportTicketController::class, 'ticketReplySend'])->name('ticket.reply');
        Route::get('ticket-download/{ticket}', [SupportTicketController::class, 'ticketDownload'])->name('ticket.download');
        Route::post('ticket-closed/{id}', [SupportTicketController::class, 'ticketClosed'])->name('ticket.closed');
        Route::post('ticket-delete', [SupportTicketController::class, 'ticketDelete'])->name('ticket.delete');


        /* ===== InAppNotificationController SETTINGS ===== */
        Route::get('push-notification-show', [InAppNotificationController::class, 'showByAdmin'])->name('push.notification.show');
        Route::get('push.notification.readAll', [InAppNotificationController::class, 'readAllByAdmin'])->name('push.notification.readAll');
        Route::get('push-notification-readAt/{id}', [InAppNotificationController::class, 'readAt'])->name('push.notification.readAt');

        /* PAYMENT METHOD MANAGE BY ADMIN*/
        Route::get('payment-methods', [PaymentMethodController::class, 'index'])->name('payment.methods');
        Route::get('edit-payment-methods/{id}', [PaymentMethodController::class, 'edit'])->name('edit.payment.methods');
        Route::put('update-payment-methods/{id}', [PaymentMethodController::class, 'update'])->name('update.payment.methods');
        Route::post('sort-payment-methods', [PaymentMethodController::class, 'sortPaymentMethods'])->name('sort.payment.methods');
        Route::post('payment-methods/deactivate', [PaymentMethodController::class, 'deactivate'])->name('payment.methods.deactivate');


        /*=* MANUAL METHOD MANAGE BY ADMIN *=*/
        Route::get('payment-methods/manual', [ManualGatewayController::class, 'index'])->name('deposit.manual.index');
        Route::get('payment-methods/manual/create', [ManualGatewayController::class, 'create'])->name('deposit.manual.create');
        Route::post('payment-methods/manual/store', [ManualGatewayController::class, 'store'])->name('deposit.manual.store');
        Route::get('payment-methods/manual/edit/{id}', [ManualGatewayController::class, 'edit'])->name('deposit.manual.edit');
        Route::put('payment-methods/manual/update/{id}', [ManualGatewayController::class, 'update'])->name('deposit.manual.update');

        /*= PAYOUT METHOD MANAGE BY ADMIN =*/
        Route::match(['get', 'post'], 'currency-exchange-api-config', [BasicControlController::class, 'currencyExchangeApiConfig'])->name('currency.exchange.api.config');

        /*= MANAGE KYC =*/
        Route::get('kyc-setting/list', [KycController::class, 'index'])->name('kyc.form.list');
        Route::get('kyc-setting/create', [KycController::class, 'create'])->name('kyc.create');
        Route::post('manage-kyc/store', [KycController::class, 'store'])->name('kyc.store');
        Route::get('manage-kyc/edit/{id}', [KycController::class, 'edit'])->name('kyc.edit');
        Route::post('manage-kyc/update/{id}', [KycController::class, 'update'])->name('kyc.update');
        Route::get('kyc/{status?}', [KycController::class, 'userKycList'])->name('kyc.list');
        Route::get('kyc/search/{status?}', [KycController::class, 'userKycSearch'])->name('kyc.search');
        Route::get('kyc/view/{id}', [KycController::class, 'view'])->name('kyc.view');
        Route::post('user/kyc/action/{id}', [KycController::class, 'action'])->name('kyc.action');
        Route::get('user/kyc-search', [KycController::class, 'searchKyc'])->name('userKyc.search');

        /*= Frontend Manage =*/
        Route::get('frontend/pages/{theme}', [PageController::class, 'index'])->name('page.index');
        Route::get('frontend/create-page/{theme}', [PageController::class, 'create'])->name('create.page');
        Route::post('frontend/create-page/store/{theme}', [PageController::class, 'store'])->name('create.page.store');
        Route::get('frontend/edit-page/{id}/{theme}/{language?}', [PageController::class, 'edit'])->name('edit.page');
        Route::post('frontend/update-page/{id}/{theme}', [PageController::class, 'update'])->name('update.page');
        Route::post('frontend/page/update-slug', [PageController::class, 'updateSlug'])->name('update.slug');
        Route::delete('frontend/page/delete/{id}', [PageController::class, 'delete'])->name('page.delete');

        Route::get('frontend/edit-static-page/{id}/{theme}/{language?}', [PageController::class, 'editStaticPage'])->name('edit.static.page');
        Route::post('frontend/update-static-page/{id}/{theme}', [PageController::class, 'updateStaticPage'])->name('update.static.page');

        Route::get('frontend/page/seo/{id}', [PageController::class, 'pageSEO'])->name('page.seo');
        Route::post('frontend/page/seo/update/{id}', [PageController::class, 'pageSeoUpdate'])->name('page.seo.update');

        Route::get('frontend/manage-menu', [ManageMenuController::class, 'manageMenu'])->name('manage.menu');
        Route::post('frontend/header-menu-item/store', [ManageMenuController::class, 'headerMenuItemStore'])->name('header.menu.item.store');
        Route::post('frontend/footer-menu-item/store', [ManageMenuController::class, 'footerMenuItemStore'])->name('footer.menu.item.store');
        Route::post('frontend/manage-menu/add-custom-link', [ManageMenuController::class, 'addCustomLink'])->name('add.custom.link');
        Route::get('frontend/manage-menu/edit-custom-link/{pageId}', [ManageMenuController::class, 'editCustomLink'])->name('edit.custom.link');
        Route::post('frontend/manage-menu/update-custom-link/{pageId}', [ManageMenuController::class, 'updateCustomLink'])->name('update.custom.link');
        Route::delete('frontend/manage-menu/delete-custom-link/{pageId}', [ManageMenuController::class, 'deleteCustomLink'])->name('delete.custom.link');
        Route::get('frontend/manage-menu/get-custom-link-data', [ManageMenuController::class, 'getCustomLinkData'])->name('get.custom.link');

        Route::get('frontend/contents/{name}', [ContentController::class, 'index'])->name('manage.content');
        Route::post('frontend/contents/store/{name}/{language}', [ContentController::class, 'store'])->name('content.store');
        Route::get('frontend/contents/item/{name}', [ContentController::class, 'manageContentMultiple'])->name('manage.content.multiple');
        Route::post('frontend/contents/item/store/{name}/{language}', [ContentController::class, 'manageContentMultipleStore'])->name('content.multiple.store');
        Route::get('frontend/contents/item/edit/{name}/{id}', [ContentController::class, 'multipleContentItemEdit'])->name('content.item.edit');
        Route::post('frontend/contents/item/update/{name}/{id}/{language}', [ContentController::class, 'multipleContentItemUpdate'])->name('multiple.content.item.update');
        Route::delete('frontend/contents/delete/{id}', [ContentController::class, 'ContentDelete'])->name('content.item.delete');

        /*====Manage Users ====*/
        Route::get('login/as/user/{id}', [UsersController::class, 'loginAsUser'])->name('login.as.user');
        Route::post('block-profile/{id}', [UsersController::class, 'blockProfile'])->name('block.profile');
        Route::get('users/{state?}', [UsersController::class, 'index'])->name('users');
        Route::get('user/edit/{id}', [UsersController::class, 'userEdit'])->name('user.edit');
        Route::get('users/search/{state?}', [UsersController::class, 'search'])->name('users.search');

        Route::post('users-delete-multiple', [UsersController::class, 'deleteMultiple'])->name('user.delete.multiple');
        Route::post('user/update/{id}', [UsersController::class, 'userUpdate'])->name('user.update');
        Route::post('user/email/{id}', [UsersController::class, 'EmailUpdate'])->name('user.email.update');
        Route::post('user/username/{id}', [UsersController::class, 'usernameUpdate'])->name('user.username.update');
        Route::post('user/password/{id}', [UsersController::class, 'passwordUpdate'])->name('user.password.update');
        Route::post('user/preferences/{id}', [UsersController::class, 'preferencesUpdate'])->name('user.preferences.update');
        Route::post('user/two-fa-security/{id}', [UsersController::class, 'userTwoFaUpdate'])->name('user.twoFa.update');
        Route::post('user/balance-update/{id}', [UsersController::class, 'userBalanceUpdate'])->name('user-balance-update');

        Route::get('user/send-email/{id}', [UsersController::class, 'sendEmail'])->name('send.email');
        Route::post('user/send-email/{id?}', [UsersController::class, 'sendMailUser'])->name('user.email.send');
        Route::get('mail-all-user', [UsersController::class, 'mailAllUser'])->name('mail.all.user');

        Route::get('user/kyc/{id}', [UsersController::class, 'userKyc'])->name('user.kyc.list');
        Route::get('user/kyc/search/{id}', [UsersController::class, 'KycSearch'])->name('user.kyc.search');

        Route::get('user/transaction/{id}', [UsersController::class, 'transaction'])->name('user.transaction');
        Route::get('user/transaction/search/{id}', [UsersController::class, 'userTransactionSearch'])->name('user.transaction.search');

        Route::get('user/payment/{id}', [UsersController::class, 'payment'])->name('user.payment');
        Route::get('user/payment/search/{id}', [UsersController::class, 'userPaymentSearch'])->name('user.payment.search');

        Route::get('user/withdraw/{id}', [UsersController::class, 'payout'])->name('user.payout');
        Route::get('user/withdraw/search/{id}', [UsersController::class, 'userPayoutSearch'])->name('user.payout.search');

        Route::get('/email-send', [UsersController::class, 'emailToUsers'])->name('email-send');
        Route::post('/email-send', [UsersController::class, 'sendEmailToUsers'])->name('email-send.store');
        Route::delete('user/delete/{id}', [UsersController::class, 'userDelete'])->name('user.delete');

        Route::get('users/add', [UsersController::class, 'userAdd'])->name('users.add');
        Route::post('users/store', [UsersController::class, 'userStore'])->name('user.store');
        Route::get('users/added-successfully/{id}', [UsersController::class, 'userCreateSuccessMessage'])
            ->name('user.create.success.message');
        Route::get('user/view-profile/{id}', [UsersController::class, 'userViewProfile'])->name('user.view.profile');


        /* ====== Transaction Log =====*/
        Route::get('transaction', [TransactionLogController::class, 'transaction'])->name('transaction');
        Route::get('transaction/search', [TransactionLogController::class, 'transactionSearch'])->name('transaction.search');

        /* ====== Payment Log =====*/
        Route::get('payment/log', [PaymentLogController::class, 'index'])->name('payment.log');
        Route::get('payment/search', [PaymentLogController::class, 'search'])->name('payment.search');
        Route::get('payment/pending', [PaymentLogController::class, 'pending'])->name('payment.pending');
        Route::get('payment/pending/request', [PaymentLogController::class, 'paymentRequest'])->name('payment.request');
        Route::put('payment/action/{id}', [PaymentLogController::class, 'action'])->name('payment.action');


        /* ====== Blog Category Controller =====*/
        Route::resource('blog-category', BlogCategoryController::class);
        Route::resource('blogs', BlogController::class);
        Route::get('blogs/edit/{id}/{language}', [BlogController::class, 'blogEdit'])->name('blog.edit');
        Route::post('blogs/update/{id}/{language}', [BlogController::class, 'blogUpdate'])->name('blog.update');
        Route::post('blogs/slug/update', [BlogController::class, 'slugUpdate'])->name('slug.update');
        Route::get('blogs/seo-page/{id}', [BlogController::class, 'blogSeo'])->name('blog.seo');
        Route::post('blogs/seo-update/{id}', [BlogController::class, 'blogSeoUpdate'])->name('blog.seo.update');
    });


});


