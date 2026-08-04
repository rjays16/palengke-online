<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CountryRegionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SellerRegistrationController;
use App\Http\Controllers\Api\AdminNotificationController;
use App\Http\Controllers\Api\SellerDeviceController;
use App\Http\Controllers\Api\ContactSupportController;
use App\Http\Controllers\Api\HelpTopicController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\SellerPaymentMethodController;
use App\Http\Controllers\Api\SystemSettingController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NoticeController;
use App\Http\Controllers\Api\SellerNotificationController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\DriverRatingController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\AdminPaymentMethodController;
use App\Http\Controllers\Api\AdminSubscriptionPaymentController;
use App\Http\Controllers\Api\AdminWalletController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\AdminSubscriptionController;
use App\Http\Controllers\Api\RestrictionCodeController;
use App\Http\Controllers\Api\LicenseTypeController;
use App\Http\Controllers\Api\DriverRegistrationController;
use App\Http\Controllers\Api\DriverAuthController;
use App\Http\Controllers\Api\DriverDeviceController;
use App\Http\Controllers\Api\DriverDocumentController;
use App\Http\Controllers\Api\LicenceController;
use App\Http\Controllers\Api\DriverBadgeController;
use App\Http\Controllers\Api\DriverPerformanceController;
use App\Http\Controllers\Api\DriverNotificationController;
use App\Http\Controllers\Api\DriverMessageController;
use App\Http\Controllers\Api\MesiboController;
use App\Http\Controllers\Api\CallSignalController;
use App\Http\Controllers\Api\TipController;

Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/admin/me', [AdminAuthController::class, 'me'])->middleware('auth:sanctum');
Route::post('/admin/forgot-password', [AdminAuthController::class, 'forgotPassword']);
Route::post('/admin/reset-password', [AdminAuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index']);
    Route::post('/admin/users', [AdminUserController::class, 'store']);
    Route::put('/admin/users/{id}', [AdminUserController::class, 'update']);
    Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy']);

    Route::get('/admin/locations', [LocationController::class, 'index']);
    Route::put('/admin/locations', [LocationController::class, 'update']);

    Route::get('/admin/countries', [CountryController::class, 'index']);
    Route::post('/admin/countries/{id}/toggle', [CountryController::class, 'toggle']);
    Route::put('/admin/countries/bulk', [CountryController::class, 'bulkUpdate']);

    Route::get('/admin/countries/{country}/regions', [CountryRegionController::class, 'index']);
    Route::post('/admin/countries/{country}/regions/{region}/toggle', [CountryRegionController::class, 'toggle']);
});

// Seller app OTP verification
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

// Seller app registration
Route::post('/seller/register', [SellerRegistrationController::class, 'register']);
Route::post('/seller/verify', [SellerRegistrationController::class, 'verify']);

// Driver app registration
Route::post('/driver/upload-photo', [DriverRegistrationController::class, 'uploadPhoto']);
Route::post('/driver/register', [DriverRegistrationController::class, 'register']);
Route::post('/driver/check-email', [DriverRegistrationController::class, 'checkEmail']);

// Driver documents
Route::get('/driver/documents', [DriverDocumentController::class, 'index']);
Route::get('/driver/documents/types', [DriverDocumentController::class, 'types']);
Route::post('/driver/documents/upload', [DriverDocumentController::class, 'upload']);
Route::post('/driver/documents/seed', [DriverDocumentController::class, 'seed']);

// Driver login
Route::post('/driver/login', [DriverAuthController::class, 'login']);
Route::post('/driver/forgot-pin', [DriverAuthController::class, 'forgotPin']);

// Driver device PIN management
Route::post('/driver/device/register', [DriverDeviceController::class, 'register']);
Route::post('/driver/device/verify-pin', [DriverDeviceController::class, 'verifyPin']);
Route::post('/driver/device/change-pin', [DriverDeviceController::class, 'changePin']);
Route::post('/driver/device/status', [DriverDeviceController::class, 'status']);

