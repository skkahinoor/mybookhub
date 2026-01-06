<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\HeaderLogo;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class VendorPlanController extends Controller
{
    /**
     * Show payment page and create Razorpay order
     */
    public function createOrder($vendor_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        
        if ($vendor->plan === 'pro' && $vendor->plan_expires_at && $vendor->plan_expires_at->isFuture()) {
            return redirect()->route('admin.login')
                ->with('error_message', 'You already have an active Pro plan.');
        }

        // Get dynamic plan price from settings
        $amount = (int) Setting::getValue('pro_plan_price', 49900); // Default to ₹499 in paise

        try {
            $client = new Client();
            
            // Create Razorpay Order
            $response = $client->post('https://api.razorpay.com/v1/orders', [
                'auth' => [
                    env('RAZORPAY_KEY_ID'),
                    env('RAZORPAY_KEY_SECRET')
                ],
                'json' => [
                    'amount' => $amount,
                    'currency' => 'INR',
                    'receipt' => 'vendor_plan_' . $vendor->id . '_' . time(),
                    'notes' => [
                        'vendor_id' => $vendor->id,
                        'vendor_name' => $vendor->name,
                        'plan' => 'pro',
                        'type' => 'vendor_subscription'
                    ]
                ]
            ]);

            $orderData = json_decode($response->getBody()->getContents(), true);

            // Update vendor with order ID
            $vendor->update([
                'razorpay_order_id' => $orderData['id'],
            ]);

            return view('admin.vendor.payment', [
                'vendor' => $vendor,
                'order_id' => $orderData['id'],
                'amount' => $orderData['amount'],
                'currency' => $orderData['currency'],
                'key_id' => env('RAZORPAY_KEY_ID'),
            ]);

        } catch (\Exception $e) {
            Log::error('Razorpay Order Creation Failed: ' . $e->getMessage());
            return redirect()->route('vendor.register')
                ->with('error_message', 'Failed to create payment order. Please try again.');
        }
    }

    /**
     * Verify and process Razorpay payment
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
            'vendor_id' => 'required|exists:vendors,id',
        ]);

        $vendor = Vendor::findOrFail($request->vendor_id);

        // Verify signature
        $generatedSignature = hash_hmac('sha256', 
            $request->razorpay_order_id . '|' . $request->razorpay_payment_id,
            env('RAZORPAY_KEY_SECRET')
        );

        if ($generatedSignature !== $request->razorpay_signature) {
            return response()->json([
                'status' => false,
                'message' => 'Payment verification failed. Invalid signature.'
            ], 400);
        }

        // Verify payment with Razorpay API
        try {
            $client = new Client();
            
            $response = $client->get('https://api.razorpay.com/v1/payments/' . $request->razorpay_payment_id, [
                'auth' => [
                    env('RAZORPAY_KEY_ID'),
                    env('RAZORPAY_KEY_SECRET')
                ]
            ]);

            $paymentData = json_decode($response->getBody()->getContents(), true);

            if ($paymentData['status'] === 'captured' || $paymentData['status'] === 'authorized') {
                // Update vendor with Pro plan
                $vendor->update([
                    'plan' => 'pro',
                    'plan_started_at' => now(),
                    'plan_expires_at' => now()->addMonth(),
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature,
                ]);

                return redirect()->route('vendor.payment.success')
                    ->with('success_message', 'Payment successful! Pro plan activated.');
            } else {
                return redirect()->route('vendor.payment.failure')
                    ->with('error_message', 'Payment not completed. Please try again.');
            }

        } catch (\Exception $e) {
            Log::error('Razorpay Payment Verification Failed: ' . $e->getMessage());
            return redirect()->route('vendor.payment.failure')
                ->with('error_message', 'Payment verification failed. Please contact support.');
        }
    }

    /**
     * Payment success page
     */
    public function paymentSuccess(Request $request)
    {
        return view('admin.vendor.payment_success');
    }

    /**
     * Payment failure page
     */
    public function paymentFailure(Request $request)
    {
        return view('admin.vendor.payment_failure');
    }

    /**
     * Upgrade to Pro plan (for existing vendors)
     */
    public function upgrade(Request $request)
    {
        $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
        
        if (!$admin || $admin->type !== 'vendor' || !$admin->vendor_id) {
            return redirect('admin/login')
                ->with('error_message', 'Unauthorized access.');
        }

        $vendor = Vendor::findOrFail($admin->vendor_id);

        if ($vendor->plan === 'pro' && $vendor->plan_expires_at && $vendor->plan_expires_at->isFuture()) {
            return redirect('admin/dashboard')
                ->with('error_message', 'You already have an active Pro plan.');
        }

        return redirect()->route('vendor.payment.create', ['vendor_id' => $vendor->id]);
    }

    /**
     * Downgrade to Free plan
     */
    public function downgrade(Request $request)
    {
        $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
        
        if (!$admin || $admin->type !== 'vendor' || !$admin->vendor_id) {
            return redirect('admin/login')
                ->with('error_message', 'Unauthorized access.');
        }

        $vendor = Vendor::findOrFail($admin->vendor_id);

        if ($vendor->plan === 'free') {
            return redirect('admin/dashboard')
                ->with('error_message', 'You are already on Free plan.');
        }

        $vendor->update([
            'plan' => 'free',
            'plan_expires_at' => null,
        ]);

        return redirect('admin/dashboard')
            ->with('success_message', 'You have been downgraded to Free plan. Your Pro plan features will be disabled.');
    }

    /**
     * Renew Pro plan subscription
     */
    public function renew(Request $request)
    {
        $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
        
        if (!$admin || $admin->type !== 'vendor' || !$admin->vendor_id) {
            return redirect('admin/login')
                ->with('error_message', 'Unauthorized access.');
        }

        $vendor = Vendor::findOrFail($admin->vendor_id);

        // If already on Pro and not expired, extend the plan
        if ($vendor->plan === 'pro' && $vendor->plan_expires_at && $vendor->plan_expires_at->isFuture()) {
            // Extend from current expiry date
            $newExpiry = $vendor->plan_expires_at->addMonth();
        } else {
            // Start new subscription
            $newExpiry = now()->addMonth();
        }

        return redirect()->route('vendor.payment.create', ['vendor_id' => $vendor->id])
            ->with('info_message', 'Please complete payment to renew your Pro plan subscription.');
    }

    /**
     * Show plan management page
     */
    public function manage()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
        
        if (!$admin || $admin->type !== 'vendor' || !$admin->vendor_id) {
            return redirect('admin/login')
                ->with('error_message', 'Unauthorized access.');
        }

        $vendor = Vendor::findOrFail($admin->vendor_id);
        
        // Count products this month for Free plan
        $productsThisMonth = 0;
        $freePlanBookLimit = (int) Setting::getValue('free_plan_book_limit', 100);
        if ($vendor->plan === 'free') {
            $currentMonthStart = now()->startOfMonth();
            $productsThisMonth = \App\Models\Product::whereHas('firstAttribute', function($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id)
                  ->where('admin_type', 'vendor');
            })
            ->where('created_at', '>=', $currentMonthStart)
            ->count();
        }

        // Get dynamic plan price
        $proPlanPrice = (int) Setting::getValue('pro_plan_price', 49900);

        return view('admin.vendor.plan_manage', [
            'vendor' => $vendor,
            'productsThisMonth' => $productsThisMonth,
            'freePlanBookLimit' => $freePlanBookLimit,
            'proPlanPrice' => $proPlanPrice,
            'logos' => $logos,
            'headerLogo' => $headerLogo,
        ]);
    }
}
