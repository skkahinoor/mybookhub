<?php


use App\Http\Controllers\User\AccountController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\BookRequestController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\SellBookController;
use App\Http\Controllers\User\WalletController;
use Illuminate\Support\Facades\Route;


// Student routes (previously /user)
Route::prefix('/student')->namespace('App\Http\Controllers\User')->group(function () {

    // Public auth endpoints
    Route::get('/login', [AuthController::class, 'Login'])->name('student.login');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('student.forgot-password.form');
    Route::get('/register', [AuthController::class, 'Register'])->name('student.register');
    Route::post('/loginStore', [AuthController::class, 'loginStore'])->name('student.loginstore');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('student.forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('student.reset-password');
    Route::post('/registerStore', [AuthController::class, 'registerStore'])->name('student.registerstore');

    // Protected routes (require authentication as student)
    Route::group(['middleware' => ['auth', 'student']], function () {
        Route::get('/index', [DashboardController::class, 'index'])->name('student.index');
        Route::post('/profile/update', [AccountController::class, 'updateProfile'])
            ->name('student.profile.update');
        Route::post('/logout', [AuthController::class, 'logout'])->name('student.logout');
        Route::match(['GET', 'POST'], '/account', [AccountController::class, 'index'])->name('student.account');
        Route::get('/academic-boards', [AccountController::class, 'getAcademicBoards'])->name('student.academic.boards');
        Route::get('/academic-classes', [AccountController::class, 'getAcademicClasses'])->name('student.academic.classes');
        Route::post('/avatar', [AccountController::class, 'updateAvatar'])->name('student.avatar.update');

        // Wallet
        Route::get('/wallet', [WalletController::class, 'index'])->name('student.wallet');
        Route::get('/referrals', [WalletController::class, 'referrals'])->name('student.referrals');

        // Book Request
        Route::post('/book-request', [BookRequestController::class, 'store'])->name('student.book.request.store');
        Route::get('/book-requests', [BookRequestController::class, 'indexbookrequest'])->name('student.book.indexrequest');
        Route::post('/book-request/{id}/reply', [BookRequestController::class, 'replyToQuery'])->name('student.book.reply');
        Route::post('/book-request/{id}/end-conversation', [BookRequestController::class, 'endConversation'])->name('student.book.end_conversation');
        Route::get('/queries', [BookRequestController::class, 'indexqueries'])->name('student.query.index');
        Route::get('/queries/raise', [BookRequestController::class, 'raiseQueryPage'])->name('student.query.raise');
        Route::get('/orders', [OrderController::class, 'index'])->name('student.orders.index');
        Route::get('/orders/queries', [OrderController::class, 'orderQueries'])->name('student.orders.queries');
        Route::get('/orders/query-details/{id}', [OrderController::class, 'queryDetails'])->name('student.orders.query.details');
        Route::post('/orders/query-reply/{id}', [OrderController::class, 'postQueryReply'])->name('student.orders.query.reply');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('student.orders.show');
        Route::get('/orders/cancel/{id}', [OrderController::class, 'cancelOrder'])->name('student.orders.cancel');
        Route::post('/orders/item/return/{id}', [OrderController::class, 'returnItem'])->name('student.orders.return_item');
        Route::get('/orders/pay-now/{id}', [OrderController::class, 'payNow'])->name('student.orders.payNow');
        Route::post('/orders/raise-query', [OrderController::class, 'raiseQuery'])->name('student.orders.raise-query');

        // Notifications (Student)
        Route::get('/notifications', [NotificationController::class, 'index'])->name('student.notifications.index');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('student.notifications.read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('student.notifications.mark_all_read');
        Route::post('/notifications/register-token', [NotificationController::class, 'registerFcmToken'])->name('student.notifications.register_token');

        // Sell Old Book routes
        Route::get('/sell-book', [SellBookController::class, 'index'])->name('student.sell-book.index');
        Route::get('/sell-book/create', [SellBookController::class, 'create'])->name('student.sell-book.create');
        Route::post('/sell-book/check-isbn', [SellBookController::class, 'getBookByIsbn'])->name('student.sell-book.check-isbn');
        Route::post('/sell-book/name-suggestions', [SellBookController::class, 'nameSuggestions'])->name('student.sell-book.name-suggestions');
        Route::post('/sell-book/add-publisher-ajax', [SellBookController::class, 'addPublisherAjax'])->name('student.sell-book.addPublisherAjax');
        Route::get('/sell-book/boards', [SellBookController::class, 'getBoards'])->name('student.sell-book.boards');
        Route::get('/sell-book/classes', [SellBookController::class, 'getClasses'])->name('student.sell-book.classes');
        Route::get('/sell-book/subjects', [SellBookController::class, 'getSubjects'])->name('student.sell-book.subjects');

        Route::post('/sell-book', [SellBookController::class, 'store'])->name('student.sell-book.store');
        
        Route::post('/sell-book/toggle-sell-faster', [SellBookController::class, 'toggleSellFaster'])->name('student.sell-book.toggle-sell-faster');
        Route::post('/sell-book/razorpay/create-order', [SellBookController::class, 'createRazorpayOrder'])->name('student.sell-book.razorpay.create-order');
        Route::post('/sell-book/razorpay/verify-payment', [SellBookController::class, 'verifyPlatformChargePayment'])->name('student.sell-book.razorpay.verify-payment');
        Route::post('/sell-book/mark-as-sold', [SellBookController::class, 'markAsSold'])->name('student.sell-book.mark-as-sold');
        Route::get('/sell-book/{id}/purchaser-details', [SellBookController::class, 'purchaserDetails'])->name('student.sell-book.purchaser-details');

        Route::post('/sell-book/{id}', [SellBookController::class, 'store'])->name('student.sell-book.update');
    });
});
