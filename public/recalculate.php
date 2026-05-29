<?php
// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function calculateDistancePHP($lat1, $lon1, $lat2, $lon2) {
    if (!$lat1 || !$lon1 || !$lat2 || !$lon2) return 0;
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    return $miles * 1.609344; // Convert to kilometers
}

echo "<h1>Recalculating Earnings for Delivered Orders</h1>";

$orders = App\Models\Order::where('order_status', 'Delivered')->get();

if ($orders->isEmpty()) {
    echo "<p>No delivered orders found in the database.</p>";
    exit;
}

$updatedCount = 0;
foreach ($orders as $order) {
    echo "<h3>Order #{$order->id}</h3>";
    echo "Current Earning: ₹" . number_format($order->agent_trip_earning, 2) . "<br>";
    echo "Current Distance: " . number_format($order->total_trip_distance, 2) . " km<br>";

    // Calculate start coordinates (fallback to pickup if missing)
    $startLat = $order->agent_start_lat ? (float)$order->agent_start_lat : null;
    $startLng = $order->agent_start_lng ? (float)$order->agent_start_lng : null;

    $pickup = \App\Models\OrdersProduct::where('order_id', $order->id)->first(); 
    $pickupLat = null;
    $pickupLng = null;

    if ($pickup) {
        if ($pickup->vendor_id > 0) {
            $business = \App\Models\VendorsBusinessDetail::where('vendor_id', $pickup->vendor_id)->first();
            if ($business) {
                $pickupLat = $business->latitude;
                $pickupLng = $business->longitude;
            }
        } else {
            $attr = $pickup->product_attribute;
            $sellerUser = null;
            if ($attr && $attr->user_id > 0) {
                $sellerUser = \App\Models\User::find($attr->user_id);
            } else if ($pickup->admin_id > 0) {
                $sellerUser = \App\Models\User::find($pickup->admin_id);
            }
            if ($sellerUser) {
                $pickupLat = $sellerUser->latitude;
                $pickupLng = $sellerUser->longitude;
            }
        }
    }

    $dropLat = $order->latitude ? (float)$order->latitude : null;
    $dropLng = $order->longitude ? (float)$order->longitude : null;

    if (!$startLat && $pickupLat) {
        $startLat = (float)$pickupLat;
        $startLng = (float)$pickupLng;
    }

    if ($startLat && $startLng && $pickupLat && $pickupLng && $dropLat && $dropLng) {
        $d1 = calculateDistancePHP($startLat, $startLng, (float)$pickupLat, (float)$pickupLng);
        $d2 = calculateDistancePHP((float)$pickupLat, (float)$pickupLng, $dropLat, $dropLng);
        
        $totalDistance = $d1 + $d2;
        
        $rate = (float)$order->agent_rate_at_trip;
        if ($rate <= 0) {
            $rate = \App\Models\DeliverySetting::where('status', 1)->first()->agent_rate_per_km ?? 10.00;
        }

        $earning = $totalDistance * $rate;

        $order->update([
            'agent_start_lat' => $order->agent_start_lat ?? $startLat,
            'agent_start_lng' => $order->agent_start_lng ?? $startLng,
            'agent_rate_at_trip' => $rate,
            'total_trip_distance' => $totalDistance,
            'agent_trip_earning' => $earning
        ]);

        echo "<strong style='color: green;'>Updated:</strong> New Earning: ₹" . number_format($earning, 2) . " | New Distance: " . number_format($totalDistance, 2) . " km (Rate: ₹{$rate}/km)<br>";
        $updatedCount++;
    } else {
        echo "<strong style='color: red;'>Skipped:</strong> Missing coordinates. Start: ($startLat, $startLng), Pickup: ($pickupLat, $pickupLng), Drop: ($dropLat, $dropLng)<br>";
    }
    echo "<hr>";
}

echo "<h2>Recalculation completed. Updated {$updatedCount} orders.</h2>";
