<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\HomeController;
use App\Http\Controllers\Api\User\ProductController;
use App\Http\Controllers\Api\User\ProfileController;

Route::get('/user/home', [HomeController::class, 'home']);
Route::get('/user/products', [ProductController::class, 'index']);
Route::get('/user/product-details/{attribute_id}', [ProductController::class, 'productDetails']);
Route::get('/user/search-suggestions', [ProductController::class, 'suggestions']);
Route::get('/user/education-level', [HomeController::class, 'getSections']);
Route::get('/user/institution', [HomeController::class, 'getInstitutions']);
Route::get('/user/institution-class/{institution_id}', [HomeController::class, 'getInstitutionclass']);
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
    // Profile Routes
    Route::get('/user/profile', [ProfileController::class, 'getProfile']);
    Route::post('/user/profile/basic-info', [ProfileController::class, 'updateBasicInfo']);
    Route::post('/user/profile/academic-info', [ProfileController::class, 'updateAcademicInfo']);
    Route::post('/user/profile/address', [ProfileController::class, 'updateAddress']);
    Route::post('/user/change-password', [ProfileController::class, 'changePassword']);

    Route::post('/user/checkout', [ProductController::class, 'checkout']);
    Route::post('/user/verify', [ProductController::class, 'verifyRazorpayPayment']);
    Route::get('/user/orders', [ProductController::class, 'orders']);
    Route::get('/user/order-status/{id}', [ProductController::class, 'orderStatus']);
    Route::get('/user/wallet/transactions', [HomeController::class, 'getWalletTransactions']);
});
