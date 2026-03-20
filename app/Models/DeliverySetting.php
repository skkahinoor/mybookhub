<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliverySetting extends Model
{
    use HasFactory;
    
    protected $fillable = ['min_order_amount', 'delivery_charge'];
}
