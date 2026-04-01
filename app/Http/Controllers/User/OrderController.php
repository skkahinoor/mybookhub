<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\OrderQuery;
use App\Models\OrderQueryMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
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
                    return '<a href="' . route('student.orders.show', $order->id) . '" class="btn btn-sm btn-primary" style="color: white; padding: 1.1rem 1rem !important;">View Details</a>';
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

        // Check if order can be cancelled (e.g. only if Status is New, Pending, or Paid)
        $allowedStatus = ['New', 'Pending', 'Paid'];
        if (!in_array($order->order_status, $allowedStatus)) {
            return redirect()->back()->with('error_message', 'Order cannot be cancelled at this stage.');
        }

        // Update Status
        $order->order_status = 'Cancelled';
        $order->save();

        // Also cancel all order items and notify vendors
        $items = \App\Models\OrdersProduct::where('order_id', $id)->get();
        \App\Models\OrdersProduct::where('order_id', $id)->update(['item_status' => 'Cancelled']);

        $vendorIds = $items->pluck('vendor_id')->filter()->unique();
        foreach ($vendorIds as $vendorId) {
            \App\Models\Notification::create([
                'type'         => 'order_cancelled',
                'title'        => 'Order Cancelled',
                'message'      => 'Order #' . $order->id . ' containing your products was cancelled by the customer.',
                'related_id'   => $order->id,
                'related_type' => \App\Models\Order::class,
                'vendor_id'    => $vendorId,
                'is_read'      => false,
            ]);
        }

        // Revert Wallet
        \App\Models\WalletTransaction::revertWallet($id);

        // Notify user
        Notification::create([
            'type' => 'order_cancelled',
            'title' => 'Order cancelled',
            'message' => 'Your order #' . $id . ' has been cancelled. Wallet balance (if used) has been reverted.',
            'related_id' => (int) Auth::id(),
            'related_type' => User::class,
            'is_read' => false,
        ]);

        return redirect()->back()->with('success_message', 'Order has been cancelled and wallet balance (if any) has been reverted.');
    }

    public function returnOrder(Request $request, $id)
    {
        $request->validate([
            'return_reason' => 'required|string',
        ]);

        $order = Order::where('id', $id)->where('user_id', Auth::user()->id)->firstOrFail();

        // Check if order is eligible for return
        if ($order->order_status != 'Delivered' || !$order->delivered_at) {
            return redirect()->back()->with('error_message', 'Order must be delivered before initiating a return.');
        }

        $deliveredDate = \Carbon\Carbon::parse($order->delivered_at);
        if ($deliveredDate->addDays(7)->isPast()) {
            return redirect()->back()->with('error_message', 'Return period (7 days) has expired for this order.');
        }

        // Update Return Status
        $order->order_status = 'Return Requested';
        $order->return_status = 'Return Requested';
        $order->return_reason = $request->return_reason;
        $order->save();

        // Also update all order items and notify vendors
        $items = \App\Models\OrdersProduct::where('order_id', $id)->get();
        \App\Models\OrdersProduct::where('order_id', $id)->update(['item_status' => 'Return Requested']);

        $vendorIds = $items->pluck('vendor_id')->filter()->unique();
        foreach ($vendorIds as $vendorId) {
            \App\Models\Notification::create([
                'type'         => 'order_return_requested',
                'title'        => 'Order Return Requested',
                'message'      => "Customer '" . Auth::user()->name . "' has requested a return for Order #" . $id . ". Reason: " . $request->return_reason,
                'related_id'   => $id,
                'related_type' => \App\Models\Order::class,
                'vendor_id'    => $vendorId,
                'is_read'      => false,
            ]);
        }

        // Notify Admin
        \App\Models\Notification::create([
            'type' => 'order_return_requested',
            'title' => 'Order Return Requested',
            'message' => "Customer '" . Auth::user()->name . "' has requested a return for Order #" . $id . ". Reason: " . $request->return_reason,
            'related_id' => $id,
            'related_type' => Order::class,
            'is_read' => false,
        ]);

        return redirect()->back()->with('success_message', 'Return request has been submitted successfully. Our team will review it and get back to you.');
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

    public function raiseQuery(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'order_product_id' => 'required|exists:orders_products,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $order = Order::find($request->order_id);
        if ($order->user_id != Auth::id()) {
            return redirect()->back()->with('error_message', 'Unauthorized access.');
        }

        $product = OrdersProduct::find($request->order_product_id);

        // Generate Ticket ID
        $ticket_id = 'TKT-' . strtoupper(Str::random(6));

        OrderQuery::create([
            'ticket_id' => $ticket_id,
            'order_id' => $request->order_id,
            'order_product_id' => $request->order_product_id,
            'user_id' => Auth::id(),
            'vendor_id' => $product->vendor_id,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        // Notify Admin
        Notification::create([
            'type' => 'order_query',
            'title' => 'New Order Query Raised',
            'message' => "Customer '" . Auth::user()->name . "' raised a query for Order #" . $request->order_id . " regarding '" . $product->product_name . "'. Ticket ID: " . $ticket_id,
            'related_id' => $request->order_id,
            'related_type' => Order::class,
            'is_read' => false,
        ]);

        return redirect()->back()->with('success_message', 'Query raised successfully. Your Ticket ID is ' . $ticket_id . '. Our team will get back to you soon.');
    }

    public function orderQueries(Request $request)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        $queries = OrderQuery::with(['order', 'orderProduct'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($request->ajax()) {
            return DataTables::of($queries)
                ->addIndexColumn()
                ->addColumn('ticket_id', function($q) {
                    return '<strong>' . $q->ticket_id . '</strong>';
                })
                ->addColumn('order_id', function($q) {
                    return '#' . $q->order_id;
                })
                ->addColumn('product_name', function($q) {
                    return $q->orderProduct->product_name ?? 'N/A';
                })
                ->addColumn('status', function($q) {
                    $class = 'badge-warning';
                    if($q->status == 'resolved') $class = 'badge-success';
                    elseif($q->status == 'ongoing') $class = 'badge-info';
                    elseif($q->status == 'closed') $class = 'badge-secondary';
                    return '<span class="badge ' . $class . '">' . ucfirst($q->status) . '</span>';
                })
                ->addColumn('date', function($q) {
                    return $q->created_at->format('M d, Y');
                })
                ->addColumn('action', function($q) {
                    return '<a href="' . route('student.orders.query.details', $q->id) . '" class="btn btn-sm btn-info">View Thread</a>';
                })
                ->rawColumns(['ticket_id', 'status', 'action'])
                ->make(true);
        }

        return view('user.orders.queries', compact('logos', 'headerLogo'));
    }

    public function queryDetails($id)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        $query = OrderQuery::with(['order', 'orderProduct', 'messages.user'])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        return view('user.orders.query_details', compact('query', 'logos', 'headerLogo'));
    }

    public function postQueryReply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi,pdf|max:10240', // 10MB limit
        ]);

        $query = OrderQuery::where('user_id', Auth::id())->where('id', $id)->firstOrFail();

        if ($query->status == 'closed') {
            return redirect()->back()->with('error_message', 'This ticket is closed and cannot be replied to.');
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('attachments/order_queries'), $filename);
            $attachmentPath = 'attachments/order_queries/' . $filename;
        }

        OrderQueryMessage::create([
            'order_query_id' => $id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachment' => $attachmentPath,
            'sender_type' => 'student',
        ]);

        // Optional: Notify Admin/Vendor
        Notification::create([
            'type' => 'order_query',
            'title' => 'New Reply for Ticket #' . $query->ticket_id,
            'message' => "Customer '" . Auth::user()->name . "' replied to Ticket #" . $query->ticket_id,
            'related_id' => $query->order_id,
            'related_type' => Order::class,
            'is_read' => false,
        ]);

        return redirect()->back()->with('success_message', 'Reply sent successfully.');
    }
}