// Admin — driver licence
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/drivers/{id}/licence', [LicenceController::class, 'adminShow']);
    Route::post('/admin/drivers/{id}/licence/toggle-free', [LicenceController::class, 'toggleFree']);
});

// Driver settings & licence (authenticated)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/driver/profile', [DriverRegistrationController::class, 'profile']);
    Route::get('/driver/settings', [DriverRegistrationController::class, 'settings']);
    Route::put('/driver/settings', [DriverRegistrationController::class, 'updateSettings']);
    Route::put('/driver/status', [DriverRegistrationController::class, 'updateStatus']);
    Route::get('/driver/licence', [LicenceController::class, 'show']);
    Route::post('/driver/licence/pay', [LicenceController::class, 'pay']);
    Route::get('/driver/badges', [DriverBadgeController::class, 'index']);
    Route::post('/driver/badges/mark-read', [DriverBadgeController::class, 'markRead']);
    Route::get('/driver/performance', [DriverPerformanceController::class, 'show']);
    Route::get('/driver/recent-deliveries', [DriverRegistrationController::class, 'recentDeliveries']);
    Route::get('/driver/history', [DriverRegistrationController::class, 'driverHistory']);
    Route::get('/driver/orders', [DriverRegistrationController::class, 'orders']);
    Route::get('/driver/earnings', [DriverRegistrationController::class, 'earnings']);
    Route::get('/driver/wallet', [DriverRegistrationController::class, 'walletBalance']);
    Route::post('/driver/wallet/cash-out', [DriverRegistrationController::class, 'cashOut']);
    Route::get('/driver/tips/latest', [TipController::class, 'latest']);
    Route::get('/driver/notifications', [DriverNotificationController::class, 'index']);
    Route::get('/driver/notifications/unread-count', [DriverNotificationController::class, 'unreadCount']);
    Route::post('/driver/notifications/read-all', [DriverNotificationController::class, 'markAllRead']);
    Route::post('/driver/notifications/{id}/read', [DriverNotificationController::class, 'markRead']);
    Route::delete('/driver/notifications/{id}', [DriverNotificationController::class, 'destroy']);
    Route::delete('/driver/notifications', [DriverNotificationController::class, 'clearAll']);
    Route::post('/driver/orders/{id}/accept', [DriverRegistrationController::class, 'acceptOrder']);
    Route::post('/driver/orders/{id}/status', [DriverRegistrationController::class, 'updateOrderStatus']);

    // Driver app — messages / conversations
    Route::get('/driver/conversations', [DriverMessageController::class, 'index']);
    Route::get('/driver/conversations/{id}', [DriverMessageController::class, 'show']);
    Route::post('/driver/conversations/{id}/messages', [DriverMessageController::class, 'sendMessage']);
    Route::post('/driver/conversations/{id}/upload-image', [DriverMessageController::class, 'uploadImage']);
    Route::post('/driver/conversations/{id}/read', [DriverMessageController::class, 'markRead']);
    Route::delete('/driver/conversations/{id}', [DriverMessageController::class, 'destroy']);
    Route::delete('/driver/messages/{id}', [DriverMessageController::class, 'destroyMessage']);
});

// Seller app login
Route::post('/seller/login', [AuthController::class, 'login']);
Route::post('/seller/forgot-pin', [AuthController::class, 'forgotPin']);
Route::get('/seller/profile', [AuthController::class, 'profile']);
Route::post('/seller/profile', [AuthController::class, 'updateProfile']);

// Mesibo VoIP — get user access token
Route::post('/mesibo/token', [MesiboController::class, 'getToken']);

// Call signaling — both seller and driver
Route::post('/calls/signal', [CallSignalController::class, 'start']);
Route::get('/calls/signal', [CallSignalController::class, 'check']);
Route::post('/calls/signal/{id}/accept', [CallSignalController::class, 'accept']);
Route::post('/calls/signal/{id}/decline', [CallSignalController::class, 'decline']);
Route::post('/calls/signal/{id}/end', [CallSignalController::class, 'end']);
Route::get('/calls/signal/{id}/status', [CallSignalController::class, 'callerStatus']);

