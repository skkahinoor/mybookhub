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

    // public function cart(Request $request)
    // {
    //     if ($resp = $this->checkAccess($request)) return $resp;

    //     return response()->json([
    //         'status' => true,
    //         'cart' => Session::get('sales_cart', [])
    //     ]);
    // }

    // public function searchByIsbn(Request $request)
    // {
    //     if ($resp = $this->checkAccess($request)) return $resp;

    //     $request->validate([
    //         'isbn' => 'required|string|max:20'
    //     ]);

    //     $vendorId = $request->user()->vendor_id;

    //     $product = Product::where('product_isbn', $request->isbn)->first();

    //     if (!$product) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Book not found'
    //         ], 404);
    //     }

    //     $attribute = ProductsAttribute::where([
    //         'product_id' => $product->id,
    //         'vendor_id' => $vendorId
    //     ])->first();

    //     if (!$attribute) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Product not available for this vendor'
    //         ], 404);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'data' => [
    //             'product_id' => $product->id,
    //             'product_name' => $product->product_name,
    //             'product_isbn' => $product->product_isbn,
    //             'price' => $attribute->price ?? $product->product_price,
    //             'stock' => $attribute->stock,
    //             'image' => $product->product_image
    //         ]
    //     ]);
    // }

    // public function addToCart(Request $request)
    // {
    //     if ($resp = $this->checkAccess($request)) return $resp;

    //     $request->validate([
    //         'product_id' => 'required|integer',
    //         'quantity' => 'required|integer|min:1'
    //     ]);

    //     $vendorId = $request->user()->vendor_id;

    //     $attribute = ProductsAttribute::where([
    //         'product_id' => $request->product_id,
    //         'vendor_id' => $vendorId
    //     ])->first();

    //     if (!$attribute || $attribute->stock < $request->quantity) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Insufficient stock'
    //         ], 400);
    //     }

    //     $product = Product::find($request->product_id);
    //     $price = $attribute->price ?? $product->product_price;

    //     $cart = Session::get('sales_cart', []);

    //     foreach ($cart as &$item) {
    //         if ($item['product_id'] == $request->product_id) {
    //             $item['quantity'] += $request->quantity;
    //             $item['total'] = $item['quantity'] * $price;
    //             Session::put('sales_cart', $cart);

    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'Cart updated',
    //                 'cart' => $cart
    //             ]);
    //         }
    //     }

    //     $cart[] = [
    //         'product_id' => $product->id,
    //         'product_name' => $product->product_name,
    //         'price' => $price,
    //         'quantity' => $request->quantity,
    //         'total' => $price * $request->quantity
    //     ];

    //     Session::put('sales_cart', $cart);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Added to cart',
    //         'cart' => $cart
    //     ]);
    // }

    // public function removeFromCart(Request $request)
    // {
    //     if ($resp = $this->checkAccess($request)) return $resp;

    //     $request->validate([
    //         'product_id' => 'required|integer'
    //     ]);

    //     $cart = array_values(array_filter(
    //         Session::get('sales_cart', []),
    //         fn ($item) => $item['product_id'] != $request->product_id
    //     ));

    //     Session::put('sales_cart', $cart);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Item removed',
    //         'cart' => $cart
    //     ]);
    // }

    // public function processSale(Request $request)
    // {
    //     if ($resp = $this->checkAccess($request)) return $resp;

    //     $request->validate([
    //         'customer_name' => 'required|string|max:255',
    //         'customer_mobile' => 'required|string|max:20',
    //         'customer_email' => 'nullable|email',
    //         'customer_address' => 'nullable|string'
    //     ]);

    //     $cart = Session::get('sales_cart', []);

    //     if (empty($cart)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Cart is empty'
    //         ], 400);
    //     }

    //     DB::beginTransaction();

    //     try {
    //         $vendorId = $request->user()->vendor_id;
    //         $adminId = $request->user()->id;

    //         $grandTotal = collect($cart)->sum('total');

    //         $order = Order::create([
    //             'user_id' => 0,
    //             'name' => $request->customer_name,
    //             'address' => $request->customer_address ?? 'N/A',
    //             'city' => 'N/A',
    //             'state' => 'N/A',
    //             'country' => 'N/A',
    //             'pincode' => 'N/A',
    //             'mobile' => $request->customer_mobile,
    //             'email' => $request->customer_email ?? 'N/A',
    //             'shipping_charges' => 0,
    //             'order_status' => 'New',
    //             'payment_method' => 'Cash',
    //             'payment_gateway' => 'Cash',
    //             'grand_total' => $grandTotal
    //         ]);

    //         foreach ($cart as $item) {
    //             $attribute = ProductsAttribute::where([
    //                 'product_id' => $item['product_id'],
    //                 'vendor_id' => $vendorId
    //             ])->lockForUpdate()->first();

    //             if ($attribute->stock < $item['quantity']) {
    //                 throw new \Exception('Stock issue');
    //             }

    //             $attribute->decrement('stock', $item['quantity']);

    //             OrdersProduct::create([
    //                 'order_id' => $order->id,
    //                 'user_id' => 0,
    //                 'admin_id' => $adminId,
    //                 'vendor_id' => $vendorId,
    //                 'product_id' => $item['product_id'],
    //                 'product_name' => $item['product_name'],
    //                 'product_price' => $item['price'],
    //                 'product_qty' => $item['quantity']
    //             ]);
    //         }

    //         DB::commit();
    //         Session::forget('sales_cart');

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Sale completed',
    //             'order_id' => $order->id
    //         ]);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

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
                'status' => false,
                'message' => 'Book not found'
            ], 404);
        }

        $attribute = ProductsAttribute::where([
            'product_id' => $product->id,
            'vendor_id'  => $vendorId
        ])->first();

        if (!$attribute) {
            return response()->json([
                'status' => false,
                'message' => 'Product not available for this vendor'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'product_id'   => $product->id,
                'product_name' => $product->product_name,
                'product_isbn' => $product->product_isbn,
                'price'        => $attribute->price ?? $product->product_price,
                'stock'        => $attribute->stock,
                'image'        => $product->product_image
            ]
        ], 200);
    }

    public function processSale(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $request->validate([
            'customer_name'     => 'required|string|max:255',
            'customer_mobile'   => 'required|string|max:20',
            'customer_email'    => 'nullable|email',
            'customer_address'  => 'nullable|string',

            'cart' => 'required|array|min:1',
            'cart.*.product_id' => 'required|integer',
            'cart.*.quantity'   => 'required|integer|min:1'
        ]);

        $cart = collect($request->cart)
            ->groupBy('product_id')
            ->map(fn($items) => [
                'product_id' => $items[0]['product_id'],
                'quantity'   => $items->sum('quantity')
            ])
            ->values();

        DB::beginTransaction();

        try {
            $vendorId = $request->user()->vendor_id;
            $adminId  = $request->user()->id;

            $grandTotal = 0;
            $resolvedItems = [];

            foreach ($cart as $item) {

                $attribute = ProductsAttribute::where([
                    'product_id' => $item['product_id'],
                    'vendor_id'  => $vendorId
                ])->lockForUpdate()->first();

                if (!$attribute) {
                    throw new \Exception(
                        'Product not available for vendor. Product ID: ' . $item['product_id']
                    );
                }

                if ($attribute->stock < $item['quantity']) {
                    throw new \Exception(
                        'Insufficient stock for product ID: ' . $item['product_id']
                    );
                }

                $product = Product::find($item['product_id']);

                if (!$product) {
                    throw new \Exception(
                        'Product not found. Product ID: ' . $item['product_id']
                    );
                }

                /**
                 * 🔥 PRICE RESOLUTION (CRITICAL FIX)
                 * Vendor price → fallback to base product price
                 */
                $price = $attribute->price ?? $product->product_price;

                if ($price === null) {
                    throw new \Exception(
                        'Price not defined for product ID: ' . $item['product_id']
                    );
                }

                $resolvedItems[] = [
                    'product'   => $product,
                    'attribute' => $attribute,
                    'price'     => $price,
                    'quantity'  => $item['quantity']
                ];

                $grandTotal += ($price * $item['quantity']);
            }

            /* =========================
           CREATE ORDER
        ========================== */
            $order = Order::create([
                'user_id'           => 0,
                'name'              => $request->customer_name,
                'address'           => $request->customer_address ?? 'N/A',
                'city'              => 'N/A',
                'state'             => 'N/A',
                'country'           => 'N/A',
                'pincode'           => 'N/A',
                'mobile'            => $request->customer_mobile,
                'email'             => $request->customer_email ?? 'N/A',
                'shipping_charges'  => 0,
                'order_status'      => 'New',
                'payment_method'    => 'Cash',
                'payment_gateway'   => 'Cash',
                'grand_total'       => $grandTotal
            ]);

            /* =========================
           CREATE ORDER ITEMS + UPDATE STOCK
        ========================== */
            foreach ($resolvedItems as $item) {

                // Update stock
                $item['attribute']->decrement('stock', $item['quantity']);

                // Create order product
                OrdersProduct::create([
                    'order_id'      => $order->id,
                    'user_id'       => 0,
                    'admin_id'      => $adminId,
                    'vendor_id'     => $vendorId,
                    'product_id'    => $item['product']->id,
                    'product_name'  => $item['product']->product_name,
                    'product_price' => $item['price'],   // ✅ NEVER NULL
                    'product_qty'   => $item['quantity'],
                    'item_status'   => 'New'
                ]);
            }

            DB::commit();

            return response()->json([
                'status'   => true,
                'message'  => 'Sale completed successfully',
                'order_id' => $order->id
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
