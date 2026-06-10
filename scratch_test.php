<?php
include 'vendor/autoload.php';
$app = include_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$product = \App\Models\Product::find(1);

$userLat = session('user_latitude');
$userLng = session('user_longitude');

$bestAttr = \App\Models\ProductsAttribute::where('product_id', $product->id)
    ->where('status', 1)
    ->where('stock', '>', 0)
    ->buyBox()
    ->first();

if (!$bestAttr) {
    $bestAttr = \App\Models\ProductsAttribute::where('product_id', $product->id)
        ->where('status', 1)
        ->first();
}

$vendor = $bestAttr ? $bestAttr->vendor : null;

// Calculate distance
$distance = null;
$vLat = null;
$vLng = null;
if ($vendor) {
    if ($vendor->vendorbusinessdetails && !empty($vendor->vendorbusinessdetails->latitude) && !empty($vendor->vendorbusinessdetails->longitude)) {
        $vLat = $vendor->vendorbusinessdetails->latitude;
        $vLng = $vendor->vendorbusinessdetails->longitude;
    } elseif ($vendor->location) {
        list($vLat, $vLng) = array_pad(explode(',', $vendor->location), 2, null);
    }
}

if ($userLat && $userLng && is_numeric($vLat) && is_numeric($vLng)) {
    $R  = 6371;
    $dL = deg2rad((float)$vLat - (float)$userLat);
    $dN = deg2rad((float)$vLng - (float)$userLng);
    $a  = sin($dL / 2) ** 2 + cos(deg2rad((float)$userLat)) * cos(deg2rad((float)$vLat)) * sin($dN / 2) ** 2;
    $distance = round($R * 2 * atan2(sqrt($a), sqrt(1 - $a)), 1);
}

$finalPrice = \App\Models\Product::getDiscountPrice($product->id);
$shopName   = optional($vendor?->vendorbusinessdetails)->shop_name ?? 'Individual Seller';
$address    = optional($vendor?->vendorbusinessdetails)->shop_address ?? '';

$mapped = [
    'id'          => $bestAttr ? $bestAttr->id : null,
    'product_id'  => $product->id,
    'name'        => $product->product_name,
    'isbn'        => $product->product_isbn,
    'image'       => $product->product_image
        ? config('app.book_covers_base_url', 'https://d3pq1zjqrptggt.cloudfront.net/book_covers/') . $product->product_image
        : null,
    'price'       => '₹' . number_format($finalPrice, 0),
    'shop'        => $shopName,
    'address'     => $address,
    'distance'    => $distance !== null ? $distance . ' km away' : null,
    'url'         => route('front.products.detail', $product->id),
];

print_r($mapped);
