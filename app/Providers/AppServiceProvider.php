<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Front\ProductsController;
use App\Models\Section;
use App\Models\FilterClassSubject;
use App\Models\Notification;
use App\Models\User;

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

            // Fetch Hierarchical Data from filter_class_subject
            $navFilterData = FilterClassSubject::with(['section', 'category', 'subcategory'])
                ->get()
                ->groupBy('section_id')
                ->map(function ($items) {
                    $section = $items->first()->section;
                    if (!$section) return null;

                    return [
                        'name' => $section->name,
                        'boards' => $items->groupBy('category_id')->map(function ($subItems) {
                            $category = $subItems->first()->category;
                            if (!$category) return null;

                            return [
                                'name' => $category->category_name,
                                'classes' => $subItems->unique('sub_category_id')->map(function ($item) {
                                    return [
                                        'id' => $item->subcategory->id ?? null,
                                        'name' => $item->subcategory->subcategory_name ?? null,
                                        'section_id' => $item->section_id,
                                        'category_id' => $item->category_id
                                    ];
                                })->filter(fn($c) => $c['id'])->values()
                            ];
                        })->filter()->values()
                    ];
                })->filter()->values();

            $view->with('navFilterData', $navFilterData);
        });

        // Share student notifications with user navbar, front header, and layout3 header
        View::composer(['user.layout.navbar', 'front.layout.header', 'front.layout.layout3'], function ($view) {
            if (!Auth::check()) {
                return;
            }

            $userId = Auth::id();

            $navNotificationsQuery = Notification::query()
                ->where(function ($q) use ($userId) {
                    $q->where(function ($q2) use ($userId) {
                        $q2->where('related_type', User::class)
                            ->where('related_id', $userId);
                    })->orWhere(function ($q2) {
                        $q2->whereNull('related_type')
                            ->whereNull('related_id');
                    });
                });

            $navUnreadCount = (clone $navNotificationsQuery)
                ->where('is_read', false)
                ->count();

            $navNotifications = (clone $navNotificationsQuery)
                ->latest()
                ->limit(5)
                ->get();

            $view->with('navUnreadCount', $navUnreadCount);
            $view->with('navNotifications', $navNotifications);
        });
    }
}
