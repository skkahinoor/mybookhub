<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdersProduct extends Model
{
    protected $table = 'orders_products';

    protected $fillable = [
        'order_id',
        'user_id',
        'vendor_id',
        'admin_id',
        'product_id',
        'product_name',
        'product_price',
        'product_qty',
        'item_status',
        'courier_name',
        'tracking_number',
        'commission'
    ];

    protected $casts = [
        'order_id'      => 'integer',
        'user_id'       => 'integer',
        'vendor_id'     => 'integer',
        'admin_id'      => 'integer',
        'product_id'    => 'integer',
        'product_price' => 'float',
        'product_qty'   => 'integer',
        'commission'    => 'float',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Admin::class, 'vendor_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function logs()
    {
        return $this->hasMany(OrdersLog::class, 'order_item_id');
    }

    public function scopeOwnedByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }
}
