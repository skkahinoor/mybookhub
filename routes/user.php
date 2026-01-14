<?php


use App\Http\Controllers\User\AccountController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\BookRequestController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\OrderController;
use Illuminate\Support\Facades\Route;


//User routes
Route::prefix('/user')->namespace('App\Http\Controllers\User')->group(function () {

    Route::get('/login', [AuthController::class, 'Login'])->name('user.login');
    Route::get('/register', [AuthController::class, 'Register'])->name('user.register');
    Route::post('/loginStore', [AuthController::class, 'loginStore'])->name('user.loginstore');
    Route::post('/registerStore', [AuthController::class, 'registerStore'])->name('user.registerstore');

    // Protected routes (require authentication)
    Route::group(['middleware' => ['auth', 'user']], function () {
        Route::get('/index', [DashboardController::class, 'index'])->name('user.index');
        Route::post('/user/profile/update', [AccountController::class, 'updateProfile'])
            ->name('user.profile.update');
        Route::post('user/logout', [AuthController::class, 'logout'])->name('user.logout');
        Route::match(['GET', 'POST'], '/account', [AccountController::class, 'index'])->name('user.account');
        Route::post('/avatar', [AccountController::class, 'updateAvatar'])->name('user.avatar.update');

        Route::post('/book-request', [BookRequestController::class, 'store'])->name('user.book.request.store');
        Route::get('/book-requests', [BookRequestController::class, 'indexbookrequest'])->name('user.book.indexrequest');
        Route::post('/book-request/{id}/reply', [BookRequestController::class, 'replyToQuery'])->name('user.book.reply');
        Route::get('/queries', [BookRequestController::class, 'indexqueries'])->name('user.query.index');
        Route::get('/orders', [OrderController::class, 'index'])->name('user.orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('user.orders.show');
    });
});
