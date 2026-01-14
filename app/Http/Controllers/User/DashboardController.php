<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\Order;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id;
        
        // Total Orders
        $totalOrders = Order::where('user_id', $userId)->count();
        $totalSpent = Order::where('user_id', $userId)->sum('grand_total');
        
        // Recent orders (latest 5)
        $recentOrders = Order::with('orders_products')
            ->where('user_id', $userId)
            ->latest('created_at')
            ->take(5)
            ->get();
        
        // Wishlist items (latest 5)
        $wishlistItems = Wishlist::with('product')
            ->where('user_id', $userId)
            ->latest('created_at')
            ->take(5)
            ->get();
        
        // Today's Orders
        $todayOrders = Order::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->count();
        $todayOrdersWorth = Order::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->sum('grand_total');
        
        // Weekly Orders (last 7 days)
        $weeklyOrders = Order::where('user_id', $userId)
            ->whereBetween('created_at', [now()->subDays(7)->startOfDay(), now()->endOfDay()])
            ->count();
        $weeklyOrdersWorth = Order::where('user_id', $userId)
            ->whereBetween('created_at', [now()->subDays(7)->startOfDay(), now()->endOfDay()])
            ->sum('grand_total');
        
        // Monthly Orders (current month)
        $monthlyOrders = Order::where('user_id', $userId)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
        $monthlyOrdersWorth = Order::where('user_id', $userId)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('grand_total');
        
        $pendingOrders = Order::where('user_id', $userId)
            ->where('order_status', 'like', '%pending%')
            ->count();
        $deliveredOrders = Order::where('user_id', $userId)
            ->where('order_status', 'like', '%delivered%')
            ->count();

        // Monthly spending data for chart (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlySpent = Order::where('user_id', $userId)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('grand_total');
            $monthlyData[] = [
                'month' => $month->format('M Y'),
                'amount' => $monthlySpent
            ];
        }
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        return view('user.dashboard.index', compact('logos', 'headerLogo', 'totalOrders', 'totalSpent', 'pendingOrders', 'deliveredOrders', 'monthlyData', 'todayOrders', 'todayOrdersWorth', 'weeklyOrders', 'weeklyOrdersWorth', 'monthlyOrders', 'monthlyOrdersWorth', 'recentOrders', 'wishlistItems'));
    }
}

