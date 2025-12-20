<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorsBusinessDetail extends Model
{
    use HasFactory;

    // Table name (important because Laravel pluralization is different)
    protected $table = 'vendors_business_details';

    // Mass assignable fields
    protected $fillable = [
        'vendor_id',
        'shop_name',
        'shop_address',
        'shop_city',
        'shop_state',
        'shop_country',
        'shop_pincode',
        'shop_mobile',
        'shop_website',
        'shop_email',
        'address_proof',
        'address_proof_image',
        'business_license_number',
        'gst_number',
        'pan_number',
    ];

    /**
     * Relationship: Business detail belongs to a Vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
