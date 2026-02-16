<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Cart;
use App\Models\HeaderLogo;
use App\Models\Language;
use App\Models\ProductsAttribute;
use App\Models\Payment;
use App\Models\Section;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Mail;

class RazorpayController extends Controller
{
    public function razorpay()
    {
        if (Session::has('order_id')) {
            $order_id = Session::get('order_id');
            $order = Order::with('orders_products')->where('id', $order_id)->first();

            if (!$order) {
                return redirect('cart');
            }

            $grand_total = $order->grand_total;
            $user = Auth::user();
            $condition = session('condition', 'new');
            $sections  = Section::all();
            $logos     = HeaderLogo::all();
            $language  = Language::get();


            return view('front.razorpay.razorpay', compact('order', 'grand_total', 'user', 'logos', 'sections', 'condition', 'language'));
        } else {
            return redirect('cart');
        }
    }

    public function payment(Request $request)
    {
        $input = $request->all();
        $logos     = HeaderLogo::all();
        $sections  = Section::all();

        if (isset($input['razorpay_payment_id']) && !empty($input['razorpay_payment_id'])) {

            $order_id = Session::get('order_id');

            // Verify payment signature here if using orders API (Recommended for production)
            // For now, implementing basic capture flow based on frontend success

            // Record Payment
            $payment = new Payment;
            $payment->order_id = $order_id;
            $payment->user_id = Auth::user()->id;
            $payment->payment_id = $input['razorpay_payment_id'];
            $payment->payer_id = Auth::user()->id;
            $payment->payer_email = Auth::user()->email;
            $payment->amount = $input['totalAmount'];
            $payment->currency = 'INR';
            $payment->payment_status = 'Captured';
            $payment->save();

            // Update Order Status
            Order::where('id', $order_id)->update(['order_status' => 'Paid']);

            // Wallet Credit Logic
            \App\Models\WalletTransaction::checkAndCreditWallet($order_id);

            // Reduce Stock
            $orderDetails = Order::with('orders_products')->where('id', $order_id)->first()->toArray();
            foreach ($orderDetails['orders_products'] as $key => $item) {
                // Get current stock
                $currentStock = ProductsAttribute::where(['product_id' => $item['product_id']])->value('stock');

                $newStock = $currentStock - $item['product_qty'];

                ProductsAttribute::where(['product_id' => $item['product_id']])
                    ->update(['stock' => $newStock]);
            }

            // Empty Cart
            Cart::where('user_id', Auth::user()->id)->delete();

            // Send Email
            $email = Auth::user()->email;
            $messageData = [
                'email' => $email,
                'name' => Auth::user()->name,
                'order_id' => $order_id,
                'orderDetails' => $orderDetails
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
