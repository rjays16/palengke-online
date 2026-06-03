<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SellerRegistrationController;
use App\Http\Controllers\Api\AdminNotificationController;
use App\Http\Controllers\Api\SellerDeviceController;
use App\Http\Controllers\Api\ContactSupportController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\FeedbackController;

Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/admin/me', [AdminAuthController::class, 'me'])->middleware('auth:sanctum');

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
});

// Seller app OTP verification
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

// Seller app registration
Route::post('/seller/register', [SellerRegistrationController::class, 'register']);

// Seller app login
Route::post('/seller/login', [AuthController::class, 'login']);

// Seller device PIN management
Route::post('/seller/device/register', [SellerDeviceController::class, 'register']);
Route::post('/seller/device/verify-pin', [SellerDeviceController::class, 'verifyPin']);
Route::post('/seller/device/status', [SellerDeviceController::class, 'status']);
Route::post('/seller/devices', [SellerDeviceController::class, 'list']);
Route::post('/seller/devices/remove', [SellerDeviceController::class, 'remove']);
Route::post('/seller/devices/logout-current', [SellerDeviceController::class, 'logoutCurrent']);

// Seller app — submit a report
Route::post('/reports', [ReportController::class, 'store']);
// Seller app — view own reports
Route::get('/reports', [ReportController::class, 'myReports']);
// Seller app — submit feedback
Route::post('/feedbacks', [FeedbackController::class, 'store']);

// Public endpoint for seller app to fetch enabled contact supports
Route::get('/contact-supports', [ContactSupportController::class, 'index']);

// Public endpoint for seller app to fetch enabled countries
Route::get('/countries', [CountryController::class, 'index']);

// Public endpoint for seller app to fetch enabled locations
Route::get('/locations', [LocationController::class, 'index']);

// Admin — seller registrations (requires auth)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/sellers/pending', [SellerRegistrationController::class, 'pending']);
    Route::get('/admin/sellers/history', [SellerRegistrationController::class, 'history']);
    Route::get('/admin/sellers/{id}', [SellerRegistrationController::class, 'show']);
    Route::post('/admin/sellers/{id}/approve', [SellerRegistrationController::class, 'approve']);
    Route::post('/admin/sellers/{id}/reject', [SellerRegistrationController::class, 'reject']);

    Route::get('/admin/notifications', [AdminNotificationController::class, 'index']);
    Route::get('/admin/notifications/unread-count', [AdminNotificationController::class, 'unreadCount']);
    Route::post('/admin/notifications/{id}/read', [AdminNotificationController::class, 'markRead']);
    Route::post('/admin/notifications/read-all', [AdminNotificationController::class, 'markAllRead']);
    Route::delete('/admin/notifications/{id}', [AdminNotificationController::class, 'destroy']);

    Route::get('/admin/contact-supports', [ContactSupportController::class, 'adminIndex']);
    Route::put('/admin/contact-supports', [ContactSupportController::class, 'update']);

    Route::get('/admin/reports', [ReportController::class, 'adminIndex']);
    Route::put('/admin/reports/{id}', [ReportController::class, 'updateStatus']);

    Route::get('/admin/feedbacks', [FeedbackController::class, 'adminIndex']);
    Route::put('/admin/feedbacks/{id}', [FeedbackController::class, 'updateStatus']);
});
