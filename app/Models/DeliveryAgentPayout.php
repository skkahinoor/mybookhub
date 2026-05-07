<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAgentPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_agent_id',
        'amount',
        'status',
        'payment_method',
        'transaction_id',
        'admin_remarks',
    ];

    public function deliveryAgent()
    {
        return $this->belongsTo(DeliveryAgent::class, 'delivery_agent_id');
    }
}
