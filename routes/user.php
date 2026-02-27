<?php


use App\Http\Controllers\User\AccountController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\BookRequestController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\SellBookController;
use App\Http\Controllers\User\WalletController;
use Illuminate\Support\Facades\Route;


// Student routes (previously /user)
Route::prefix('/student')->namespace('App\Http\Controllers\User')->group(function () {

    // Public auth endpoints
    Route::get('/login', [AuthController::class, 'Login'])->name('student.login');
    Route::get('/register', [AuthController::class, 'Register'])->name('student.register');
    Route::post('/loginStore', [AuthController::class, 'loginStore'])->name('student.loginstore');
    Route::post('/registerStore', [AuthController::class, 'registerStore'])->name('student.registerstore');

    // Protected routes (require authentication as student)
    Route::group(['middleware' => ['auth', 'student']], function () {
        Route::get('/index', [DashboardController::class, 'index'])->name('student.index');
        Route::post('/profile/update', [AccountController::class, 'updateProfile'])
            ->name('student.profile.update');
        Route::post('/logout', [AuthController::class, 'logout'])->name('student.logout');
        Route::match(['GET', 'POST'], '/account', [AccountController::class, 'index'])->name('student.account');
        Route::post('/avatar', [AccountController::class, 'updateAvatar'])->name('student.avatar.update');

        // Wallet
        Route::get('/wallet', [WalletController::class, 'index'])->name('student.wallet');

        // Book Request
        Route::post('/book-request', [BookRequestController::class, 'store'])->name('student.book.request.store');
        Route::get('/book-requests', [BookRequestController::class, 'indexbookrequest'])->name('student.book.indexrequest');
        Route::post('/book-request/{id}/reply', [BookRequestController::class, 'replyToQuery'])->name('student.book.reply');
        Route::get('/queries', [BookRequestController::class, 'indexqueries'])->name('student.query.index');
        Route::get('/orders', [OrderController::class, 'index'])->name('student.orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('student.orders.show');
        Route::get('/orders/cancel/{id}', [OrderController::class, 'cancelOrder'])->name('student.orders.cancel');
        Route::get('/orders/pay-now/{id}', [OrderController::class, 'payNow'])->name('student.orders.payNow');

        // Sell Old Book routes
        Route::get('/sell-book', [SellBookController::class, 'index'])->name('student.sell-book.index');
        Route::get('/sell-book/create', [SellBookController::class, 'create'])->name('student.sell-book.create');
        Route::post('/sell-book', [SellBookController::class, 'store'])->name('student.sell-book.store');
        Route::get('/sell-book/{id}', [SellBookController::class, 'show'])->name('student.sell-book.show');
        Route::get('/sell-book/{id}/edit', [SellBookController::class, 'edit'])->name('student.sell-book.edit');
        Route::put('/sell-book/{id}', [SellBookController::class, 'update'])->name('student.sell-book.update');
    });
});
