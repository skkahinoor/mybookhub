<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sales\SalesExecutiveAuthController;

// Sales Executives routes
Route::prefix('/sales')->namespace('App\Http\Controllers\Sales')->group(function () {
    Route::get('login', 'SalesExecutiveAuthController@showLogin')->name('sales.login');
    Route::post('login', 'SalesExecutiveAuthController@login')->name('sales.login.submit');
    Route::get('/sales/register', [SalesExecutiveAuthController::class, 'showRegister'])->name('sales.register');
    Route::post('/sales/send-otp', [SalesExecutiveAuthController::class, 'sendOtp'])->name('sales.otp.send');
    Route::post('/sales/register', [SalesExecutiveAuthController::class, 'register'])->name('sales.register.submit');

    Route::group(['middleware' => ['sales']], function () {
        Route::get('dashboard', 'SalesExecutiveAuthController@dashboard')->name('sales.dashboard');
        Route::post('logout', 'SalesExecutiveAuthController@logout')->name('sales.logout');

        // Sales Executive Profile
        Route::get('profile', 'ProfileController@edit')->name('sales.profile.edit');
        Route::post('profile', 'ProfileController@update')->name('sales.profile.update');

        // Sales Institution Management (similar to Admin)
        Route::resource('institution-managements', 'InstitutionManagementController')->names([
            'index'   => 'sales.institution_managements.index',
            'create'  => 'sales.institution_managements.create',
            'store'   => 'sales.institution_managements.store',
            'show'    => 'sales.institution_managements.show',
            'edit'    => 'sales.institution_managements.edit',
            'update'  => 'sales.institution_managements.update',
            'destroy' => 'sales.institution_managements.destroy',
        ]);

        // AJAX: get classes/streams for a given institution (only those added by current sales)
        Route::get('students/institution-classes', 'StudentController@getInstitutionClasses')
            ->name('sales.students.institution_classes');

        // Sales Students Management (similar to Admin)
        Route::resource('students', 'StudentController')->names([
            'index'   => 'sales.students.index',
            'create'  => 'sales.students.create',
            'store'   => 'sales.students.store',
            'show'    => 'sales.students.show',
            'edit'    => 'sales.students.edit',
            'update'  => 'sales.students.update',
            'destroy' => 'sales.students.destroy',
        ])->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        // Sales Vendors Management
        Route::resource('vendors', 'VendorController')->names([
            'index'   => 'sales.vendors.index',
            'create'  => 'sales.vendors.create',
            'store'   => 'sales.vendors.store',
            'show'    => 'sales.vendors.show',
            'destroy' => 'sales.vendors.destroy',
        ])->only(['index', 'create', 'store', 'show', 'destroy']);

        // Route::post('students/store-user-location', 'StudentController@storeUserLocation')->name('sales.students.store_user_location');
        // Route::post('students/store-institution-location', 'StudentController@storeInstitutionLocation')->name('sales.students.store_institution_location');
        // Route::get('students/institution/{institution}/address', 'StudentController@getInstitutionAddress')->name('sales.students.institution_address');

        // Sales Reports
        Route::get('reports', 'ReportController@index')->name('sales.reports.index');

        // Sales Withdrawals
        Route::resource('withdrawals', 'WithdrawalController')->names([
            'index'   => 'sales.withdrawals.index',
            'create'  => 'sales.withdrawals.create',
            'store'   => 'sales.withdrawals.store',
        ])->only(['index', 'create', 'store']);

        // Sales Blocks Management (similar to Admin)
        Route::resource('blocks', 'BlockController')->names([
            'index'   => 'sales.blocks.index',
            'create'  => 'sales.blocks.create',
            'store'   => 'sales.blocks.store',
            'show'    => 'sales.blocks.show',
            'edit'    => 'sales.blocks.edit',
            'update'  => 'sales.blocks.update',
            'destroy' => 'sales.blocks.destroy',
        ])->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        // AJAX routes for cascading location dropdowns
        Route::get('institution-countries', [\App\Http\Controllers\Sales\InstitutionManagementController::class, 'getCountries'])->name('institution_countries');
        Route::get('institution-states', [\App\Http\Controllers\Sales\InstitutionManagementController::class, 'getStates'])->name('institution_states');
        Route::get('institution-districts', [\App\Http\Controllers\Sales\InstitutionManagementController::class, 'getDistricts'])->name('institution_districts');
        Route::get('institution-blocks', [\App\Http\Controllers\Sales\InstitutionManagementController::class, 'getBlocks'])->name('institution_blocks');
        Route::get('institution-classes', [\App\Http\Controllers\Sales\InstitutionManagementController::class, 'getClasses'])->name('sales.institution.classes');

        // AJAX routes for sales profile location dropdowns
        Route::get('sales-profile-states', [\App\Http\Controllers\Sales\ProfileController::class, 'getStates'])->name('sales.profile.states');
        Route::get('sales-profile-districts', [\App\Http\Controllers\Sales\ProfileController::class, 'getDistricts'])->name('sales.profile.districts');
        Route::get('sales-profile-blocks', [\App\Http\Controllers\Sales\ProfileController::class, 'getBlocks'])->name('sales.profile.blocks');
    });
});


