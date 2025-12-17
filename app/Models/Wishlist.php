<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

    public static function getWishlistItems(): array
    {
        if (Auth::check()) {
            $items = self::with([
                'product' => function ($query) {
                    $query->select('id', 'category_id', 'product_name', 'product_image');
                }
            ])->orderBy('id', 'Desc')->where([
                'user_id' => Auth::user()->id,
            ])->get()->toArray();
        } else {
            $items = self::with([
                'product' => function ($query) {
                    $query->select('id', 'category_id', 'product_name', 'product_image');
                }
            ])->orderBy('id', 'Desc')->where([
                'session_id' => Session::get('session_id'),
            ])->get()->toArray();
        }

        return $items;
    }

    public static function totalWishlistItems(): int
    {
        if (Auth::check()) {
            return self::where('user_id', Auth::user()->id)->sum('quantity');
        }
        return self::where('session_id', Session::get('session_id'))->sum('quantity');
    }

    public static function isProductInWishlist(int $productId): bool
    {
        if (Auth::check()) {
            return self::where([
                'user_id' => Auth::user()->id,
                'product_id' => $productId,
            ])->exists();
        }
        return self::where([
            'session_id' => Session::get('session_id'),
            'product_id' => $productId,
        ])->exists();
    }
}