// Seller device PIN management
Route::post('/seller/device/register', [SellerDeviceController::class, 'register']);
Route::post('/seller/device/verify-pin', [SellerDeviceController::class, 'verifyPin']);
Route::post('/seller/device/change-pin', [SellerDeviceController::class, 'changePin']);
Route::post('/seller/device/status', [SellerDeviceController::class, 'status']);
Route::post('/seller/devices', [SellerDeviceController::class, 'list']);
Route::post('/seller/devices/remove', [SellerDeviceController::class, 'remove']);
Route::post('/seller/devices/logout-current', [SellerDeviceController::class, 'logoutCurrent']);

// Seller app — products
Route::get('/seller/products', [ProductController::class, 'index']);
Route::get('/seller/products/{id}', [ProductController::class, 'show']);
Route::post('/seller/products', [ProductController::class, 'store']);
Route::post('/seller/products/upload-image', [ProductController::class, 'uploadImage']);
Route::post('/seller/products/{id}', [ProductController::class, 'update']);
Route::delete('/seller/products/{id}', [ProductController::class, 'destroy']);

// Seller app — orders
Route::get('/seller/orders', [OrderController::class, 'index']);
Route::get('/seller/orders/{id}', [OrderController::class, 'show']);
Route::post('/seller/orders/{id}/status', [OrderController::class, 'updateStatus']);

// Seller app — messages / conversations
Route::get('/seller/conversations', [MessageController::class, 'index']);
Route::get('/seller/conversations/{id}', [MessageController::class, 'show']);
Route::post('/seller/conversations/{id}/messages', [MessageController::class, 'sendMessage']);
Route::post('/seller/conversations/{id}/upload-image', [MessageController::class, 'uploadImage']);
Route::post('/seller/conversations/{id}/read', [MessageController::class, 'markRead']);
Route::post('/seller/conversations/{id}/toggle-mute', [MessageController::class, 'toggleMute']);
Route::delete('/seller/conversations/{id}', [MessageController::class, 'destroy']);
Route::delete('/seller/messages/{id}', [MessageController::class, 'destroyMessage']);

// Seller app — dashboard stats
Route::get('/seller/dashboard/stats', [DashboardController::class, 'stats']);

// Seller app — analytics
Route::get('/seller/analytics', [AnalyticsController::class, 'index']);

// Seller app — update monthly goals
Route::post('/seller/goals', [AnalyticsController::class, 'updateGoals']);

// Seller app — subscription / trial
Route::get('/seller/subscription', [SubscriptionController::class, 'index']);
Route::post('/seller/subscription/subscribe', [SubscriptionController::class, 'subscribe']);

// Public — payment methods for seller app
Route::get('/payment-methods', [PaymentMethodController::class, 'index']);

// Seller app — wallet
Route::get('/seller/wallet/balance', [WalletController::class, 'balance']);
Route::get('/seller/wallet/transactions', [WalletController::class, 'transactions']);
Route::post('/seller/wallet/top-up', [WalletController::class, 'topUp']);

// Seller app — notifications
Route::get('/seller/notifications', [SellerNotificationController::class, 'index']);
Route::post('/seller/notifications', [SellerNotificationController::class, 'store']);
Route::delete('/seller/notifications', [SellerNotificationController::class, 'clearAll']);
Route::post('/seller/notifications/read-all', [SellerNotificationController::class, 'markAllRead']);
Route::post('/seller/notifications/{id}/read', [SellerNotificationController::class, 'markRead']);
Route::delete('/seller/notifications/{id}', [SellerNotificationController::class, 'destroy']);

// Seller app — payment methods
Route::get('/seller/payment-methods', [SellerPaymentMethodController::class, 'index']);
Route::post('/seller/payment-methods', [SellerPaymentMethodController::class, 'store']);
Route::delete('/seller/payment-methods/{id}', [SellerPaymentMethodController::class, 'destroy']);
Route::post('/seller/payment-methods/{id}/default', [SellerPaymentMethodController::class, 'setDefault']);

