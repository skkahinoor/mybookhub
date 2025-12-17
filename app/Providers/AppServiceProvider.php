<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Front\ProductsController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Customizing The Pagination View Using Bootstrap (displaying Laravel pagination using Bootstrap pagination): https://laravel.com/docs/9.x/pagination#using-bootstrap
        \Illuminate\Pagination\Paginator::useBootstrap();

        // Share cart data with all views that use layout3
        View::composer('front.layout.layout3', function ($view) {
            // Ensure session_id is set
            if (empty(session('session_id'))) {
                session(['session_id' => session()->getId()]);
            }
            
            $cartData = ProductsController::getHeaderCartData();
            $wishlistData = ProductsController::getHeaderWishlistData();
            
            $view->with('headerCartItems', $cartData['cartItems']);
            $view->with('headerCartTotal', $cartData['totalPrice']);
            $view->with('headerCartItemsCount', $cartData['cartItemsCount']);
            
            $view->with('headerWishlistItems', $wishlistData['wishlistItems']);
            $view->with('headerWishlistItemsCount', $wishlistData['wishlistItemsCount']);
        });
    }
}
