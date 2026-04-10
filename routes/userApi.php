<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\HomeController;
use App\Http\Controllers\Api\User\ProductController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Controllers\Api\User\SellBookController;
use App\Http\Controllers\Api\User\BookRequestController;

Route::post('/user/register', [ProfileController::class, 'register']);
Route::post('/user/verify-otp', [ProfileController::class, 'verifyOtp']);
Route::post('/user/resend-otp', [ProfileController::class, 'resendOtp']);
Route::get('/user/home', [HomeController::class, 'home']);
Route::get('/user/products', [ProductController::class, 'index']);
Route::get('/user/vendor-product/{product_id}', [ProductController::class, 'vendorsproduct']);
Route::get('/user/product-details/{attribute_id}', [ProductController::class, 'productDetails']);
Route::get('/user/search-suggestions', [ProductController::class, 'suggestions']);
Route::get('/user/education-level', [HomeController::class, 'getSections']);
Route::get('/user/institution', [HomeController::class, 'getInstitutions']);
Route::get('/user/institution-class/{institution_id}', [HomeController::class, 'getInstitutionclass']);
Route::get('/user/board/{id}', [HomeController::class, 'getCategories']);
Route::get('/user/class/{id}', [HomeController::class, 'getSubcategories']);
Route::get('/user/subjects/{id}', [HomeController::class, 'getSubjects']);
Route::get('/user/book-type', [HomeController::class, 'getBookTypes']);
Route::get('/user/language', [HomeController::class, 'getLanguages']);

// Configuration Routes
Route::get('/user/delivery-settings', [ProductController::class, 'getDeliverySettings']);
Route::post('/user/update-delivery-settings', [ProductController::class, 'updateDeliverySettings']);

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
    Route::post('/user/register-fcm-token', [\App\Http\Controllers\Api\User\NotificationController::class, 'registerToken']);
    Route::post('/user/profile/basic-info', [ProfileController::class, 'updateBasicInfo']);
    Route::post('/user/profile/academic-info', [ProfileController::class, 'updateAcademicInfo']);
    Route::post('/user/profile/address', [ProfileController::class, 'updateAddress']);
    Route::post('/user/profile/bank-info', [ProfileController::class, 'updateBankInfo']);
    Route::post('/user/change-password', [ProfileController::class, 'changePassword']);
    Route::get('/user/referrals', [ProfileController::class, 'referrals']);

    // Multiple Address Routes
    Route::get('/user/addresses', [\App\Http\Controllers\Api\User\AddressController::class, 'index']);
    Route::get('/user/addresses/{id}', [\App\Http\Controllers\Api\User\AddressController::class, 'show']);
    Route::post('/user/addresses', [\App\Http\Controllers\Api\User\AddressController::class, 'store']);
    Route::put('/user/addresses/{id}', [\App\Http\Controllers\Api\User\AddressController::class, 'update']);
    Route::delete('/user/addresses/{id}', [\App\Http\Controllers\Api\User\AddressController::class, 'destroy']);
    Route::patch('/user/addresses/{id}/set-default', [\App\Http\Controllers\Api\User\AddressController::class, 'setDefault']);


    Route::post('/user/checkout', [ProductController::class, 'checkout']);
    Route::post('/user/verify', [ProductController::class, 'verifyRazorpayPayment']);
    Route::get('/user/orders', [ProductController::class, 'orders']);
    Route::get('/user/order-status/{id}', [ProductController::class, 'orderStatus']);
    Route::get('/user/orders/{id}/receipt', [ProductController::class, 'orderReceipt']);
    Route::post('/user/orders/{id}/cancel', [ProductController::class, 'cancelOrder']);
    Route::post('/user/orders/{id}/pay', [ProductController::class, 'payNow']);
    Route::post('/user/orders/{id}/return', [ProductController::class, 'returnOrder']);
    Route::post('/user/orders/raise-query', [ProductController::class, 'raiseQuery']);
    Route::get('/user/order-queries', [ProductController::class, 'orderQueries']);
    Route::get('/user/order-queries/{id}', [ProductController::class, 'queryDetails']);
    Route::post('/user/order-queries/{id}/reply', [ProductController::class, 'postQueryReply']);
    Route::get('/user/wallet/transactions', [HomeController::class, 'getWalletTransactions']);

    // Sell Book Routes
    Route::get('/user/sell-books', [SellBookController::class, 'index']);
    Route::get('/user/sell-books/form-data', [SellBookController::class, 'formData']);
    Route::post('/user/sell-books/isbn-lookup', [SellBookController::class, 'getBookByIsbn']);
    Route::get('/user/sell-books/name-suggestions', [SellBookController::class, 'nameSuggestions']);
    Route::post('/user/sell-books/add-publisher', [SellBookController::class, 'addPublisher']);
    Route::post('/user/sell-books/calculate-cashback', [SellBookController::class, 'calculateCashback']);
    Route::post('/user/sell-books', [SellBookController::class, 'store']);
    Route::get('/user/sell-books/{id}', [SellBookController::class, 'show']);
    Route::post('/user/sell-books/{id}', [SellBookController::class, 'update']);
    Route::delete('/user/sell-books/{id}', [SellBookController::class, 'destroy']);

    // Sell Faster / Platform Charge APIs
    Route::post('/user/sell-books-faster/toggle', [SellBookController::class, 'toggleSellFaster']);
    Route::post('/user/sell-books-faster/create-order', [SellBookController::class, 'createRazorpayOrder']);
    Route::post('/user/sell-books-faster/verify-payment', [SellBookController::class, 'verifyPlatformChargePayment']);
    Route::post('/user/sell-books-faster/mark-sold', [SellBookController::class, 'markAsSold']);
    // Book Request Routes
    Route::get('/user/book-request/matching-vendors', [BookRequestController::class, 'getMatchingVendors']);
    Route::post('/user/book-request/store', [BookRequestController::class, 'store']);
    Route::get('/user/book-requests', [BookRequestController::class, 'index']);
    Route::get('/user/book-requests/{id}', [BookRequestController::class, 'show']);
    Route::post('/user/book-requests/{id}/reply', [BookRequestController::class, 'replyToQuery']);
});
