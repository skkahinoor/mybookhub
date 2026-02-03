<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Vendor;
use App\Models\Admin;
use App\Models\ProductsAttribute;
use App\Models\Setting;
use GuzzleHttp\Client;

class VendorPlanController extends Controller
{
    private function checkAccess(Request $request)
    {
        $admin = $request->user();

        if (!$admin || !$admin instanceof Admin) {
            return response()->json([
                'status' => false,
                'message' => 'Only Admin or Vendor can access this.'
            ], 403);
        }

        if (!in_array($admin->type, ['superadmin', 'vendor'])) {
            return response()->json([
                'status' => false,
                'message' => 'Only Admin or Vendor can access this.'
            ], 403);
        }

        if ($admin->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        return null;
    }

    public function status(Request $request)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $user = $request->user();

        if ($user->type !== 'vendor' || !$user->vendor_id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $vendor = Vendor::findOrFail($user->vendor_id);

        if (
            $vendor->plan === 'pro' &&
            $vendor->plan_expires_at &&
            $vendor->plan_expires_at->isPast()
        ) {
            $vendor->update([
                'plan' => 'free',
                'plan_started_at' => null,
                'plan_expires_at' => null,
            ]);

            $vendor->refresh();
        }

        $responseData = [
            'plan'            => $vendor->plan,
            'plan_started_at' => $vendor->plan_started_at,
            'plan_expires_at' => $vendor->plan_expires_at,
            'is_pro_active'   => $vendor->plan === 'pro'
                && $vendor->plan_expires_at
                && $vendor->plan_expires_at->isFuture(),
        ];

        if ($vendor->plan === 'free') {

            $freeLimit = (int) Setting::getValue('free_plan_book_limit', 10);

            $booksUploaded = ProductsAttribute::where('vendor_id', $vendor->id)
                ->where('admin_type', 'vendor')
                ->distinct('product_id')
                ->count('product_id');

            $responseData['books_uploaded']   = $booksUploaded;
            $responseData['free_plan_limit']  = $freeLimit;
            $responseData['remaining_books']  = max(0, $freeLimit - $booksUploaded);
        }

        return response()->json([
            'status' => true,
            'data'   => $responseData
        ]);
    }

    public function upgrade(Request $request)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $user = $request->user();

        if ($user->type !== 'vendor' || !$user->vendor_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $vendor = Vendor::findOrFail($user->vendor_id);

        if ($vendor->plan === 'pro' && $vendor->plan_expires_at?->isFuture()) {
            return response()->json([
                'status' => false,
                'message' => 'You already have an active Pro plan'
            ], 400);
        }

        $amount = (int) Setting::getValue('pro_plan_price');

        try {
            $client = new Client();

            $response = $client->post('https://api.razorpay.com/v1/orders', [
                'auth' => [env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET')],
                'json' => [
                    'amount' => $amount * 100,
                    'currency' => 'INR',
                    'receipt' => 'vendor_plan_' . $vendor->id . '_' . time(),
                    'notes' => [
                        'vendor_id' => $vendor->id,
                        'plan' => 'pro',
                        'type' => 'vendor_subscription'
                    ]
                ]
            ]);

            $order = json_decode($response->getBody(), true);

            $vendor->update(['razorpay_order_id' => $order['id']]);

            return response()->json([
                'status' => true,
                'data' => [
                    'order_id' => $order['id'],
                    'amount' => $amount,
                    'currency' => 'INR',
                    'key' => env('RAZORPAY_KEY_ID')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay Order Error', ['error' => $e->getMessage()]);
            return response()->json(['status' => false, 'message' => 'Payment order failed'], 500);
        }
    }

    public function verify(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
        ]);

        $user = $request->user();

        if ($user->type !== 'vendor') {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $vendor = Vendor::findOrFail($user->vendor_id);

        // Signature verify
        $generatedSignature = hash_hmac(
            'sha256',
            $request->razorpay_order_id . '|' . $request->razorpay_payment_id,
            env('RAZORPAY_KEY_SECRET')
        );

        if ($generatedSignature !== $request->razorpay_signature) {
            return response()->json(['status' => false, 'message' => 'Invalid signature'], 400);
        }

        try {
            $client = new Client();

            $payment = $client->get(
                'https://api.razorpay.com/v1/payments/' . $request->razorpay_payment_id,
                ['auth' => [env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET')]]
            );

            $paymentData = json_decode($payment->getBody(), true);

            if (!in_array($paymentData['status'], ['captured', 'authorized'])) {
                return response()->json(['status' => false, 'message' => 'Payment not completed'], 400);
            }

            // Renewal logic
            $expiry = ($vendor->plan === 'pro' && $vendor->plan_expires_at?->isFuture())
                ? $vendor->plan_expires_at->addMonth()
                : now()->addMonth();

            $vendor->update([
                'plan' => 'pro',
                'plan_started_at' => now(),
                'plan_expires_at' => $expiry,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Pro plan activated successfully',
                'expires_at' => $expiry
            ]);
        } catch (\Exception $e) {
            Log::error('Payment Verify Error', ['error' => $e->getMessage()]);
            return response()->json(['status' => false, 'message' => 'Verification failed'], 500);
        }
    }

    public function webhookupgrade(Request $request)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $user = $request->user();

        if ($user->type !== 'vendor' || !$user->vendor_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $vendor = Vendor::findOrFail($user->vendor_id);

        if ($vendor->plan === 'pro' && $vendor->plan_expires_at?->isFuture()) {
            return response()->json([
                'status' => false,
                'message' => 'You already have an active Pro plan'
            ], 400);
        }

        $amount = (int) Setting::getValue('pro_plan_price');

        try {
            $client = new \GuzzleHttp\Client();

            // 🔹 Create Razorpay order
            $response = $client->post('https://api.razorpay.com/v1/orders', [
                'auth' => [
                    env('RAZORPAY_KEY_ID'),
                    env('RAZORPAY_KEY_SECRET')
                ],
                'json' => [
                    'amount' => $amount * 100,
                    'currency' => 'INR',
                    'receipt' => 'vendor_plan_' . $vendor->id . '_' . time(),
                    'notes' => [
                        'vendor_id' => $vendor->id,
                        'plan' => 'pro'
                    ]
                ]
            ]);

            $order = json_decode($response->getBody(), true);

            $vendor->update([
                'razorpay_order_id' => $order['id']
            ]);

            // 🔥 Razorpay hosted checkout
            $paymentUrl = "https://checkout.razorpay.com/v1/checkout.js?" . http_build_query([
                'key_id'   => env('RAZORPAY_KEY_ID'),
                'order_id' => $order['id'],
                'amount'   => $amount * 100,
                'currency' => 'INR',
                'name'     => 'MyBookHub',
                'description' => 'Upgrade to Pro Plan',
                'prefill[email]'   => $user->email,
                'prefill[contact]' => $vendor->mobile,
                'theme[color]'     => '#FF9F1C'
            ]);

            return response()->json([
                'status' => true,
                'payment_url' => $paymentUrl
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay Order Error', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => false,
                'message' => 'Payment order failed'
            ], 500);
        }
    }

    public function razorpayWebhook(Request $request)
    {
        $signature = $request->header('X-Razorpay-Signature');
        $secret = env('RAZORPAY_WEBHOOK_SECRET');

        $expected = hash_hmac(
            'sha256',
            $request->getContent(),
            $secret
        );

        if (!hash_equals($expected, $signature)) {
            return response()->json(['status' => false], 403);
        }

        if ($request->event === 'payment.captured') {

            $entity = $request->payload['payment']['entity'];
            $orderId = $entity['order_id'];
            $paymentId = $entity['id'];

            $vendor = Vendor::where('razorpay_order_id', $orderId)->first();

            if ($vendor) {
                $expiry = ($vendor->plan === 'pro' && $vendor->plan_expires_at?->isFuture())
                    ? $vendor->plan_expires_at->addMonth()
                    : now()->addMonth();

                $vendor->update([
                    'plan' => 'pro',
                    'plan_started_at' => now(),
                    'plan_expires_at' => $expiry,
                    'razorpay_payment_id' => $paymentId
                ]);
            }
        }

        return response()->json(['status' => true]);
    }


    public function downgrade(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->type !== 'vendor') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $vendor = Vendor::findOrFail($user->vendor_id);

        $vendor->update([
            'plan' => 'free',
            'plan_started_at' => null,
            'plan_expires_at' => null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Downgraded to Free plan'
        ]);
    }

    public function getPlan(Request $request)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $user = $request->user();
        $vendor = Vendor::findOrFail($user->vendor_id);

        // Auto downgrade if expired
        if ($vendor->plan === 'pro' && $vendor->plan_expires_at?->isPast()) {
            $vendor->update([
                'plan' => 'free',
                'plan_started_at' => null,
                'plan_expires_at' => null,
            ]);
            $vendor->refresh();
        }

        $freeLimit = (int) Setting::getValue('free_plan_book_limit', 100);
        $proPrice  = (int) Setting::getValue('pro_plan_price', 499);

        $booksThisMonth = \App\Models\ProductsAttribute::where('vendor_id', $vendor->id)
            ->where('admin_type', 'vendor')
            ->where('created_at', '>=', now()->startOfMonth())
            ->distinct('product_id')
            ->count('product_id');

        return response()->json([
            'status' => true,
            'data' => [
                'current_plan' => $vendor->plan,
                'is_pro_active' => $vendor->plan === 'pro'
                    && $vendor->plan_expires_at
                    && $vendor->plan_expires_at->isFuture(),

                'free_plan' => [
                    'monthly_limit' => $freeLimit,
                    'books_uploaded' => $booksThisMonth,
                    'remaining_books' => max(0, $freeLimit - $booksThisMonth),
                    'coupons_enabled' => false,
                ],

                'pro_plan' => [
                    'price' => $proPrice,
                    'currency' => 'INR',
                    'duration' => '1 Month',
                ]
            ]
        ]);
    }
}
