<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\OrdersLog;
use App\Models\OrderStatus;
use App\Models\OrderItemStatus;
use App\Models\Product;
use App\Models\ProductsAttribute;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{


    // Render admin/orders/orders.blade.php page (Orders Management section) in the Admin Panel
    public function orders() {
        if (!Auth::guard('admin')->user()->can('view_orders')) {
            abort(403, 'Unauthorized action.');
        }        
        $user = Auth::guard('admin')->user();
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        
        // Correcting issues in the Skydash Admin Panel Sidebar using Session
        Session::put('page', 'orders');

        // Check user role using Spatie
        $isVendor = $user->hasRole('vendor');
        $isAdmin = $user->hasRole('admin');

        if ($isVendor) {
            // Check vendor status
            $vendorStatus = $user->status ?? 0;

            if ($vendorStatus == 0) {
                return redirect('admin/update-vendor-details/personal')
                    ->with('error_message', 'Your Vendor Account is not approved yet. Please make sure to fill your valid personal, business and bank details.');
            }

            // Get vendor_id from the vendor relationship
            $vendor_id = $user->vendor_id; // Using the accessor from User model

            // Show only orders containing this vendor's products
            $orders = Order::with([
                'orders_products' => function ($query) use ($vendor_id) {
                    $query->where('vendor_id', $vendor_id);
                }
            ])->orderBy('id', 'Desc')->get()->toArray();

        } else {
            // Admin or other roles - show ALL orders
            $orders = Order::with('orders_products')->orderBy('id', 'Desc')->get()->toArray();
        }

        // Pass role information to view
        $adminType = $isVendor ? 'vendor' : ($isAdmin ? 'admin' : 'user');

        return view('admin.orders.orders')->with(compact('orders', 'logos', 'headerLogo', 'adminType'));
    }

    // demo code
    public function orderDetails($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        Session::put('page', 'orders');

        $admin = Auth::guard('admin')->user();
        $isVendor = $admin->hasRole('vendor');
        $isAdmin = $admin->hasRole('admin');
        $vendor_id = $admin->vendor_id;

        // Vendor status check
        if ($isVendor && $admin->status == 0) {
            return redirect('admin/update-vendor-details/personal')
                ->with('error_message', 'Your Vendor Account is not approved yet. Please complete your details.');
        }

        if ($isVendor) {
            $order = Order::with([
                'orders_products' => function ($query) use ($vendor_id) {
                    $query->with(['product.attributes'])->where('vendor_id', $vendor_id);
                }
            ])->where('id', $id)->first();
        } else {
            $order = Order::with(['orders_products.product.attributes'])->where('id', $id)->first();
        }

        if (!$order) {
            abort(404, 'Order not found');
        }

        $orderDetails = $order->toArray();

        // Vendor must have products in this order
        if ($isVendor && empty($orderDetails['orders_products'])) {
            abort(403, 'You are not authorized to view this order');
        }

        if (!empty($orderDetails['user_id']) && $orderDetails['user_id'] > 0) {
            // Registered user
            $user = User::find($orderDetails['user_id']);

            if (!$user) {
                abort(404, 'User not found for this order');
            }

            $userDetails = $user->toArray();
        } else {
            // Guest checkout → use order table snapshot
            $userDetails = [
                'name'    => $orderDetails['name'],
                'email'   => $orderDetails['email'],
                'mobile'  => $orderDetails['mobile'],
                'address' => $orderDetails['address'],
                'city'    => $orderDetails['city'],
                'state'   => $orderDetails['state'],
                'country' => $orderDetails['country'],
                'pincode' => $orderDetails['pincode'],
            ];
        }

        $orderStatuses = OrderStatus::where('status', 1)->get()->toArray();
        $orderItemStatuses = OrderItemStatus::where('status', 1)->get()->toArray();

        $orderLog = OrdersLog::with('orders_products')
            ->where('order_id', $id)
            ->orderBy('id', 'DESC')
            ->get()
            ->toArray();


        $total_items = 0;
        foreach ($orderDetails['orders_products'] as $product) {
            $total_items += $product['product_qty'];
        }

        $totalDiscount = ($orderDetails['coupon_amount'] ?? 0) + ($orderDetails['extra_discount'] ?? 0);

        if ($totalDiscount > 0 && $total_items > 0) {
            $item_discount = round($totalDiscount / $total_items, 2);
        } else {
            $item_discount = 0;
        }

        // Set adminType for view compatibility
        $adminType = $isVendor ? 'vendor' : ($isAdmin ? 'admin' : 'user');

        return view('admin.orders.order_details')->with(compact(
            'orderDetails',
            'userDetails',
            'orderStatuses',
            'orderItemStatuses',
            'orderLog',
            'item_discount',
            'total_items',
            'logos',
            'headerLogo',
            'adminType'
        ));
    }

    public function updateOrderStatus(Request $request) {
        if (!Auth::guard('admin')->user()->can('update_order_status')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->isMethod('post')) {
            $data = $request->all();
            // dd($data);

            if (empty($data['courier_name']) && empty($data['tracking_number']) && $data['order_status'] == 'Shipped') { 

                $getResults = Order::pushOrder($data['order_id']);
                // dd($getResults);
                if (!isset($getResults['status']) || (isset($getResults['status']) && $getResults['status'] == false)) { // If Status is not coming at all, or it's coming but it's false
                    Session::put('error_message', $getResults['message']); // The message is coming from the Shiprocket API    // Storing Data: https://laravel.com/docs/9.x/session#storing-data

                    return redirect()->back(); 
                }
            }

            // Update Order Status in `orders` table
            Order::where('id', $data['order_id'])->update(['order_status' => $data['order_status']]);

            if (!empty($data['courier_name']) && !empty($data['tracking_number'])) { 
                Order::where('id', $data['order_id'])->update([
                    'courier_name'    => $data['courier_name'],
                    'tracking_number' => $data['tracking_number']
                ]);
            }
            $log = new OrdersLog;
            $log->order_id     = $data['order_id'];
            $log->order_status = $data['order_status'];
            $log->save();

            $deliveryDetails = Order::select('mobile', 'email', 'name')->where('id', $data['order_id'])->first()->toArray();
            $orderDetails    = Order::with('orders_products')->where('id', $data['order_id'])->first()->toArray(); 

            if (!empty($data['courier_name']) && !empty($data['tracking_number'])) { 
                $email = $deliveryDetails['email'];

                // The email message data/variables that will be passed in to the email view
                $messageData = [
                    'email'           => $email,
                    'name'            => $deliveryDetails['name'],
                    'order_id'        => $data['order_id'],
                    'orderDetails'    => $orderDetails,
                    'order_status'    => $data['order_status'],
                    'courier_name'    => $data['courier_name'],
                    'tracking_number' => $data['tracking_number']
                ];

                \Illuminate\Support\Facades\Mail::send('emails.order_status', $messageData, function ($message) use ($email) { 
                    $message->to($email)->subject('Order Status Updated - MultiVendorEcommerceApplication.com.eg');
                });
            } else { // if there are no Courier Name and Tracking Number data, don't include them in the email
                $email = $deliveryDetails['email'];

                // The email message data/variables that will be passed in to the email view
                $messageData = [
                    'email'        => $email,
                    'name'         => $deliveryDetails['name'],
                    'order_id'     => $data['order_id'],
                    'orderDetails' => $orderDetails,
                    'order_status' => $data['order_status']
                ];

                \Illuminate\Support\Facades\Mail::send('emails.order_status', $messageData, function ($message) use ($email) { 
                    $message->to($email)->subject('Order Status Updated - MultiVendorEcommerceApplication.com.eg');
                });
            }

            $message = 'Order Status has been updated successfully!';


            return redirect()->back()->with('success_message', $message);
            return view('admin.orders.orders', compact('orders', 'logos', 'headerLogo'));
        }
    }

    public function updateOrderItemStatus(Request $request) {
        if (!Auth::guard('admin')->user()->can('update_order_item_status')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->isMethod('post')) {
            $data = $request->all();
            // dd($data);

            // Update Order Item Status in `orders_products` table
            OrdersProduct::where('id', $data['order_item_id'])->update(['item_status' => $data['order_item_status']]);

            if (!empty($data['item_courier_name']) && !empty($data['item_tracking_number'])) { // if a 'vendor' or 'admin' updates the order Item Status to 'Shipped' in admin/orders/order_details.blade.php, and submits both Courier Name and Tracking Number HTML input fields
                OrdersProduct::where('id', $data['order_item_id'])->update([
                    'courier_name'    => $data['item_courier_name'],
                    'tracking_number' => $data['item_tracking_number']
                ]);
            }


            // Get the `order_id` column (which is the foreign key to the `id` column in `orders` table) value from `orders_products` table
            $getOrderId = OrdersProduct::select('order_id')->where('id', $data['order_item_id'])->first()->toArray();

            $log = new OrdersLog;
            $log->order_id      = $getOrderId['order_id'];
            $log->order_item_id = $data['order_item_id'];
            $log->order_status  = $data['order_item_status'];
            $log->save();

            $deliveryDetails = Order::select('mobile', 'email', 'name')->where('id', $getOrderId['order_id'])->first()->toArray();

            $order_item_id = $data['order_item_id'];
            $orderDetails  = Order::with([ 
                'orders_products' => function ($query) use ($order_item_id) { 
                    $query->where('id', $order_item_id); // `id` column in `orders_products` table
                }
            ])->where('id', $getOrderId['order_id'])->first()->toArray(); 

            if (!empty($data['item_courier_name']) && !empty($data['item_tracking_number'])) { 
                $email = $deliveryDetails['email'];

                // The email message data/variables that will be passed in to the email view
                $messageData = [
                    'email'           => $email,
                    'name'            => $deliveryDetails['name'],
                    'order_id'        => $getOrderId['order_id'],
                    'orderDetails'    => $orderDetails,
                    'order_status'    => $data['order_item_status'],
                    'courier_name'    => $data['item_courier_name'],
                    'tracking_number' => $data['item_tracking_number']
                ];

                \Illuminate\Support\Facades\Mail::send('emails.order_item_status', $messageData, function ($message) use ($email) { // Sending Mail: https://laravel.com/docs/9.x/mail#sending-mail    // 'emails.order_item_status' is the order_item_status.blade.php file inside the 'resources/views/emails' folder that will be sent as an email    // We pass in all the variables that order_item_status.blade.php will use    // https://www.php.net/manual/en/functions.anonymous.php
                    $message->to($email)->subject('Order Item Status Updated - MultiVendorEcommerceApplication.com.eg');
                });
            } else { // if there are no Courier Name and Tracking Number data, don't include them in the email
                $email = $deliveryDetails['email'];

                // The email message data/variables that will be passed in to the email view
                $messageData = [
                    'email'        => $email,
                    'name'         => $deliveryDetails['name'],
                    'order_id'     => $getOrderId['order_id'],
                    'orderDetails' => $orderDetails,
                    'order_status' => $data['order_item_status']
                ];

                \Illuminate\Support\Facades\Mail::send('emails.order_item_status', $messageData, function ($message) use ($email) { // Sending Mail: https://laravel.com/docs/9.x/mail#sending-mail    // 'emails.order_item_status' is the order_item_status.blade.php file inside the 'resources/views/emails' folder that will be sent as an email    // We pass in all the variables that order_item_status.blade.php will use    // https://www.php.net/manual/en/functions.anonymous.php
                    $message->to($email)->subject('Order Item Status Updated - MultiVendorEcommerceApplication.com.eg');
                });
            }

            $message = 'Order Item Status has been updated successfully!';


            return redirect()->back()->with('success_message', $message);
            return view('admin.orders.orders', compact('orders', 'logos', 'headerLogo'));
        }
    }

    // demo code 3

    public function viewOrderInvoice($order_id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        $order = Order::with('orders_products.product.attributes')
            ->where('id', $order_id)
            ->first();

        if (!$order) {
            abort(404, 'Order not found');
        }

        $orderDetails = $order->toArray();

        if (!empty($orderDetails['user_id']) && $orderDetails['user_id'] > 0) {
            // Registered user
            $user = User::find($orderDetails['user_id']);

            if (!$user) {
                abort(404, 'User not found for this order');
            }

            $userDetails = $user->toArray();
        } else {
            // Guest checkout → use order table snapshot
            $userDetails = [
                'name'    => $orderDetails['name'],
                'email'   => $orderDetails['email'],
                'phone'  => $orderDetails['mobile'],
                'address' => $orderDetails['address'],
                'city'    => $orderDetails['city'],
                'state'   => $orderDetails['state'],
                'country' => $orderDetails['country'],
                'pincode' => $orderDetails['pincode'],
            ];
        }

        return view('admin.orders.order_invoice')->with(compact(
            'orderDetails',
            'userDetails',
            'logos',
            'headerLogo'
        ));
    }

    // demo code2
    public function viewPDFInvoice($order_id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();


        $order = Order::with('orders_products.product.attributes')->where('id', $order_id)->first();

        if (!$order) {
            abort(404, 'Order not found');
        }

        $orderDetails = $order->toArray();


        if (!empty($orderDetails['user_id']) && $orderDetails['user_id'] > 0) {
            // Registered user
            $user = User::find($orderDetails['user_id']);

            if (!$user) {
                abort(404, 'User not found for this order');
            }

            $userDetails = $user->toArray();
        } else {
            // Guest checkout → take snapshot from orders table
            $userDetails = [
                'name'    => $orderDetails['name'],
                'email'   => $orderDetails['email'],
                'phone'  => $orderDetails['mobile'],
                'address' => $orderDetails['address'],
                'city'    => $orderDetails['city'],
                'state'   => $orderDetails['state'],
                'country' => $orderDetails['country'],
                'pincode' => $orderDetails['pincode'],
            ];
        }


        $invoiceHTML = '
<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; }
        th { background: #cf8938; color: #fff; }
    </style>
</head>
<body>

<h2>Invoice</h2>

<p>
<b>Name:</b> ' . $userDetails['name'] . '<br>
<b>Email:</b> ' . $userDetails['email'] . '<br>
<b>Mobile:</b> ' . $userDetails['phone'] . '<br>
<b>Address:</b> ' . $userDetails['address'] . '<br>
</p>

<p>
<b>Order ID:</b> ' . $orderDetails['id'] . '<br>
<b>Order Date:</b> ' . date('Y-m-d H:i:s', strtotime($orderDetails['created_at'])) . '<br>
<b>Payment Method:</b> ' . $orderDetails['payment_method'] . '<br>
<b>Status:</b> ' . $orderDetails['order_status'] . '
</p>

<table border="1" width="100%" cellpadding="5" cellspacing="0">
<thead>
<tr>
    <th>Product</th>
    <th>MRP</th>
    <th>Disc.</th>
    <th>Price</th>
    <th>Qty</th>
    <th>Total</th>
</tr>
</thead>
<tbody>';

        $subTotal = 0;

        foreach ($orderDetails['orders_products'] as $product) {
            $lineTotal = $product['product_price'] * $product['product_qty'];
            $subTotal += $lineTotal;

            $mrp = $product['product']['product_price'] ?? 'N/A';
            $prodAttr = collect($product['product']['attributes'] ?? [])->where('vendor_id', $product['vendor_id'])->first();
            $disc = ($prodAttr['product_discount'] ?? 0) . '%';

            $invoiceHTML .= '
<tr>
    <td>' . $product['product_name'] . '</td>
    <td style="text-align:center;">' . ($mrp != 'N/A' ? 'INR ' . $mrp : $mrp) . '</td>
    <td style="text-align:center;">' . $disc . '</td>
    <td style="text-align:center;">INR ' . $product['product_price'] . '</td>
    <td style="text-align:center;">' . $product['product_qty'] . '</td>
    <td style="text-align:right;">INR ' . $lineTotal . '</td>
</tr>';
        }

        $invoiceHTML .= '
</tbody>
</table>

<div style="text-align: right; margin-top: 20px;">
    <p><b>Subtotal:</b> INR ' . $subTotal . '</p>';

        if (!empty($orderDetails['coupon_amount']) && $orderDetails['coupon_amount'] > 0) {
            $invoiceHTML .= '<p><b>Coupon Discount (' . ($orderDetails['coupon_code'] ?? 'N/A') . '):</b> - INR ' . $orderDetails['coupon_amount'] . '</p>';
        }

        if (!empty($orderDetails['extra_discount']) && $orderDetails['extra_discount'] > 0) {
            $invoiceHTML .= '<p><b>Extra Discount:</b> - INR ' . $orderDetails['extra_discount'] . '</p>';
        }

        $invoiceHTML .= '
    <p style="font-size: 16px; border-top: 1px solid #ccc; padding-top: 10px;">
        <b>Grand Total:</b> INR ' . $orderDetails['grand_total'] . '
    </p>
</div>

<p>Thank you for your order.</p>

</body>
</html>';


        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($invoiceHTML);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('invoice-' . $orderDetails['id'] . '.pdf');
    }



    // Sales Concept - Display page
    public function salesConcept()
    {
        $headerLogo = HeaderLogo::first();
        $logos      = HeaderLogo::first();

        Session::put('page', 'sales_concept');

        // ✅ ALWAYS initialize
        $cart       = Session::get('sales_cart', []);
        $couponData = Session::get('sales_coupon', null);

        return view('admin.orders.sales_concept', compact(
            'cart',
            'logos',
            'headerLogo',
            'couponData'
        ));
    }

    // Sales Concept - Search book by ISBN
    public function searchBookByIsbn(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string|max:20'
        ]);

        $admin = Auth::guard('admin')->user();
        $isVendor = $admin->hasRole('vendor');

        $product = Product::where('product_isbn', $request->isbn)->first();

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Book not found'
            ], 404);
        }

        $attribute = ProductsAttribute::where('product_id', $product->id)
            ->when($isVendor, function ($q) use ($admin) {
                $q->where('vendor_id', $admin->vendor_id);
            })
            ->first();

        if (!$attribute) {
            return response()->json([
                'status' => false,
                'message' => 'Product not available in inventory'
            ], 404);
        }

        $basePrice       = $product->product_price;
        $discountPercent = $attribute->product_discount ?? 0;
        $discountAmount  = round(($basePrice * $discountPercent) / 100);
        $finalPrice      = round($basePrice - $discountAmount, 2);

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
                // 'product_image'    => $product->product_image ?? ''
            ]
        ]);
    }

    // Sales Concept - Add to cart (session)
    public function addToSalesCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1'
        ]);

        $product   = Product::findOrFail($request->product_id);
        $attribute = ProductsAttribute::where('product_id', $product->id)->first();

        if ($attribute->stock < $request->quantity) {
            return response()->json(['status' => false, 'message' => 'Insufficient stock'], 400);
        }

        // Calculate discount
        $originalPrice   = $product->product_price;
        $discountPercent = $attribute->product_discount ?? 0;
        $discountAmount  = round(($originalPrice * $discountPercent) / 100);
        $discountedPrice = round($originalPrice - $discountAmount);

        $cart  = session()->get('sales_cart', []);
        $found = false;

        foreach ($cart as &$item) {
            if ($item['product_id'] == $product->id) {
                $item['quantity'] += $request->quantity;
                $item['total']     = $item['price'] * $item['quantity'];
                $found = true;
            }
        }

        if (!$found) {
            $cart[] = [
                'product_id'      => $product->id,
                'product_name'    => $product->product_name,
                'product_isbn'    => $product->product_isbn,
                'original_price'  => $originalPrice,
                'discount_percent'=> $discountPercent,
                'price'           => $discountedPrice, // Use discounted price
                'quantity'        => $request->quantity,
                'total'           => $discountedPrice * $request->quantity
            ];
        }

        session()->put('sales_cart', $cart);

        // 🔥 reset discounts on cart change
        session()->forget([
            'sales_coupon',
            'sales_extra_discount_amount'
        ]);

        return response()->json(['status' => true, 'message' => 'Added to cart']);
    }

    public function applyExtraDiscount(Request $request)
    {
        $request->validate([
            'extra_discount' => 'required|numeric|min:0'
        ]);

        $cart = session()->get('sales_cart', []);
        if (empty($cart)) {
            return response()->json(['status' => false, 'message' => 'Cart is empty'], 400);
        }

        $subTotal = array_sum(array_column($cart, 'total'));

        // Extra discount as fixed amount
        $amount = round($request->extra_discount, 2);
        
        // Ensure discount doesn't exceed subtotal
        if ($amount > $subTotal) {
            return response()->json([
                'status' => false, 
                'message' => 'Discount amount cannot exceed subtotal (₹' . $subTotal . ')'
            ], 400);
        }

        session()->put('sales_extra_discount_amount', $amount);

        // 🔥 RE-CALCULATE COUPON IF EXISTS
        $coupon = session('sales_coupon');
        if ($coupon) {
            $afterExtra = $subTotal - $amount;

            if ($coupon['type'] === 'Percentage') {
                $couponDiscount = round(($afterExtra * $coupon['value']) / 100);
            } else {
                $couponDiscount = $coupon['value'];
            }

            $couponDiscount = min($couponDiscount, $afterExtra);

            session()->put('sales_coupon.discount', $couponDiscount);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Extra discount applied'
        ]);
    }


    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50'
        ]);

        $admin = Auth::guard('admin')->user();

        $coupon = Coupon::where('coupon_code', $request->coupon_code)
            ->where('vendor_id', $admin->vendor_id)
            ->where('status', 1)
            ->whereDate('expiry_date', '>=', now())
            ->first();

        if (!$coupon) {
            return response()->json(['status' => false, 'message' => 'Invalid or expired coupon'], 400);
        }

        $cart = session()->get('sales_cart', []);
        if (empty($cart)) {
            return response()->json(['status' => false, 'message' => 'Cart is empty'], 400);
        }

        $subTotal      = array_sum(array_column($cart, 'total'));
        $extraDiscount = session('sales_extra_discount_amount', 0);
        $afterExtra    = max(0, $subTotal - $extraDiscount);

        if ($coupon->amount_type === 'Percentage') {
            $discount = round(($afterExtra * $coupon->amount) / 100);
        } else {
            $discount = $coupon->amount;
        }

        $discount = min($discount, $afterExtra);

        session()->put('sales_coupon', [
            'code'     => $coupon->coupon_code,
            'type'     => $coupon->amount_type,
            'value'    => $coupon->amount,
            'discount' => $discount
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Coupon applied successfully'
        ]);
    }

    // Sales Concept - Remove from cart
    public function removeFromSalesCart(Request $request)
    {
        $cart = array_values(array_filter(
            session()->get('sales_cart', []),
            fn($item) => $item['product_id'] != $request->product_id
        ));

        session()->put('sales_cart', $cart);

        if (empty($cart)) {
            session()->forget([
                'sales_coupon',
                'sales_extra_discount_amount'
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function processSale(Request $request)
    {
        $request->validate([
            'customer_name'    => 'string|max:255',
            'customer_mobile'  => 'required|string|max:20',
            'customer_email'   => 'nullable|email',
            'customer_address' => 'nullable|string'
        ]);

        $cart = session()->get('sales_cart', []);
        if (empty($cart)) {
            return back()->with('error_message', 'Cart is empty');
        }

        DB::beginTransaction();
        try {

            // 1️⃣ Sub total
            $subTotal = array_sum(array_column($cart, 'total'));

            // 2️⃣ Extra discount (AMOUNT only)
            $extraDiscount = session('sales_extra_discount_amount', 0);
            $extraDiscount = min($extraDiscount, $subTotal);

            $afterExtra = $subTotal - $extraDiscount;

            // 3️⃣ Coupon (AMOUNT only)
            $coupon       = session('sales_coupon');
            $couponAmount = $coupon ? min($coupon['discount'], $afterExtra) : 0;
            $couponCode   = $coupon['code'] ?? null;

            // 4️⃣ Final total
            $grandTotal = max(0, $afterExtra - $couponAmount);

            // 5️⃣ Create Order
            $order = Order::create([
                'user_id'          => 0,
                'name'             => 'N/A',
                'mobile'           => $request->customer_mobile,
                'email'            => 'N/A',
                'address'          => 'N/A',
                'city'             => 'N/A',
                'state'            => 'N/A',
                'country'          => 'N/A',
                'pincode'          => 'N/A',
                'shipping_charges' => 0,
                'payment_method'   => 'Cash',
                'payment_gateway'  => 'Cash',
                'order_status'     => 'New',

                // ✅ FIXED FIELDS
                'coupon_code'      => $couponCode,
                'coupon_amount'    => $couponAmount,
                'extra_discount'   => $extraDiscount,
                'grand_total'      => $grandTotal
            ]);

            // 6️⃣ Order items + stock update
            foreach ($cart as $item) {

                $admin = Auth::guard('admin')->user();
                $isVendor = $admin->hasRole('vendor');

                $attrQuery = ProductsAttribute::where('product_id', $item['product_id']);

                if ($isVendor) {
                    $attrQuery->where('vendor_id', $admin->vendor_id);
                }

                $attr = $attrQuery->lockForUpdate()->first();

                if (!$attr || $attr->stock < $item['quantity']) {
                    throw new \Exception(
                        'Insufficient stock for ' . $item['product_name']
                    );
                }

                // ✅ THIS WILL NOW UPDATE CORRECTLY
                $attr->decrement('stock', $item['quantity']);

                OrdersProduct::create([
                    'order_id'      => $order->id,
                    'user_id'       => 0,
                    'admin_id'      => (!$isVendor) ? $admin->id : 0,
                    'vendor_id'     => ($isVendor) ? $admin->vendor_id : 0,
                    'product_id'    => $item['product_id'],
                    'product_name'  => $item['product_name'],
                    'product_price' => $item['price'],
                    'product_qty'   => $item['quantity'],
                    'item_status'   => 'New'
                ]);
            }


            DB::commit();

            // 7️⃣ Clear session
            session()->forget([
                'sales_cart',
                'sales_coupon',
                'sales_extra_discount_amount'
            ]);

            return redirect()->back()
                ->with('success_message', 'Sale completed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error_message', $e->getMessage());
        }
    }
}
