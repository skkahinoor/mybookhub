<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Deliveryagent\DeliveryAgentApiController;

/*
|--------------------------------------------------------------------------
| Delivery Agent API Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::post('/delivery-agent/register', [DeliveryAgentApiController::class, 'register']);
Route::post('/delivery-agent/verify-otp', [DeliveryAgentApiController::class, 'verifyOtp']);
Route::post('/delivery-agent/login', [DeliveryAgentApiController::class, 'login']);
Route::post('/delivery-agent/forgot-password', [DeliveryAgentApiController::class, 'forgotPassword']);
Route::post('/delivery-agent/reset-password', [DeliveryAgentApiController::class, 'resetPassword']);

// Location Routes (Public for registration)
Route::get('/delivery-agent/countries', [DeliveryAgentApiController::class, 'getCountries']);
Route::get('/delivery-agent/states', [DeliveryAgentApiController::class, 'getStates']);
Route::get('/delivery-agent/districts', [DeliveryAgentApiController::class, 'getDistricts']);
Route::get('/delivery-agent/blocks', [DeliveryAgentApiController::class, 'getBlocks']);

// Protected Routes
Route::middleware(['auth:sanctum', 'delivery_agent'])->group(function () {
    Route::get('/delivery-agent/profile', [DeliveryAgentApiController::class, 'getProfile']);
    Route::post('/delivery-agent/toggle-online', [DeliveryAgentApiController::class, 'toggleOnline']);
    Route::post('/delivery-agent/update-profile', [DeliveryAgentApiController::class, 'updateProfile']);
    Route::post('/delivery-agent/accept-order', [DeliveryAgentApiController::class, 'acceptOrder']);
    Route::post('/delivery-agent/reject-order', [DeliveryAgentApiController::class, 'rejectOrder']);
    Route::post('/delivery-agent/update-order-status', [DeliveryAgentApiController::class, 'updateOrderStatus']);
    Route::get('/delivery-agent/available-orders', [DeliveryAgentApiController::class, 'getAvailableOrders']);
    Route::get('/delivery-agent/history', [DeliveryAgentApiController::class, 'getHistory']);
    Route::get('/delivery-agent/order/{id}', [DeliveryAgentApiController::class, 'getOrderDetails']);
    Route::get('/delivery-agent/earnings', [DeliveryAgentApiController::class, 'getAgentEarningsData']);
    Route::post('/delivery-agent/request-payout', [DeliveryAgentApiController::class, 'requestPayout']);
    Route::get('/delivery-agent/payout-history', [DeliveryAgentApiController::class, 'getPayoutHistory']);
    Route::post('/delivery-agent/update-fcm-token', [DeliveryAgentApiController::class, 'updateFcmToken']);
    Route::post('/delivery-agent/contact-us', [DeliveryAgentApiController::class, 'submitContactQuery']);
    Route::get('/delivery-agent/contact-queries', [DeliveryAgentApiController::class, 'getContactQueries']);
    Route::get('/delivery-agent/contact-queries/{id}', [DeliveryAgentApiController::class, 'getQueryMessages']);
    Route::post('/delivery-agent/contact-queries/{id}/reply', [DeliveryAgentApiController::class, 'replyContactQuery']);
    Route::post('/delivery-agent/contact-queries/{id}/close', [DeliveryAgentApiController::class, 'closeContactQuery']);
    Route::post('/delivery-agent/delete-account', [DeliveryAgentApiController::class, 'deleteAccount']);
    
    // Future logical routes for delivery app:
    // Route::get('/delivery-agent/orders', [DeliveryAgentApiController::class, 'availableOrders']);
    // Route::post('/delivery-agent/order/{id}/accept', [DeliveryAgentApiController::class, 'acceptOrder']);
    // Route::post('/delivery-agent/order/{id}/update-status', [DeliveryAgentApiController::class, 'updateOrderStatus']);
});
