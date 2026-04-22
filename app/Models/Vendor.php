<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Vendor extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::saved(function ($vendor) {
            Cache::put('products_cache_version', time());
        });

        static::deleted(function ($vendor) {
            Cache::put('products_cache_version', time());
        });
    }

    protected $fillable = [
        'user_id',
        'location',
        'whatsapp_opt_in',
        'whatsapp_phone',
        'whatsapp_opt_in_at',
        'commission',
        'plan',
        'plan_started_at',
        'plan_expires_at',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
    ];

    protected $casts = [
        'plan_started_at' => 'datetime',
        'plan_expires_at' => 'datetime',
        'whatsapp_opt_in' => 'boolean',
        'whatsapp_opt_in_at' => 'datetime',
    ];


    // Vendor → Business Details (One to One)
    public function vendorbusinessdetails()
    {
        return $this->hasOne(
            VendorsBusinessDetail::class,
            'vendor_id',
            'id'
        );
    }

    // Vendor belongs to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }




    /* =======================
       Helper Functions
    ======================= */

    /**
     * Credit commission to Sales Executive for this vendor's plan activation.
     */
    public function creditCommission($plan = null)
    {
        $plan = strtolower($plan ?: $this->plan ?: 'free');
        $vendorUser = $this->user; // BelongsTo relationship

        if (!$vendorUser || !$vendorUser->added_by) {
            return;
        }

        $salesExecutive = \App\Models\User::find($vendorUser->added_by);
        if (!$salesExecutive || !$salesExecutive->hasRole('sales', 'web')) {
            return;
        }

        $planLabel = ($plan === 'pro') ? 'Pro Plan' : 'Free Plan';
        $description = "Commission for Vendor: {$vendorUser->name} (#{$vendorUser->id}) [{$planLabel}]";

        // Avoid double crediting
        $alreadyPaid = \App\Models\WalletTransaction::where('user_id', $salesExecutive->id)
            ->where('description', $description)
            ->exists();

        if ($alreadyPaid) {
            return;
        }

        $settingKey = ($plan === 'pro') ? 'default_income_per_pro_vendor' : 'default_income_per_vendor';
        $defaultAmount = ($plan === 'pro') ? 100 : 50;
        $amount = (float) \App\Models\Setting::getValue($settingKey, $defaultAmount);

        $salesExecutive->wallet_balance += $amount;
        $salesExecutive->save();

        \App\Models\WalletTransaction::create([
            'user_id'     => $salesExecutive->id,
            'amount'      => $amount,
            'type'        => 'credit',
            'description' => $description,
        ]);
    }

    // Get Vendor Shop Name
    public static function getVendorShop($vendorid)
    {
        return VendorsBusinessDetail::where('vendor_id', $vendorid)
            ->value('shop_name');
    }

    // Get Vendor Commission
    public static function getVendorCommission($vendor_id)
    {
        return Vendor::where('id', $vendor_id)
            ->value('commission');
    }
}
