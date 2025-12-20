<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorsBankDetail extends Model
{
    use HasFactory;

    // Explicit table name
    protected $table = 'vendors_bank_details';

    // Mass assignable fields
    protected $fillable = [
        'vendor_id',
        'account_holder_name',
        'bank_name',
        'account_number',
        'bank_ifsc_code',
    ];

    /**
     * Relationship: Bank detail belongs to Vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
