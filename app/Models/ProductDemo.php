<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDemo extends Model
{
    use HasFactory;
    protected $table = 'product';
    protected $fillable = ['p_name','sku','qty'];
}
