<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Cart;
use App\Models\HeaderLogo;
use App\Models\Language;
use App\Models\ProductsAttribute;
use App\Models\Payment;
use App\Models\Section;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Mail;
use Razorpay\Api\Api;

class RazorpayController extends Controller
{
    public function razorpayDirect(Request $request)
    {
        $razorpay_order_id = $request->get('order_id');

        if (!$razorpay_order_id) {
            return redirect('cart');
        }

        $orders = Order::with('orders_products')->where('razorpay_order_id', $razorpay_order_id)->get();

        if ($orders->isEmpty()) {
             // Fallback to order_id if passed
             $order_id = $request->get('id');
             if($order_id){
                $orders = Order::with('orders_products')->where('id', $order_id)->get();
             }
        }

        if ($orders->isEmpty()) {
            return redirect('cart');
        }

        $grand_total = $orders->sum('grand_total');
        $order = $orders->first();
        $user = User::find($order->user_id); // Find user by order if not logged in

        $condition = session('condition', 'new');
        $sections = Section::all();
        $logos = HeaderLogo::all();
        $language = Language::get();

        $razorpayOrder = [
            'id' => $order->razorpay_order_id,
            'amount' => round($grand_total * 100)
        ];

        return view('front.razorpay.razorpay', compact(
            'order',
            'orders',
            'grand_total',
            'user',
            'logos',
            'sections',
            'condition',
            'language',
            'razorpayOrder'
        ));
    }

    public function razorpay()
    {
        if (Session::has('order_ids') || Session::has('order_id')) {

            $order_ids = Session::has('order_ids') ? Session::get('order_ids') : [Session::get('order_id')];
            $orders = Order::with('orders_products')->whereIn('id', $order_ids)->get();

            if ($orders->isEmpty()) {
                return redirect('cart');
            }

            $grand_total = $orders->sum('grand_total');

            $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

            $razorpayOrder = $api->order->create([
                'receipt' => 'order_' . implode('_', $order_ids),
                'amount' => round($grand_total * 100),
                'currency' => 'INR'
            ]);

            // Save razorpay order id to all related orders
            Order::whereIn('id', $order_ids)->update(['razorpay_order_id' => $razorpayOrder['id']]);

            $order = $orders->first(); // For view compatibility if needed
            $user = Auth::user();

            $condition = session('condition', 'new');
            $sections = Section::all();
            $logos = HeaderLogo::all();
            $language = Language::get();

            return view('front.razorpay.razorpay', compact(
                'order',
                'orders',
                'grand_total',
                'user',
                'logos',
                'sections',
                'condition',
                'language',
                'razorpayOrder'
            ));
        }

        return redirect('cart');
    }

    public function payment(Request $request)
    {
        $input = $request->all();
        $logos     = HeaderLogo::all();
        $sections  = Section::all();

        if (isset($input['razorpay_payment_id']) && !empty($input['razorpay_payment_id'])) {

            $order_ids = Session::has('order_ids') ? Session::get('order_ids') : [Session::get('order_id')];

            // If no order in session, try to find by razorpay_order_id
            if (empty($order_ids) || (count($order_ids) == 1 && $order_ids[0] == null)) {
                if (isset($input['razorpay_order_id'])) {
                    $order_ids = Order::where('razorpay_order_id', $input['razorpay_order_id'])->pluck('id')->toArray();
                }
            }

            $orders = Order::with('orders_products')->whereIn('id', $order_ids)->get();

            foreach ($orders as $order) {
                // Record Payment for each order
                $payment = new Payment;
                $payment->order_id = $order->id;
                $payment->user_id = $order->user_id;
                $payment->payment_id = $input['razorpay_payment_id'];
                $payment->razorpay_order_id = $input['razorpay_order_id'];
                $payment->payer_id = $order->user_id;
                $payment->payer_email = $order->email;
                $payment->amount = $order->grand_total;
                $payment->currency = 'INR';
                $payment->payment_status = 'Captured';
                $payment->save();

                // Update Order Status
                $order->update(['order_status' => 'Paid']);

                // Notify user: payment successful
                if ($order->user_id) {
                    Notification::create([
                        'type' => 'order_paid',
                        'title' => 'Payment successful',
                        'message' => 'Your payment for order #' . $order->id . ' (₹' . number_format((float) $order->grand_total, 2) . ') was successful. Order is now confirmed.',
                        'related_id' => (int) $order->user_id,
                        'related_type' => User::class,
                        'is_read' => false,
                    ]);
                }

                // Wallet Credit Logic
                \App\Models\WalletTransaction::checkAndCreditWallet($order->id);

                // Reduce Stock
                foreach ($order->orders_products as $item) {
                    $currentStock = ProductsAttribute::where('id', $item->product_attribute_id)->value('stock');
                    ProductsAttribute::where('id', $item->product_attribute_id)
                        ->update(['stock' => max(0, $currentStock - $item->product_qty)]);
                }
            }

            // Empty Cart
            Cart::where('user_id', $orders->first()->user_id)->delete();

            // Send Email for first order (or iterate for all if needed, but usually one is enough for acknowledgment)
            $email = $orders->first()->email;
            $messageData = [
                'email' => $email,
                'name' => $orders->first()->name,
                'order_id' => $order_ids[0],
                'orderDetails' => $orders->toArray()
            ];

            try {
                Mail::send('emails.order', $messageData, function ($message) use ($email) {
                    $message->to($email)->subject('Order Placed - BookHub');
                });
            } catch (\Exception $e) {
                // Log email fail
            }

            return redirect('razorpay/success');
        } else {
            return redirect('razorpay/fail');
        }
    }

    public function success()
    {
        $condition = session('condition', 'new');
        $sections  = Section::all();
        $logos     = HeaderLogo::all();
        $language  = Language::get();
        return view('front.razorpay.success', compact('logos', 'sections', 'condition', 'language'));
    }

    public function fail()
    {
        $condition = session('condition', 'new');
        $sections  = Section::all();
        $logos     = HeaderLogo::all();
        $language  = Language::get();
        return view('front.razorpay.fail', compact('logos', 'sections', 'condition', 'language'));
    }
}
