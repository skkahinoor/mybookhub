<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class SalesExecutive extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $guard = 'sales_executives';

    protected $fillable = [
        'profile_picture',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'block',
        'district',
        'state',
        'pincode',
        'country',
        'bank_name',
        'account_number',
        'ifsc_code',
        'bank_branch',
        'upi_id',
        'total_target',
        'completed_target',
        'income_per_target',
        'status',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}


