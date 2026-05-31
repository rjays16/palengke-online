<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CountryController;

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
Route::post('/send-otp', [\App\Http\Controllers\Api\AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [\App\Http\Controllers\Api\AuthController::class, 'verifyOtp']);

// Seller app registration
Route::post('/seller/register', [\App\Http\Controllers\Api\SellerRegistrationController::class, 'register']);

// Public endpoint for seller app to fetch enabled countries
Route::get('/countries', [CountryController::class, 'index']);

// Public endpoint for seller app to fetch enabled locations
Route::get('/locations', [LocationController::class, 'index']);

// Admin — seller registrations (requires auth)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/sellers/pending', [\App\Http\Controllers\Api\SellerRegistrationController::class, 'pending']);
    Route::get('/admin/sellers/history', [\App\Http\Controllers\Api\SellerRegistrationController::class, 'history']);
    Route::get('/admin/sellers/{id}', [\App\Http\Controllers\Api\SellerRegistrationController::class, 'show']);
    Route::post('/admin/sellers/{id}/approve', [\App\Http\Controllers\Api\SellerRegistrationController::class, 'approve']);
    Route::post('/admin/sellers/{id}/reject', [\App\Http\Controllers\Api\SellerRegistrationController::class, 'reject']);

    Route::get('/admin/notifications', [\App\Http\Controllers\Api\AdminNotificationController::class, 'index']);
    Route::get('/admin/notifications/unread-count', [\App\Http\Controllers\Api\AdminNotificationController::class, 'unreadCount']);
    Route::post('/admin/notifications/{id}/read', [\App\Http\Controllers\Api\AdminNotificationController::class, 'markRead']);
    Route::post('/admin/notifications/read-all', [\App\Http\Controllers\Api\AdminNotificationController::class, 'markAllRead']);
    Route::delete('/admin/notifications/{id}', [\App\Http\Controllers\Api\AdminNotificationController::class, 'destroy']);
});
