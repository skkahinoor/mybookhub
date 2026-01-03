<?php

namespace App\Helpers;

class Helper
{
    public static function getDistance($lat1, $lon1, $lat2, $lon2)
    {
        if ($lat1 === null || $lon1 === null || $lat2 === null || $lon2 === null) {
            return null;
        }
        $earthRadius = 6371; // Radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return $distance; // in kilometers
    }
}

// Global helper functions (outside the namespace/class)
use App\Models\Cart;

function totalCartItems() {
    if (\Illuminate\Support\Facades\Auth::check()) {
        $user_id = \Illuminate\Support\Facades\Auth::user()->id;
        $totalCartItems = Cart::where('user_id', $user_id)->sum('quantity');
    } else {
        $session_id = \Illuminate\Support\Facades\Session::get('session_id');
        $totalCartItems = Cart::where('session_id', $session_id)->sum('quantity');
    }
    return $totalCartItems;
}

function getCartItems() {
    if (\Illuminate\Support\Facades\Auth::check()) {
        $getCartItems = Cart::with([
            'product' => function ($query) {
                $query->select('id', 'category_id', 'product_name', 'product_image');
            }
        ])->orderBy('id', 'Desc')->where([
            'user_id'    => \Illuminate\Support\Facades\Auth::user()->id
        ])->get()->toArray();
    } else {
        $getCartItems = Cart::with([
            'product' => function ($query) {
                $query->select('id', 'category_id', 'product_name', 'product_image');
            }
        ])->orderBy('id', 'Desc')->where([
            'session_id' => \Illuminate\Support\Facades\Session::get('session_id')
        ])->get()->toArray();
    }
    return $getCartItems;
}
