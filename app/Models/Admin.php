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
    public function vendorPersonal()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function vendorBusiness()
    {
        return $this->hasOne(VendorsBusinessDetail::class, 'vendor_id', 'vendor_id');
    }

    public function vendorBank()
    {
        return $this->hasOne(VendorsBankDetail::class, 'vendor_id', 'vendor_id');
    }
}