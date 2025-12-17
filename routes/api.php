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
    Route::get('/book/lookup', [BookController::class, 'lookupByIsbn']);
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
