<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Order;
use App\Models\User;
use App\Models\OrdersProduct;
use App\Models\OrdersLog;
use App\Models\OrderStatus;
use App\Models\OrderItemStatus;
use App\Models\Admin;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\ProductsAttribute;

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

    public function searchByIsbn(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $request->validate([
            'isbn' => 'required|string|max:20'
        ]);

        $isbn     = trim($request->isbn);
        $vendorId = $request->user()->vendor_id;

        $product = Product::where('product_isbn', $isbn)->first();

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Book not found'
            ], 404);
        }

        $attribute = ProductsAttribute::where([
            'product_id' => $product->id,
            'vendor_id'  => $vendorId
        ])->first();

        if (!$attribute) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not available for this vendor'
            ], 404);
        }

        // ✅ SAME PRICE LOGIC AS WEBSITE
        $basePrice       = $attribute->price ?? $product->product_price;
        $discountPercent = $attribute->product_discount ?? 0;

        $discountAmount = round(($basePrice * $discountPercent) / 100);
        $finalPrice     = round($basePrice - $discountAmount);

        $basePath = url('front/images/product_images');

        return response()->json([
            'status' => true,
            'data' => [
                'product_id'       => $product->id,
                'product_name'     => $product->product_name,
                'product_isbn'     => $product->product_isbn,
                'base_price'       => round($basePrice),
                'discount_percent' => $discountPercent,
                'discount_amount'  => $discountAmount,
                'final_price'      => $finalPrice,
                'stock'            => $attribute->stock,

                'image_urls' => [
                    'large'  => $product->product_image ? $basePath . '/large/' . $product->product_image : null,
                    'medium' => $product->product_image ? $basePath . '/medium/' . $product->product_image : null,
                    'small'  => $product->product_image ? $basePath . '/small/' . $product->product_image : null,
                ],
            ]
        ], 200);
    }


    public function processSale(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_mobile'  => 'required|string|max:20',
            'customer_email'   => 'nullable|email',
            'customer_address' => 'nullable|string',

            'extra_discount'   => 'nullable|numeric|min:0',
            'coupon_code'      => 'nullable|string|max:50',

            'cart' => 'required|array|min:1',
            'cart.*.product_id' => 'required|integer',
            'cart.*.quantity'   => 'required|integer|min:1'
        ]);

        $vendorId = $request->user()->vendor_id;
        $adminId  = $request->user()->id;

        // Merge duplicate products
        $cart = collect($request->cart)
            ->groupBy('product_id')
            ->map(fn($items) => [
                'product_id' => $items[0]['product_id'],
                'quantity'   => $items->sum('quantity')
            ])
            ->values();

        DB::beginTransaction();

        try {
            $subTotal = 0;
            $resolvedItems = [];

            foreach ($cart as $item) {

                $attribute = ProductsAttribute::where([
                    'product_id' => $item['product_id'],
                    'vendor_id'  => $vendorId
                ])->lockForUpdate()->first();

                if (!$attribute || $attribute->stock < $item['quantity']) {
                    throw new \Exception('Insufficient stock for product ID: ' . $item['product_id']);
                }

                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new \Exception('Product not found. ID: ' . $item['product_id']);
                }

                // ✅ SAME PRICE LOGIC AS WEBSITE
                $basePrice       = $attribute->price ?? $product->product_price;
                $discountPercent = $attribute->product_discount ?? 0;

                $discountAmount = round(($basePrice * $discountPercent) / 100);
                $finalPrice     = round($basePrice - $discountAmount);

                $lineTotal = $finalPrice * $item['quantity'];
                $subTotal += $lineTotal;

                $resolvedItems[] = [
                    'product'     => $product,
                    'attribute'   => $attribute,
                    'final_price' => $finalPrice,
                    'quantity'    => $item['quantity']
                ];
            }

            /* ================= EXTRA DISCOUNT (SUBTOTAL) ================= */
            $extraDiscount = min($request->extra_discount ?? 0, $subTotal);

            /* ================= COUPON (SUBTOTAL) ================= */
            $couponAmount = 0;
            $couponCode   = null;

            if ($request->coupon_code) {
                $coupon = Coupon::where('coupon_code', $request->coupon_code)
                    ->where('status', 1)
                    ->whereDate('expiry_date', '>=', now())
                    ->first();

                if (!$coupon) {
                    throw new \Exception('Invalid or expired coupon');
                }

                if ($coupon->amount_type === 'percent') {
                    $couponAmount = round(($subTotal * $coupon->amount) / 100);
                } else {
                    $couponAmount = $coupon->amount;
                }

                $couponAmount = min($couponAmount, $subTotal);
                $couponCode   = $coupon->coupon_code;
            }

            /* ================= GRAND TOTAL ================= */
            $grandTotal = max(0, $subTotal - $extraDiscount - $couponAmount);

            /* ================= CREATE ORDER ================= */
            $order = Order::create([
                'user_id'          => 0,
                'name'             => $request->customer_name,
                'mobile'           => $request->customer_mobile,
                'email'            => $request->customer_email ?? 'N/A',
                'address'          => $request->customer_address ?? 'N/A',
                'city'             => 'N/A',
                'state'            => 'N/A',
                'country'          => 'N/A',
                'pincode'          => 'N/A',
                'shipping_charges' => 0,

                'payment_method'   => 'Cash',
                'payment_gateway'  => 'Cash',
                'order_status'     => 'New',

                'coupon_code'      => $couponCode,
                'coupon_amount'    => $couponAmount,
                'extra_discount'   => $extraDiscount,

                'grand_total'      => $grandTotal
            ]);

            /* ================= ORDER ITEMS + STOCK ================= */
            foreach ($resolvedItems as $item) {

                $item['attribute']->decrement('stock', $item['quantity']);

                OrdersProduct::create([
                    'order_id'      => $order->id,
                    'user_id'       => 0,
                    'admin_id'      => $adminId,
                    'vendor_id'     => $vendorId,
                    'product_id'    => $item['product']->id,
                    'product_name'  => $item['product']->product_name,
                    'product_price' => $item['final_price'],
                    'product_qty'   => $item['quantity'],
                    'item_status'   => 'New'
                ]);
            }

            DB::commit();

            return response()->json([
                'status'      => true,
                'message'     => 'Sale completed successfully',
                'order_id'    => $order->id,
                'sub_total'   => $subTotal,
                'extra_discount' => $extraDiscount,
                'coupon_discount' => $couponAmount,
                'grand_total' => $grandTotal
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
