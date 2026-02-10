<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InstitutionController;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\SalesDashboardController;
use App\Http\Controllers\Api\SalesReportController;
use App\Http\Controllers\Api\WithdrawalApiController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\CatalogueController;
use App\Http\Controllers\Api\VendorPlanController;
use App\Http\Controllers\Api\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider within a group
| which is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public route: login (accessible without token)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/sales/register', [AuthController::class, 'register']);
Route::post('/sales/verify-otp', [AuthController::class, 'verifyOtp']);

Route::post('/vendor/register', [VendorController::class, 'register']);
Route::post('/vendor/verify-otp', [VendorController::class, 'verifyOtp']);

// rozerpay webhook for payment
Route::post('razorpay/webhook', [VendorPlanController::class, 'razorpayWebhook']);
// end rozerpay webhook for payment**

// Protected routes: logout (requires valid Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->get('/validate-token', [AuthController::class, 'validateToken']);

// Institution
Route::middleware('auth:sanctum')->group(function () {
    // Cascading dropdowns
    Route::get('/countries', [InstitutionController::class, 'getCountries']);
    Route::get('/states/{country_id}', [InstitutionController::class, 'getStates']);
    Route::get('/districts/{state_id}', [InstitutionController::class, 'getDistricts']);
    Route::get('/blocks/{district_id}', [InstitutionController::class, 'getBlocks']);

    Route::get('/institutions', [InstitutionController::class, 'index']);
    Route::post('/institutions', [InstitutionController::class, 'store']);
    Route::put('/institutions/{id}', [InstitutionController::class, 'update']);
    Route::delete('/institutions/{id}', [InstitutionController::class, 'destroy']);
    // Route::post('/book/lookup', [BookController::class, 'lookupByIsbn']);
});

// Student
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/students', [StudentApiController::class, 'index']);
    Route::post('/students', [StudentApiController::class, 'store']);
    Route::put('/students/{id}', [StudentApiController::class, 'update']);
    Route::delete('/students/{id}', [StudentApiController::class, 'destroy']);
    Route::get('/getStudents/', [StudentApiController::class, 'getStudentByClass']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/sales/profile', [SalesController::class, 'getProfile']);
    Route::post('/sales/update-profile', [SalesController::class, 'updateProfile']);
    Route::get('/sales/bank-details', [SalesController::class, 'getBankDetails']);
    Route::put('/sales/update-bank', [SalesController::class, 'updateBankDetails']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/sales/today-institutes', [SalesDashboardController::class, 'todayInstitutes']);
    Route::get('/sales/total-institutes', [SalesDashboardController::class, 'totalInstitutes']);

    Route::get('/sales/today-students', [SalesDashboardController::class, 'todayStudents']);
    Route::get('/sales/total-students', [SalesDashboardController::class, 'totalStudents']);

    Route::get('/sales/graph-data', [SalesDashboardController::class, 'graphDashboard']);
    Route::get('/sales/report', [SalesReportController::class, 'getSalesReport']);
    Route::get('/sales/withdrawal-dashboard', [WithdrawalApiController::class, 'dashboard']);
    Route::post('/sales/withdraw-request', [WithdrawalApiController::class, 'requestWithdraw']);
});

// Vendor Profile Management
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/vendor/profile', [VendorController::class, 'getprofile']);
    Route::post('/vendor/profile', [VendorController::class, 'updateprofile']);
    Route::post('/vendor/business-details', [VendorController::class, 'saveBusinessDetails']);
    Route::post('/vendor/bank-details', [VendorController::class, 'saveBankDetails']);
    Route::get('/vendor/business-details', [VendorController::class, 'getBusinessDetails']);
    Route::get('/vendor/bank-details', [VendorController::class, 'getBankDetails']);
});

// Vendor Plan Management
Route::middleware('auth:sanctum')->group(function () {
    Route::get('vendor/plan/getplan', [VendorPlanController::class, 'getPlan']);
    Route::get('vendor/plan/status', [VendorPlanController::class, 'status']);
    Route::post('vendor/plan/upgrade', [VendorPlanController::class, 'upgrade']);
    Route::post('vendor/plan/webhookupgrade', [VendorPlanController::class, 'webhookupgrade']);
    // Route::post('vendor/plan/webhook', [VendorPlanController::class, 'razorpayWebhook']);

    Route::post('vendor/plan/verify', [VendorPlanController::class, 'verify']);
    Route::post('vendor/plan/expoverify', [VendorPlanController::class, 'expoverify']);
    Route::post('vendor/plan/downgrade', [VendorPlanController::class, 'downgrade']);
});

