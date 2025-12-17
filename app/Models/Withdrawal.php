<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_executive_id',
        'amount',
        'status',
        'remarks',
        'payment_method',
        'transaction_id',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the sales executive that owns the withdrawal.
     */
    public function salesExecutive()
    {
        return $this->belongsTo(SalesExecutive::class, 'sales_executive_id');
    }
}
