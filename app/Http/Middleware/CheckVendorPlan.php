<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;

class CheckVendorPlan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('admin')->user();
        
        // Only check for vendors
        if (!$admin || $admin->type !== 'vendor' || !$admin->vendor_id) {
            return $next($request);
        }

        $vendor = Vendor::find($admin->vendor_id);
        
        if (!$vendor) {
            return redirect('vendor/login')
                ->with('error_message', 'Vendor account not found.');
        }

        // Check if Pro plan has expired
        if ($vendor->plan === 'pro' && $vendor->plan_expires_at) {
            if (now()->greaterThan($vendor->plan_expires_at)) {
                // Plan expired, downgrade to free
                $vendor->update([
                    'plan' => 'free',
                    'plan_expires_at' => null,
                ]);
                
                return redirect('vendor/dashboard')
                    ->with('error_message', 'Your Pro plan has expired. You have been downgraded to Free plan.');
            }
        }

        // Check product upload limits for Free plan
        if ($vendor->plan === 'free' && ($request->routeIs('vendor.products.add') || $request->is('vendor/add-edit-product'))) {
            // Get dynamic limit from settings
            $freePlanBookLimit = (int) Setting::getValue('free_plan_book_limit', 100);
            
            // Count products added this month
            $currentMonthStart = now()->startOfMonth();
            $productsThisMonth = Product::whereHas('firstAttribute', function($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id)
                  ->where('admin_type', 'vendor');
            })
            ->where('created_at', '>=', $currentMonthStart)
            ->count();

            if ($productsThisMonth >= $freePlanBookLimit) {
                return redirect('vendor/products')
                    ->with('error_message', "You have reached the monthly limit of {$freePlanBookLimit} products for Free plan. Please upgrade to Pro plan for unlimited uploads.");
            }
        }

        // Block all coupon access for Free plan vendors
        if ($vendor->plan === 'free' && $request->is('vendor/coupons*')) {
            return redirect('vendor/dashboard')
                ->with('error_message', 'Coupon management is not available in Free plan. Please upgrade to Pro plan to create and manage coupons.');
        }

        return $next($request);
    }
}
