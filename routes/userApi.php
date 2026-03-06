<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\HomeController;
use App\Http\Controllers\Api\User\ProductController;

Route::get('/user/home', [HomeController::class, 'home']);
Route::get('/user/products', [ProductController::class, 'index']);
Route::get('/user/product-details/{attribute_id}', [ProductController::class, 'productDetails']);
Route::get('/user/search-suggestions', [ProductController::class, 'suggestions']);
Route::get('/user/education-level', [HomeController::class, 'getSections']);
Route::get('/user/board/{id}', [HomeController::class, 'getcategories']);
Route::get('/user/class', [HomeController::class, 'getSubcategories']);
Route::get('/user/book-type', [HomeController::class, 'getBookTypes']);
Route::get('/user/language', [HomeController::class, 'getLanguages']);

// Cart Routes
Route::get('/user/cart', [ProductController::class, 'getCart']);
Route::post('/user/cart/add', [ProductController::class, 'cartAdd']);
Route::post('/user/cart/update', [ProductController::class, 'cartUpdate']);
Route::post('/user/cart/delete', [ProductController::class, 'cartDelete']);

// Wishlist Routes
Route::get('/user/wishlist', [ProductController::class, 'wishlist']);
Route::post('/user/wishlist/add', [ProductController::class, 'wishlistAdd']);
Route::post('/user/wishlist/remove', [ProductController::class, 'wishlistRemove']);

// Coupon Route
Route::post('/user/coupon/apply', [ProductController::class, 'applyCoupon']);

// Checkout Auth Route
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user/checkout', [ProductController::class, 'checkout']);
    Route::post('/user/verify', [ProductController::class, 'verifyRazorpayPayment']);
    Route::get('/user/orders', [ProductController::class, 'orders']);
});
