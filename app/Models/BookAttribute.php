<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'product_price', 'stock', 'edition_id'
    ];

    public function edition()
    {
        return $this->belongsTo(Edition::class, 'edition_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}