<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldBookCommission extends Model
{
    use HasFactory;

    protected $fillable = ['percentage'];
    
    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('old_book_commission_percentage');
            \Illuminate\Support\Facades\Cache::forget('products_sync_version');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('old_book_commission_percentage');
            \Illuminate\Support\Facades\Cache::forget('products_sync_version');
        });
    }
}
