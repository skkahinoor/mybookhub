<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        $totalOrders = Order::where('user_id', Auth::user()->id)->count();
        $totalSpent = Order::where('user_id', Auth::user()->id)->sum('grand_total');
        $pendingOrders = Order::where('user_id', Auth::user()->id)
            ->where('order_status', 'like', '%pending%')
            ->count();
        $deliveredOrders = Order::where('user_id', Auth::user()->id)
            ->where('order_status', 'like', '%delivered%')
            ->count();

        if ($request->ajax()) {
            $orders = Order::with('orders_products')
                ->where('user_id', Auth::user()->id)
                ->orderBy('id', 'desc');

            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('order_id', function ($order) {
                    return '<strong>#' . $order->id . '</strong>';
                })
                ->addColumn('products', function ($order) {
                    if ($order->orders_products->count() > 0) {
                        $firstProduct = $order->orders_products->first();
                        $productImage = \App\Models\Product::getProductImage($firstProduct->product_id);

                        $html = '<div class="d-flex align-items-center">';
                        $html .= '<img src="' . asset('front/images/product_images/small/' . $productImage) . '" alt="Product" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; margin-right: 10px;">';
                        $html .= '<div>';
                        $html .= '<div class="font-weight-bold">' . $firstProduct->product_name . '</div>';
                        if ($order->orders_products->count() > 1) {
                            $html .= '<small class="text-muted">+' . ($order->orders_products->count() - 1) . ' more item(s)</small>';
                        }
                        $html .= '</div>';
                        $html .= '</div>';
                        return $html;
                    }
                    return '<span class="text-muted">No products</span>';
                })
                ->addColumn('order_status', function ($order) {
                    $status = strtolower($order->order_status);
                    $badgeClass = 'badge-secondary';

                    if (strpos($status, 'pending') !== false) {
                        $badgeClass = 'badge-warning';
                    } elseif (
                        strpos($status, 'shipped') !== false ||
                        strpos($status, 'delivered') !== false
                    ) {
                        $badgeClass = 'badge-success';
                    } elseif (strpos($status, 'cancel') !== false) {
                        $badgeClass = 'badge-danger';
                    } elseif (
                        strpos($status, 'progress') !== false ||
                        strpos($status, 'processing') !== false
                    ) {
                        $badgeClass = 'badge-info';
                    }

                    return '<span class="badge ' . $badgeClass . '" style="padding: 6px 12px; font-size: 12px;">' . $order->order_status . '</span>';
                })
                ->addColumn('payment_method', function ($order) {
                    return $order->payment_method;
                })
                ->addColumn('grand_total', function ($order) {
                    return '<strong>₹' . number_format($order->grand_total, 2) . '</strong>';
                })
                ->addColumn('order_date', function ($order) {
                    return $order->created_at->format('M d, Y');
                })
                ->addColumn('action', function ($order) {
                    return '<a href="' . route('user.orders.show', $order->id) . '" class="btn btn-sm btn-primary" style="color: white; padding: 1.1rem 1rem !important;">View Details</a>';
                })
                ->rawColumns(['order_id', 'products', 'order_status', 'grand_total', 'action'])
                ->make(true);
        }

        return view('user.orders.index', compact('logos', 'headerLogo', 'totalOrders', 'totalSpent', 'pendingOrders', 'deliveredOrders'));
    }

    public function show($id)
    {

        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $orderDetails = Order::with('orders_products')
            ->where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();

        // Calculate statistics for graphs
        $totalOrders = Order::where('user_id', Auth::user()->id)->count();
        $totalSpent = Order::where('user_id', Auth::user()->id)->sum('grand_total');
        $pendingOrders = Order::where('user_id', Auth::user()->id)
            ->where('order_status', 'like', '%pending%')
            ->count();
        $deliveredOrders = Order::where('user_id', Auth::user()->id)
            ->where('order_status', 'like', '%delivered%')
            ->count();



        return view('user.orders.orderdetails', compact('orderDetails', 'totalOrders', 'totalSpent', 'pendingOrders', 'deliveredOrders', 'logos', 'headerLogo'));
    }
    public function cancelOrder($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::user()->id)->firstOrFail();

        // Check if order can be cancelled (e.g. only if Status is New or Pending)
        $allowedStatus = ['New', 'Pending'];
        if (!in_array($order->order_status, $allowedStatus)) {
            return redirect()->back()->with('error_message', 'Order cannot be cancelled at this stage.');
        }

        // Update Status
        $order->order_status = 'Cancelled';
        $order->save();

        // Revert Wallet
        \App\Models\WalletTransaction::revertWallet($id);

        return redirect()->back()->with('success_message', 'Order has been cancelled and wallet balance (if any) has been reverted.');
    }
    public function payNow($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::user()->id)->firstOrFail();
        if ($order->order_status != 'Pending') {
            return redirect()->back()->with('error_message', 'Order is already paid or cannot be paid at this stage.');
        }

        Session::put('order_id', $id);
        return redirect('/razorpay');
    }
}
