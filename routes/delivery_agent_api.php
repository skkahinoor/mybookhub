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
Route::post('/delivery-agent/login', [DeliveryAgentApiController::class, 'login']);

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
    Route::post('/delivery-agent/update-order-status', [DeliveryAgentApiController::class, 'updateOrderStatus']);
    Route::get('/delivery-agent/available-orders', [DeliveryAgentApiController::class, 'getAvailableOrders']);
    
    // Future logical routes for delivery app:
    // Route::get('/delivery-agent/orders', [DeliveryAgentApiController::class, 'availableOrders']);
    // Route::post('/delivery-agent/order/{id}/accept', [DeliveryAgentApiController::class, 'acceptOrder']);
    // Route::post('/delivery-agent/order/{id}/update-status', [DeliveryAgentApiController::class, 'updateOrderStatus']);
});
