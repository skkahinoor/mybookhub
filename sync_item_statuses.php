<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\OrderItemStatus;

// 1. Ensure basic statuses exist
$statuses = ['Pending', 'Delivered', 'Shipped', 'In Progress', 'Canceled'];
foreach ($statuses as $s) {
    OrderItemStatus::updateOrCreate(['name' => $s], ['status' => 1]);
}

// 2. Sync legacy orders
Order::where('order_status', 'Delivered')->get()->each(function($o) {
    OrdersProduct::where('order_id', $o->id)->update([
        'item_status' => 'Delivered',
        'item_delivered_at' => $o->delivered_at ?: now()
    ]);
});

echo "Item statuses synced successfully.\n";