// Seller app — submit a report
Route::post('/reports', [ReportController::class, 'store']);
// Seller app — view own reports
Route::get('/reports', [ReportController::class, 'myReports']);
// Seller app — submit feedback
Route::post('/feedbacks', [FeedbackController::class, 'store']);

// Public endpoint for seller app to fetch categories
Route::get('/categories', [CategoryController::class, 'index']);

// Public endpoint for seller app to fetch system settings
Route::get('/settings', [SystemSettingController::class, 'index']);

// Public endpoint for seller app to fetch enabled contact supports
Route::get('/contact-supports', [ContactSupportController::class, 'index']);

// Public endpoint for seller app to fetch enabled countries
Route::get('/countries', [CountryController::class, 'index']);

// Public endpoint for driver/seller app to fetch enabled help topics
Route::get('/help-topics', [HelpTopicController::class, 'index']);

// Public endpoint for driver app to fetch enabled restriction codes
Route::get('/restriction-codes', [RestrictionCodeController::class, 'index']);

// Public endpoint for driver app to fetch enabled license types
Route::get('/license-types', [LicenseTypeController::class, 'index']);

// Public endpoint for seller app to fetch enabled locations (filtered by country_regions)
Route::get('/locations', [LocationController::class, 'publicIndex']);

// Admin — categories
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/categories', [CategoryController::class, 'index']);
    Route::post('/admin/categories', [CategoryController::class, 'store']);
    Route::post('/admin/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/admin/categories/{id}', [CategoryController::class, 'destroy']);
});

// Admin — system settings
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/settings', [SystemSettingController::class, 'index']);
    Route::put('/admin/settings', [SystemSettingController::class, 'update']);
});

