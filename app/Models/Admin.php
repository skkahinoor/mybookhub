<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $guard = 'admin';

    protected $fillable = [
        'name',
        'type',
        'vendor_id',
        'mobile',
        'email',
        'password',
        'image',
        'confirm',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    // Relationships
    public function vendorPersonal()
    {
        return $this->belongsTo('App\Models\Vendor', 'vendor_id');
    }

    public function vendorBusiness()
    {
        return $this->belongsTo('App\Models\VendorsBusinessDetail', 'vendor_id');
    }

    public function vendorBank()
    {
        return $this->belongsTo('App\Models\VendorsBankDetail', 'vendor_id');
    }
}
