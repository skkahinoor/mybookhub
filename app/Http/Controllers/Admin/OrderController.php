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
    // Note: In the Admin Panel, in the Orders Management section, if the authenticated/logged-in user is 'vendor', we'll show the orders of the products added by/related to that 'vendor' ONLY, but if the authenticated/logged-in user is 'admin', we'll show ALL orders



    // Render admin/orders/orders.blade.php page (Orders Management section) in the Admin Panel
    public function orders()
    {
        $adminType = Auth::guard('admin')->user()->type;
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        // Correcting issues in the Skydash Admin Panel Sidebar using Session
        Session::put('page', 'orders');


        // We determine the authenticated/logged-in user. If the authenticated/logged-in user is 'vendor', we show ONLY the orders of the products added by that specific 'vendor' ONLY, but if the authenticated/logged-in user is 'admin', we show ALL orders
        $adminType = Auth::guard('admin')->user()->type;      // `type`      is the column in `admins` table    // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances    // Retrieving The Authenticated User and getting their `type`      column in `admins` table
        $vendor_id = Auth::guard('admin')->user()->vendor_id; // `vendor_id` is the column in `admins` table    // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances    // Retrieving The Authenticated User and getting their `vendor_id` column in `admins` table


        if ($adminType == 'vendor') { // if the authenticated user (the logged in user) is 'vendor', check his `status`
            $vendorStatus = Auth::guard('admin')->user()->status; // `status` is the column in `admins` table    // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances    // Retrieving The Authenticated User and getting their `status` column in `admins` table

            if ($vendorStatus == 0) { // if the 'vendor' is inactive/disabled
                return redirect('admin/update-vendor-details/personal')->with('error_message', 'Your Vendor Account is not approved yet. Please make sure to fill your valid personal, business and bank details.'); // the error_message will appear to the vendor in the route: 'admin/update-vendor-details/personal' which is the update_vendor_details.blade.php page
            }
        }


        if ($adminType == 'vendor') { // If the authenticated/logged-in user is 'vendor', we show ONLY the orders of the products added by that specific 'vendor' ONLY
            $orders = Order::with([ // Eager Loading: https://laravel.com/docs/9.x/eloquent-relationships#eager-loading    // 'orders_products' is the relationship method name in Order.php model    // Constraining Eager Loads: https://laravel.com/docs/9.x/eloquent-relationships#constraining-eager-loads    // Subquery Where Clauses: https://laravel.com/docs/9.x/queries#subquery-where-clauses    // Advanced Subqueries: https://laravel.com/docs/9.x/eloquent#advanced-subqueries
                'orders_products' => function ($query) use ($vendor_id) { // function () use ()     syntax: https://www.php.net/manual/en/functions.anonymous.php#:~:text=the%20use%20language%20construct     // 'orders_products' is the Relationship method name in Order.php model
                    $query->where('vendor_id', $vendor_id); // `vendor_id` in `orders_products` table
                }
            ])->orderBy('id', 'Desc')->get()->toArray();
            // dd($orders);

        } else { // if the authenticated/logged-in user is 'admin', we show ALL orders
            $orders = Order::with('orders_products')->orderBy('id', 'Desc')->get()->toArray(); // Eager Loading: https://laravel.com/docs/9.x/eloquent-relationships#eager-loading    // 'orders_products' is the relationship method name in Order.php model
            // dd($orders);
        }


        return view('admin.orders.orders')->with(compact('orders', 'logos', 'headerLogo', 'adminType'));
    }



    // demo code
    public function orderDetails($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        Session::put('page', 'orders');

        $admin = Auth::guard('admin')->user();
        $adminType = $admin->type;
        $vendor_id = $admin->vendor_id;

        // Vendor status check
        if ($adminType === 'vendor' && $admin->status == 0) {
            return redirect('admin/update-vendor-details/personal')
                ->with('error_message', 'Your Vendor Account is not approved yet. Please complete your details.');
        }

        if ($adminType === 'vendor') {
            $order = Order::with([
                'orders_products' => function ($query) use ($vendor_id) {
                    $query->where('vendor_id', $vendor_id);
                }
            ])->where('id', $id)->first();
        } else {
            $order = Order::with('orders_products')->where('id', $id)->first();
        }

        if (!$order) {
            abort(404, 'Order not found');
        }

        $orderDetails = $order->toArray();

        // Vendor must have products in this order
        if ($adminType === 'vendor' && empty($orderDetails['orders_products'])) {
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

        if ($orderDetails['coupon_amount'] > 0 && $total_items > 0) {
            $item_discount = round($orderDetails['coupon_amount'] / $total_items, 2);
        } else {
            $item_discount = 0;
        }


        return view('admin.orders.order_details')->with(compact(
            'orderDetails',
            'userDetails',
            'orderStatuses',
            'orderItemStatuses',
            'orderLog',
            'item_discount',
            'logos',
            'headerLogo',
            'adminType'
        ));
    }


    public function updateOrderStatus(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->isMethod('post')) {
            $data = $request->all();
            // dd($data);

            // Note: There are two types of Shipping Process: "manual" and "automatic". "Manual" is in the case like small businesses, where the courier arrives at the owner warehouse to to pick up the order for shipping, and the small business owner takes the shipment details (like courier name, tracking number, ...) from the courier, and inserts those details themselves in the Admin Panel when they "Update Order Status" Section (by an 'admin') or "Update Item Status" Section (by a 'vendor' or 'admin') (in admin/orders/order_details.blade.php). With "automatic" shipping process, we're integrating third-party APIs (e.g. Shiprocket API) and orders go directly to the shipping partner, and the updates comes from the courier's end, and orders are automatically delivered to customers
            // "Automatic" Shipping Process (when 'admin' does NOT enter the Courier Name and Tracking Number): Configure the Shiprocket API in our Admin Panel in admin/orders/order_details.blade.php (to automate Pushing Orders to Shiprocket API by selecting "Shipped" from the drop-down menu)
            if (empty($data['courier_name']) && empty($data['tracking_number']) && $data['order_status'] == 'Shipped') { // if the 'admin' didn't enter the Courier Name and Tracking Nubmer when they selected "Shipped" from the drop-down menu in admin/orders/order_details.blade.php, use the "Automatic" Shipping Process (Push Orders to Shiprocket API), not the "Manual" Shipping process. Check the "Manual" Shipping process in the next if statement
                // dd('Inside Automatic Shipping Process if statement in updateOrderStatus() method in Admin/OrderController.php<br>');
                // echo 'Inside Automatic Shipping Process if statement in updateOrderStatus() method in Admin/OrderController.php<br>';
                // exit;

                $getResults = Order::pushOrder($data['order_id']);
                // dd($getResults);
                if (!isset($getResults['status']) || (isset($getResults['status']) && $getResults['status'] == false)) { // If Status is not coming at all, or it's coming but it's false
                    Session::put('error_message', $getResults['message']); // The message is coming from the Shiprocket API    // Storing Data: https://laravel.com/docs/9.x/session#storing-data

                    return redirect()->back(); // Redirecting With Flashed Session Data: https://laravel.com/docs/10.x/responses#redirecting-with-flashed-session-data
                    // return redirect()->back()->with('error_message', $getResults['message']); // Redirecting With Flashed Session Data: https://laravel.com/docs/10.x/responses#redirecting-with-flashed-session-data
                }
            }


            // Update Order Status in `orders` table
            Order::where('id', $data['order_id'])->update(['order_status' => $data['order_status']]);


            // Note: There are two types of Shipping Process: "manual" and "automatic". "Manual" is in the case like small businesses, where the courier arrives at the owner warehouse to to pick up the order for shipping, and the small business owner takes the shipment details (like courier name, tracking number, ...) from the courier, and inserts those details themselves in the Admin Panel when they "Update Order Status" Section (by an 'admin') or "Update Item Status" Section (by a 'vendor' or 'admin') (in admin/orders/order_details.blade.php). With "automatic" shipping process, we're integrating third-party APIs (e.g. Shiprocket API) and orders go directly to the shipping partner, and the updates comes from the courier's end, and orders are automatically delivered to customers
            // First: "Manual" Shipping Process (when 'admin' enters the Courier Name and Tracking Number. Check the last if statement for the "Automatic" Shipping Process) (Business owner takes the order shipment information from the courier and inserts them themselves when they "Update Order Status" (by an 'admin') (in admin/orders/order_details.blade.php)) i.e. Updating `courier_name` and `tracking_number` columns in `orders` table
            if (!empty($data['courier_name']) && !empty($data['tracking_number'])) { // if an 'admin' Updates the Order Status to 'Shipped' in admin/orders/order_details.blade.php, and submits both Courier Name and Tracking Number HTML input fields
                Order::where('id', $data['order_id'])->update([
                    'courier_name'    => $data['courier_name'],
                    'tracking_number' => $data['tracking_number']
                ]);
            }


            // We'll save the "Update Order Status" History/Logs in `orders_logs` database table (whenever an 'admin' updates an order status)
            $log = new OrdersLog;
            $log->order_id     = $data['order_id'];
            $log->order_status = $data['order_status'];
            $log->save();


            // "Update Order Status" email: We send an email and SMS to the user when the general Order Status is updated by an 'admin' (pending, shipped, in progress, …)
            $deliveryDetails = Order::select('mobile', 'email', 'name')->where('id', $data['order_id'])->first()->toArray();
            $orderDetails    = Order::with('orders_products')->where('id', $data['order_id'])->first()->toArray(); // Eager Loading: https://laravel.com/docs/9.x/eloquent-relationships#eager-loading    // 'orders_products' is the relationship method name in Order.php model


            if (!empty($data['courier_name']) && !empty($data['tracking_number'])) { // if an 'admin' Updates the Order Status to 'Shipped' in admin/orders/order_details.blade.php, and submits both Courier Name and Tracking Number HTML input fields, include the Courier Name and Tracking Nubmer data in the email (send them with the email)
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

                \Illuminate\Support\Facades\Mail::send('emails.order_status', $messageData, function ($message) use ($email) { // Sending Mail: https://laravel.com/docs/9.x/mail#sending-mail    // 'emails.order_status' is the order_status.blade.php file inside the 'resources/views/emails' folder that will be sent as an email    // We pass in all the variables that order_status.blade.php will use    // https://www.php.net/manual/en/functions.anonymous.php
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

                \Illuminate\Support\Facades\Mail::send('emails.order_status', $messageData, function ($message) use ($email) { // Sending Mail: https://laravel.com/docs/9.x/mail#sending-mail    // 'emails.order_status' is the order_status.blade.php file inside the 'resources/views/emails' folder that will be sent as an email    // We pass in all the variables that order_status.blade.php will use    // https://www.php.net/manual/en/functions.anonymous.php
                    $message->to($email)->subject('Order Status Updated - MultiVendorEcommerceApplication.com.eg');
                });
            }

            $message = 'Order Status has been updated successfully!';


            return redirect()->back()->with('success_message', $message);
            return view('admin.orders.orders', compact('orders', 'logos', 'headerLogo'));
        }
    }

    // Update Item Status (which can be determined by both 'vendor'-s and 'admin'-s, in contrast to "Update Order Status" which is updated by 'admin'-s ONLY, not 'vendor'-s) (Pending, In Progress, Shipped, Delivered, ...) in admin/orders/order_details.blade.php in Admin Panel
    // Note: The `order_statuses` table contains all kinds of order statuses (that can be updated by 'admin'-s ONLY in `orders` table) like: pending, in progress, shipped, canceled, ...etc. In `order_statuses` table, the `name` column can be: 'New', 'Pending', 'Canceled', 'In Progress', 'Shipped', 'Partially Shipped', 'Delivered', 'Partially Delivered' and 'Paid'. 'Partially Shipped': If one order has products from different vendors, and one vendor has shipped their product to the customer while other vendor (or vendors) didn't!. 'Partially Delivered': if one order has products from different vendors, and one vendor has shipped and DELIVERED their product to the customer while other vendor (or vendors) didn't!    // The `order_item_statuses` table contains all kinds of order statuses (that can be updated by both 'vendor'-s and 'admin'-s in `orders_products` table) like: pending, in progress, shipped, canceled, ...etc.
    public function updateOrderItemStatus(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->isMethod('post')) {
            $data = $request->all();
            // dd($data);

            // Update Order Item Status in `orders_products` table
            OrdersProduct::where('id', $data['order_item_id'])->update(['item_status' => $data['order_item_status']]);


            // Note: There are two types of Shipping Process: "manual" and "automatic". "Manual" is in the case like small businesses, where the courier arrives at the owner warehouse to to pick up the order for shipping, and the small business owner takes the shipment details (like courier name, tracking number, ...) from the courier, and inserts those details themselves in the Admin Panel when they "Update Order Status" Section (by an 'admin') or "Update Item Status" Section (by a 'vendor' or 'admin') (in admin/orders/order_details.blade.php). With "automatic" shipping process, we're integrating third-party APIs and orders go directly to the shipping partner, and the updates comes from the courier's end, and orders are automatically delivered to customers
            // First: "Manual" Shipping Process (Business owner takes the order shipment information from the courier and inserts them themselves when they "Update Order Item Status" (by a 'vendor' or 'admin') (in admin/orders/order_details.blade.php)) i.e. Updating `courier_name` and `tracking_number` columns in `orders_products` table
            if (!empty($data['item_courier_name']) && !empty($data['item_tracking_number'])) { // if a 'vendor' or 'admin' updates the order Item Status to 'Shipped' in admin/orders/order_details.blade.php, and submits both Courier Name and Tracking Number HTML input fields
                OrdersProduct::where('id', $data['order_item_id'])->update([
                    'courier_name'    => $data['item_courier_name'],
                    'tracking_number' => $data['item_tracking_number']
                ]);
            }


            // Get the `order_id` column (which is the foreign key to the `id` column in `orders` table) value from `orders_products` table
            $getOrderId = OrdersProduct::select('order_id')->where('id', $data['order_item_id'])->first()->toArray();


            // We'll save the Update "Item Status" History/Logs in `orders_logs` database table (whenever a 'vendor' or 'admin' updates an order item status)
            // Note: In `orders_logs` table, if the `order_item_id` column is zero 0, this means the "Item Status" has never been updated, and if it's not zero 0, this means it's been previously updated by a 'vendor' or 'admin' and the number references/denotes the `id` column (foreign key) of the `orders_products` table
            $log = new OrdersLog;
            $log->order_id      = $getOrderId['order_id'];
            $log->order_item_id = $data['order_item_id'];
            $log->order_status  = $data['order_item_status'];
            $log->save();


            // "Item Status" update email: We send an email and SMS to the user when the Item Status (in the "Ordered Products" section) is updated by a 'vendor' or 'admin' (pending, shipped, in progress, …) for every product on its own in the email (not the whole order products, but the email is about the product that has been updated ONLY)
            $deliveryDetails = Order::select('mobile', 'email', 'name')->where('id', $getOrderId['order_id'])->first()->toArray();

            // Making sure that ONLY ONE order product (from the `orders_products` table) that has been item status updated, NOT all the order products, are sent in the email
            $order_item_id = $data['order_item_id'];
            $orderDetails  = Order::with([ // Eager Loading: https://laravel.com/docs/9.x/eloquent-relationships#eager-loading    // 'orders_products' is the relationship method name in Order.php model    // Constraining Eager Loads: https://laravel.com/docs/9.x/eloquent-relationships#constraining-eager-loads    // Subquery Where Clauses: https://laravel.com/docs/9.x/queries#subquery-where-clauses    // Advanced Subqueries: https://laravel.com/docs/9.x/eloquent#advanced-subqueries
                'orders_products' => function ($query) use ($order_item_id) { // function () use ()     syntax: https://www.php.net/manual/en/functions.anonymous.php#:~:text=the%20use%20language%20construct     // 'orders_products' is the Relationship method name in Order.php model
                    $query->where('id', $order_item_id); // `id` column in `orders_products` table
                }
            ])->where('id', $getOrderId['order_id'])->first()->toArray(); // Eager Loading: https://laravel.com/docs/9.x/eloquent-relationships#eager-loading    // 'orders_products' is the relationship method name in Order.php model
            // dd($orderDetails);
            // Note: Now in this case, updating the item status of one product will send an email to user but with telling the item statuses of all of the Order items (not ONLY the item with the status updated!). The solution to this is using a subquery (Constraining Eager Loads)


            if (!empty($data['item_courier_name']) && !empty($data['item_tracking_number'])) { // if a 'vendor' or 'admin' Updates the Order "Item Status" to 'Shipped' in admin/orders/order_details.blade.php, and submits both Courier Name and Tracking Number HTML input fields, include the Courier Name and Tracking Nubmer data in the email (send them with the email)
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

        $order = Order::with('orders_products')
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


        $order = Order::with('orders_products')->where('id', $order_id)->first();

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

<table border="1">
<thead>
<tr>
    <th>Product</th>
    <th>Qty</th>
    <th>Price</th>
    <th>Total</th>
</tr>
</thead>
<tbody>';

        $subTotal = 0;

        foreach ($orderDetails['orders_products'] as $product) {
            $lineTotal = $product['product_price'] * $product['product_qty'];
            $subTotal += $lineTotal;

            $invoiceHTML .= '
<tr>
    <td>' . $product['product_name'] . '</td>
    <td>' . $product['product_qty'] . '</td>
    <td>INR ' . $product['product_price'] . '</td>
    <td>INR ' . $lineTotal . '</td>
</tr>';
        }

        $invoiceHTML .= '
</tbody>
</table>

<p>
<b>Subtotal:</b> INR ' . $subTotal . '<br>
<b>Discount:</b> INR ' . ($orderDetails['coupon_amount'] ?? 0) . '<br>
<b>Grand Total:</b> INR ' . $orderDetails['grand_total'] . '
</p>

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

        $admin     = Auth::guard('admin')->user();
        $adminType = $admin->type;

        $product = Product::where('product_isbn', $request->isbn)->first();

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Book not found'
            ], 404);
        }

        $attribute = ProductsAttribute::where('product_id', $product->id)
            ->when($adminType === 'vendor', function ($q) use ($admin) {
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
        // $discountPercent = $attribute->product_discount ?? 0;

        // $discountAmount = round(($basePrice * $discountPercent) / 100);
        // $finalPrice     = round($basePrice - $discountAmount);

        return response()->json([
            'status' => true,
            'data' => [
                'product_id'       => $product->id,
                'product_name'     => $product->product_name,
                'product_isbn'     => $product->product_isbn,
                'base_price'       => round($basePrice),
                // 'discount_percent' => $discountPercent,
                // 'discount_amount'  => $discountAmount,
                // 'final_price'      => $finalPrice,
                'stock'            => $attribute->stock,
                'product_image'    => $product->product_image ?? ''
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
                'product_id'   => $product->id,
                'product_name' => $product->product_name,
                'product_isbn' => $product->product_isbn,
                'price'        => $product->product_price,
                'quantity'     => $request->quantity,
                'total'        => $product->product_price * $request->quantity
            ];
        }

        session()->put('sales_cart', $cart);

        // 🔥 reset discounts on cart change
        session()->forget([
            'sales_coupon',
            'sales_extra_discount_amount',
            'sales_extra_discount_percent'
        ]);

        return response()->json(['status' => true, 'message' => 'Added to cart']);
    }

    public function applyExtraDiscount(Request $request)
    {
        $request->validate([
            'extra_discount' => 'required|numeric|min:0|max:100'
        ]);

        $cart = session()->get('sales_cart', []);
        if (empty($cart)) {
            return response()->json(['status' => false, 'message' => 'Cart is empty'], 400);
        }

        $subTotal = array_sum(array_column($cart, 'total'));

        // Extra discount
        $percent = $request->extra_discount;
        $amount  = round(($subTotal * $percent) / 100);
        $amount  = min($amount, $subTotal);

        session()->put('sales_extra_discount_percent', $percent);
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
                'sales_extra_discount_amount',
                'sales_extra_discount_percent'
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function processSale(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required|string|max:255',
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

                // ✅ FIXED FIELDS
                'coupon_code'      => $couponCode,
                'coupon_amount'    => $couponAmount,
                'extra_discount'   => $extraDiscount,
                'grand_total'      => $grandTotal
            ]);

            // 6️⃣ Order items + stock update
            foreach ($cart as $item) {

                $admin     = Auth::guard('admin')->user();
                $adminType = $admin->type;

                $attrQuery = ProductsAttribute::where('product_id', $item['product_id']);

                if ($adminType === 'vendor') {
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
                    'admin_id'      => ($adminType !== 'vendor') ? $admin->id : 0,
                    'vendor_id'     => ($adminType === 'vendor') ? $admin->vendor_id : 0,
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
                'sales_extra_discount_amount',
                'sales_extra_discount_percent'
            ]);

            return redirect()->back()
                ->with('success_message', 'Sale completed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error_message', $e->getMessage());
        }
    }
}
