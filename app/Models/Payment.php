<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'user_id',
        'payment_id',
        'razorpay_order_id',
        'razorpay_signature',
        'payer_id',
        'payer_email',
        'amount',
        'currency',
        'payment_status'
    ];

    /**
     * Relationship with Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}