<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_type',
        'license_number',
        'id_proof',
        'license_image',
        'profile_image',
        'status',
        'is_online',
        'pickup_status',
        'drop_status',
        'rejected_order_ids',
        'account_holder_name',
        'bank_name',
        'account_number',
        'ifsc_code',
        'upi_id',
        'document_verify_status',
    ];

    protected $casts = [
        'rejected_order_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
