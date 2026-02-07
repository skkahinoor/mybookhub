<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location',
        'confirm',
        'commission',
        'status',
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