// Admin — seller registrations (requires auth)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/sellers', [SellerRegistrationController::class, 'index']);
    Route::get('/admin/sellers/pending', [SellerRegistrationController::class, 'pending']);
    Route::get('/admin/sellers/history', [SellerRegistrationController::class, 'history']);
    Route::get('/admin/sellers/{id}', [SellerRegistrationController::class, 'show']);
    Route::get('/admin/sellers/{id}/products', [ProductController::class, 'adminIndex']);
    Route::get('/admin/sellers/{id}/orders', [OrderController::class, 'adminIndex']);
    Route::post('/admin/sellers/{id}/approve', [SellerRegistrationController::class, 'approve']);
    Route::post('/admin/sellers/{id}/reject', [SellerRegistrationController::class, 'reject']);

    // Admin — driver registrations
    Route::get('/admin/drivers', [DriverRegistrationController::class, 'index']);
    Route::get('/admin/drivers/pending', [DriverRegistrationController::class, 'pending']);
    Route::get('/admin/drivers/history', [DriverRegistrationController::class, 'history']);
    Route::get('/admin/drivers/{id}', [DriverRegistrationController::class, 'show']);
    Route::post('/admin/drivers', [DriverRegistrationController::class, 'store']);
    Route::put('/admin/drivers/{id}', [DriverRegistrationController::class, 'update']);
    Route::delete('/admin/drivers/{id}', [DriverRegistrationController::class, 'destroy']);
    Route::get('/admin/drivers/{id}/deliveries', [DriverRegistrationController::class, 'deliveries']);
    Route::post('/admin/drivers/{id}/approve', [DriverRegistrationController::class, 'approve']);
    Route::post('/admin/drivers/{id}/reject', [DriverRegistrationController::class, 'reject']);

    Route::get('/admin/notifications', [AdminNotificationController::class, 'index']);
    Route::get('/admin/notifications/unread-count', [AdminNotificationController::class, 'unreadCount']);
    Route::post('/admin/notifications/{id}/read', [AdminNotificationController::class, 'markRead']);
    Route::post('/admin/notifications/read-all', [AdminNotificationController::class, 'markAllRead']);
    Route::delete('/admin/notifications/{id}', [AdminNotificationController::class, 'destroy']);

    Route::get('/admin/contact-supports', [ContactSupportController::class, 'adminIndex']);
    Route::put('/admin/contact-supports', [ContactSupportController::class, 'update']);

    Route::get('/admin/help-topics', [HelpTopicController::class, 'adminIndex']);
    Route::put('/admin/help-topics', [HelpTopicController::class, 'update']);

    Route::get('/admin/reports', [ReportController::class, 'adminIndex']);
    Route::put('/admin/reports/{id}', [ReportController::class, 'updateStatus']);

    Route::get('/admin/feedbacks', [FeedbackController::class, 'adminIndex']);
    Route::put('/admin/feedbacks/{id}', [FeedbackController::class, 'updateStatus']);

    Route::get('/admin/notices', [NoticeController::class, 'index']);
    Route::post('/admin/notices', [NoticeController::class, 'store']);
    Route::put('/admin/notices/{id}', [NoticeController::class, 'update']);
    Route::delete('/admin/notices/{id}', [NoticeController::class, 'destroy']);

    Route::get('/admin/faqs', [FaqController::class, 'index']);
    Route::post('/admin/faqs', [FaqController::class, 'store']);
    Route::put('/admin/faqs/{id}', [FaqController::class, 'update']);
    Route::delete('/admin/faqs/{id}', [FaqController::class, 'destroy']);

    Route::get('/admin/payment-methods', [AdminPaymentMethodController::class, 'index']);
    Route::post('/admin/payment-methods', [AdminPaymentMethodController::class, 'store']);
    Route::put('/admin/payment-methods/{id}', [AdminPaymentMethodController::class, 'update']);
    Route::delete('/admin/payment-methods/{id}', [AdminPaymentMethodController::class, 'destroy']);

    Route::get('/admin/subscription-payments', [AdminSubscriptionPaymentController::class, 'index']);

    Route::post('/admin/sellers/{id}/wallet/top-up', [AdminWalletController::class, 'topUp']);
    Route::get('/admin/sellers/{id}/wallet/transactions', [AdminWalletController::class, 'transactions']);

    Route::post('/admin/sellers/{id}/subscription/change', [AdminSubscriptionController::class, 'changePlan']);
    Route::post('/admin/sellers/{id}/subscription/extend', [AdminSubscriptionController::class, 'extend']);
    Route::post('/admin/sellers/{id}/subscription/cancel', [AdminSubscriptionController::class, 'cancel']);

    Route::get('/admin/restriction-codes', [RestrictionCodeController::class, 'adminIndex']);
    Route::post('/admin/restriction-codes/{id}/toggle', [RestrictionCodeController::class, 'toggle']);
    Route::put('/admin/restriction-codes/{id}', [RestrictionCodeController::class, 'update']);
});

// Public — fetch active notices for seller / customer / driver apps
Route::get('/notices', [NoticeController::class, 'active']);

// Public — fetch active FAQs for seller app
Route::get('/faqs', [FaqController::class, 'active']);

// Customer app — submit a review / rating
Route::post('/reviews', [ReviewController::class, 'store']);

// Customer app — submit a driver rating
Route::post('/driver-ratings', [DriverRatingController::class, 'store']);

// Customer app — place an order with an optional tip (public)
Route::post('/tips', [TipController::class, 'store']);

// Customer app — get order info for rating page
Route::get('/order-info/{id}', function ($id) {
    $order = \App\Models\Order::with('seller')->find($id);
    if (!$order) {
        return response()->json(['message' => 'Order not found.'], 404);
    }
    return response()->json([
        'storeName' => $order->seller->store_name ?? $order->seller->first_name ?? 'Store',
        'storeImage' => $order->seller->avatar ?? null,
        'items' => collect($order->items)->pluck('name')->implode(', '),
        'total' => (float) $order->total,
        'customer_name' => $order->customer_name,
    ]);
});
