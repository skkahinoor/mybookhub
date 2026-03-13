<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookRequest;
use App\Models\HeaderLogo;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Fix: Auto-assign front-facing role if the user has no role assigned
        if (empty($user->role_id)) {
            // Prefer 'student' role; fallback to legacy 'user' role
            $roleId = \App\Helpers\RoleHelper::studentId() ?? \App\Helpers\RoleHelper::userId();
            if ($roleId) {
                $user->role_id = $roleId;
                $user->save();

                $spatieRole = Role::find($roleId);
                if ($spatieRole && ! $user->hasRole($spatieRole->name)) {
                    $user->assignRole($spatieRole);
                }
            }
        }

        $userId = $user->id;
        
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

        $wishlistCount = (int) Wishlist::where('user_id', $userId)->sum('quantity');

        $bookRequestsCount = (int) BookRequest::where('requested_by_user', $userId)->count();
        $pendingBookRequestsCount = (int) BookRequest::where('requested_by_user', $userId)
            ->where('status', 'pending')
            ->count();
        
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

        // Student-specific notifications
        // Some notifications are global (no related_id/type). For students, show:
        // - notifications explicitly tied to this user (related_type=User, related_id=userId)
        // - global announcements (related_id/type null)
        $studentNotificationsQuery = Notification::query()
            ->where(function ($q) use ($userId) {
                $q->where(function ($q2) use ($userId) {
                    $q2->where('related_type', User::class)
                        ->where('related_id', $userId);
                })->orWhere(function ($q2) {
                    $q2->whereNull('related_type')
                        ->whereNull('related_id');
                });
            });

        $studentNotifications = (clone $studentNotificationsQuery)
            ->latest()
            ->limit(5)
            ->get();

        $unreadAlertsCount = (clone $studentNotificationsQuery)
            ->where('is_read', false)
            ->count();

        return view('user.dashboard.index', compact(
            'logos',
            'headerLogo',
            'totalOrders',
            'totalSpent',
            'pendingOrders',
            'deliveredOrders',
            'monthlyData',
            'todayOrders',
            'todayOrdersWorth',
            'weeklyOrders',
            'weeklyOrdersWorth',
            'monthlyOrders',
            'monthlyOrdersWorth',
            'recentOrders',
            'wishlistItems',
            'studentNotifications',
            'unreadAlertsCount',
            'wishlistCount',
            'bookRequestsCount',
            'pendingBookRequestsCount'
        ));
    }
}

