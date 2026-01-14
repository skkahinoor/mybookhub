<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdersLog extends Model
{
    use HasFactory;

    protected $table = 'orders_logs';

    protected $fillable = [
        'order_id',
        'order_item_id',
        'order_status'
    ];

    protected $casts = [
        'order_id'      => 'integer',
        'order_item_id' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function orderItem()
    {
        return $this->belongsTo(OrdersProduct::class, 'order_item_id');
    }

    public static function getItemDetails($order_item_id)
    {
        return OrdersProduct::find($order_item_id);
    }

    public function orders_products()
    {
        return $this->hasMany('App\Models\OrdersProduct', 'id', 'order_item_id'); // 'order_id' (column of `orders_products` table) is the Foreign Key of the Relationship
    }

    // public static function getItemDetails($order_item_id)
    // {
    //     $getItemDetails = \App\Models\OrdersProduct::where('id', $order_item_id)->first()->toArray();


    //     return $getItemDetails;
    // }
}

//  We'll use this relationship to show if the order status previewed in "Update Order Status" Section in admin/orders/order_details.blade.php is whether updated from "Update Item Status" Section (in case the `order_item_id` column is NOT zero 0) or from "Update Order Status" Section
//      Relationship of an Order Log `orders_logs` table with Order_Products `orders_products` table (every Order Log has many Order_Products) (`order_item_id` column in `orders_logs` table is the foreign key to the `id` column in `orders_products` table)
//     public function orders_products() {
//         return $this->hasMany('App\Models\OrdersProduct', 'id', 'order_item_id'); // 'order_id' (column of `orders_products` table) is the Foreign Key of the Relationship
//     }



//     public static function getItemDetails($order_item_id) {
//         $getItemDetails = \App\Models\OrdersProduct::where('id', $order_item_id)->first()->toArray();


//         return $getItemDetails;
//     }
