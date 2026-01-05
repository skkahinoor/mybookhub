<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Admin;
use GuzzleHttp\Client;
use App\Models\ProductsAttribute;

class VendorPlanController extends Controller
{
    private function checkAccess(Request $request)
    {
        $admin = $request->user(); // Sanctum user

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

        $FREE_LIMIT = 10;
        $booksUploaded = 0;

        if ($vendor->plan === 'free') {
            $booksUploaded = ProductsAttribute::where('vendor_id', $vendor->id)
                ->where('admin_type', 'vendor')
                ->distinct('product_id')
                ->count('product_id');
        }

        return response()->json([
            'status' => true,
            'data' => [
                'plan'            => $vendor->plan,
                'plan_started_at' => $vendor->plan_started_at,
                'plan_expires_at' => $vendor->plan_expires_at,
                'is_pro_active'   => $vendor->plan === 'pro'
                    && $vendor->plan_expires_at
                    && $vendor->plan_expires_at->isFuture(),
            ]
        ], 200);
    }

    public function upgrade(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->type !== 'vendor' || !$user->vendor_id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $vendor = Vendor::findOrFail($user->vendor_id);

        if ($vendor->plan === 'pro' && $vendor->plan_expires_at && $vendor->plan_expires_at->isFuture()) {
            return response()->json([
                'status' => false,
                'message' => 'You already have an active Pro plan'
            ], 400);
        }

        $amount = 49900; // ₹499

        try {
            $client = new Client();

            $response = $client->post('https://api.razorpay.com/v1/orders', [
                'auth' => [
                    env('RAZORPAY_KEY_ID'),
                    env('RAZORPAY_KEY_SECRET')
                ],
                'json' => [
                    'amount' => $amount,
                    'currency' => 'INR',
                    'receipt' => 'vendor_plan_' . $vendor->id . '_' . time(),
                ]
            ]);

            $order = json_decode($response->getBody()->getContents(), true);

            $vendor->update([
                'razorpay_order_id' => $order['id']
            ]);

            return response()->json([
                'status' => true,
                'order' => [
                    'order_id' => $order['id'],
                    'amount' => $order['amount'],
                    'currency' => $order['currency'],
                    'key' => env('RAZORPAY_KEY_ID')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Unable to create payment order'
            ], 500);
        }
    }

    public function verify(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required'
        ]);

        $user = $request->user();

        if (!$user || $user->type !== 'vendor') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $vendor = Vendor::findOrFail($user->vendor_id);

        $signature = hash_hmac(
            'sha256',
            $request->razorpay_order_id . '|' . $request->razorpay_payment_id,
            env('RAZORPAY_KEY_SECRET')
        );

        if ($signature !== $request->razorpay_signature) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid signature'
            ], 400);
        }

        $vendor->update([
            'plan' => 'pro',
            'plan_started_at' => now(),
            'plan_expires_at' => now()->addMonth(),
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Pro plan activated successfully'
        ]);
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
            'plan_expires_at' => null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Downgraded to Free plan'
        ]);
    }
}
