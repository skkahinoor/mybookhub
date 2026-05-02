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

// Protected Routes
Route::middleware(['auth:sanctum', 'delivery_agent'])->group(function () {
    Route::get('/delivery-agent/profile', [DeliveryAgentApiController::class, 'getProfile']);
    
    // Future logical routes for delivery app:
    // Route::get('/delivery-agent/orders', [DeliveryAgentApiController::class, 'availableOrders']);
    // Route::post('/delivery-agent/order/{id}/accept', [DeliveryAgentApiController::class, 'acceptOrder']);
    // Route::post('/delivery-agent/order/{id}/update-status', [DeliveryAgentApiController::class, 'updateOrderStatus']);
});