// Catalogue Management
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/vendor/sections', [CatalogueController::class, 'getSection']);
    Route::post('/vendor/sections', [CatalogueController::class, 'storeSection']);
    Route::put('/vendor/sections/{id}', [CatalogueController::class, 'updateSection']);
    Route::delete('/vendor/sections/{id}', [CatalogueController::class, 'destroySection']);

    Route::get('/vendor/category', [CatalogueController::class, 'getCategory']);
    Route::post('/vendor/category', [CatalogueController::class, 'storeCategory']);
    Route::post('/vendor/category/{id}', [CatalogueController::class, 'updateCategory']);
    Route::delete('/vendor/category/{id}', [CatalogueController::class, 'destroyCategory']);

    Route::get('/vendor/publisher', [CatalogueController::class, 'getPublisher']);
    Route::post('/vendor/publisher', [CatalogueController::class, 'storePublisher']);
    Route::put('/vendor/publisher/{id}', [CatalogueController::class, 'updatePublisher']);
    Route::delete('/vendor/publisher/{id}', [CatalogueController::class, 'destroyPublisher']);

    Route::get('/vendor/author', [CatalogueController::class, 'getAuthor']);
    Route::post('/vendor/author', [CatalogueController::class, 'storeAuthor']);
    Route::put('/vendor/author/{id}', [CatalogueController::class, 'updateAuthor']);
    Route::delete('/vendor/author/{id}', [CatalogueController::class, 'destroyAuthor']);

    Route::get('/vendor/subject', [CatalogueController::class, 'getSubject']);
    Route::post('/vendor/subject', [CatalogueController::class, 'storeSubject']);
    Route::put('/vendor/subject/{id}', [CatalogueController::class, 'updateSubject']);
    Route::delete('/vendor/subject/{id}', [CatalogueController::class, 'destroySubject']);

    Route::get('/vendor/language', [CatalogueController::class, 'getLanguage']);
    Route::post('/vendor/language', [CatalogueController::class, 'storeLanguage']);
    Route::put('/vendor/language/{id}', [CatalogueController::class, 'updateLanguage']);
    Route::delete('/vendor/language/{id}', [CatalogueController::class, 'destroyLanguage']);

    Route::get('/vendor/edition', [CatalogueController::class, 'getEdition']);
    Route::post('/vendor/edition', [CatalogueController::class, 'storeEdition']);
    Route::put('/vendor/edition/{id}', [CatalogueController::class, 'updateEdition']);
    Route::delete('/vendor/edition/{id}', [CatalogueController::class, 'destroyEdition']);

    Route::get('/vendor/coupons', [CatalogueController::class, 'getCoupon']);
    Route::post('/vendor/coupons', [CatalogueController::class, 'storeCoupon']);
    Route::post('/vendor/coupons/{id}', [CatalogueController::class, 'updateCoupon']);
    Route::patch('/vendor/coupons/{id}/status', [CatalogueController::class, 'updateCouponStatus']);
    Route::delete('/vendor/coupons/{id}', [CatalogueController::class, 'destroyCoupon']);

    Route::get('vendor/book-requests', [CatalogueController::class, 'getBookRequest']);
    Route::delete('vendor/book-requests/{id}', [CatalogueController::class, 'deleteBookRequest']);
    // Route::patch('vendor/book-requests/{id}/status', [CatalogueController::class, 'updateBookRequestStatus']);
    Route::put('vendor/book-requests/{id}/reply', [CatalogueController::class, 'replyBookRequest']);

    Route::get('/vendor/products', [BookController::class, 'getproduct']);
    Route::patch('/vendor/products/{id}/status', [BookController::class, 'updateProductStatus']);
    Route::patch('/vendor/products/{id}/bestSeller', [BookController::class, 'isBestSeller']);
    Route::patch('/vendor/products/{id}/featured', [BookController::class, 'isFeatured']);
    Route::delete('/vendor/products/{id}', [BookController::class, 'deleteProduct']);
    Route::post('/vendor/products/isbn-lookup', [BookController::class, 'lookupByIsbn']);
    Route::get('/vendor/products/autocomplete', [BookController::class, 'autocomplete']);
    Route::get('/vendor/products/byId/{id}', [BookController::class, 'show']);
    Route::get('/vendor/products/{id}/summary', [BookController::class, 'productSummaryByid']);
    Route::post('/vendor/products', [BookController::class, 'storeManualProduct']);
    Route::post('/vendor/products/summary', [BookController::class, 'productSummary']);
    Route::post('/vendor/products/stock', [BookController::class, 'storeProductAttribute']);
});

// Order Management
Route::middleware('auth:sanctum')->group(function () {
    Route::get('vendor/orders', [OrderController::class, 'index']);
    Route::get('vendor/orders/{order_id}', [OrderController::class, 'show']);
    Route::patch('vendor/orders/{order_id}/status', [OrderController::class, 'updateOrderStatus']);
    Route::patch('vendor/order-items/{order_item_id}/status', [OrderController::class, 'updateOrderItemStatus']);

    // Route::get('vendor/sales/cart', [OrderController::class, 'cart']);
    Route::post('vendor/sales/search-isbn', [OrderController::class, 'searchByIsbn']);
    Route::post('/vendor/sales/process', [OrderController::class, 'processSale']);
    Route::post('/vendor/coupon/validate', [OrderController::class, 'validateCoupon']);
    // Route::post('vendor/sales/cart/add', [OrderController::class, 'addToCart']);
    // Route::post('vendor/cart/remove', [OrderController::class, 'removeFromCart']);
    // Route::post('vendor/process', [OrderController::class, 'processSale']);
});
