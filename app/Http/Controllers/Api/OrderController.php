<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\OrdersProduct;
use App\Models\OrdersLog;
use App\Models\OrderStatus;
use App\Models\OrderItemStatus;
use App\Models\Admin;

class OrderController extends Controller
{

    private function checkAccess(Request $request)
    {
        $admin = $request->user();

        if (!$admin instanceof Admin) {
            return response()->json([
                'status' => false,
                'message' => 'Only Admin or Vendor can access this.'
            ], 403);
        }

        if (!in_array($admin->type, ['superadmin', 'vendor'])) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized role.'
            ], 403);
        }

        if ((int) $admin->status !== 1) {
            return response()->json([
                'status' => false,
                'message' => 'Account inactive.'
            ], 403);
        }

        return null;
    }

    public function index(Request $request)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $admin = $request->user();

        if ($admin->type === 'vendor') {
            if (empty($admin->vendor_id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vendor account not linked properly.'
                ], 403);
            }

            $orders = Order::whereHas('orders_products', function ($q) use ($admin) {
                    $q->where('vendor_id', $admin->vendor_id);
                })
                ->with([
                    'orders_products' => function ($q) use ($admin) {
                        $q->where('vendor_id', $admin->vendor_id);
                    }
                ])
                ->latest()
                ->get();
        } else {
            $orders = Order::with('orders_products')
                ->latest()
                ->get();
        }

        return response()->json([
            'status' => true,
            'data' => $orders
        ], 200);
    }

    public function show(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $admin = $request->user();

        $orderQuery = Order::where('id', $id);

        if ($admin->type === 'vendor') {
            if (empty($admin->vendor_id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vendor account not linked properly.'
                ], 403);
            }

            $orderQuery->whereHas('orders_products', function ($q) use ($admin) {
                $q->where('vendor_id', $admin->vendor_id);
            })->with([
                'orders_products' => function ($q) use ($admin) {
                    $q->where('vendor_id', $admin->vendor_id);
                }
            ]);
        } else {
            $orderQuery->with('orders_products');
        }

        $order = $orderQuery->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found or unauthorized'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'order' => $order,
            'user' => User::find($order->user_id),
            'order_statuses' => OrderStatus::where('status', 1)->get(),
            'item_statuses' => OrderItemStatus::where('status', 1)->get(),
            'logs' => OrdersLog::where('order_id', $order->id)->latest()->get()
        ], 200);
    }

    public function updateOrderStatus(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $admin = $request->user();

        if ($admin->type !== 'superadmin') {
            return response()->json([
                'status' => false,
                'message' => 'Only admin can update order status.'
            ], 403);
        }

        $request->validate([
            'order_status' => 'required|string'
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $order->update([
            'order_status' => $request->order_status,
            'courier_name' => $request->courier_name,
            'tracking_number' => $request->tracking_number
        ]);

        OrdersLog::create([
            'order_id' => $order->id,
            'order_status' => $request->order_status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order status updated successfully'
        ], 200);
    }

    public function updateOrderItemStatus(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $admin = $request->user();

        $request->validate([
            'item_status' => 'required|string'
        ]);

        $itemQuery = OrdersProduct::where('id', $id);

        if ($admin->type === 'vendor') {
            if (empty($admin->vendor_id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vendor account not linked properly.'
                ], 403);
            }

            $itemQuery->where('vendor_id', $admin->vendor_id);
        }

        $item = $itemQuery->first();

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Order item not found or unauthorized'
            ], 403);
        }

        $item->update([
            'item_status' => $request->item_status,
            'courier_name' => $request->courier_name,
            'tracking_number' => $request->tracking_number
        ]);

        OrdersLog::create([
            'order_id' => $item->order_id,
            'order_item_id' => $item->id,
            'order_status' => $request->item_status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order item status updated successfully'
        ], 200);
    }
}
