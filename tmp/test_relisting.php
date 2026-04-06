<?php

use App\Models\Product;
use App\Models\ProductsAttribute;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Load Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Setup test environment
$user = User::first(); // Grab a test user
if (!$user) {
    echo "No user found to test.\n";
    exit;
}
Auth::login($user);

$product = Product::where('condition', 'old')->first();
if (!$product) {
    echo "No old product found to test.\n";
    exit;
}

echo "Testing for User ID: {$user->id} and Product ID: {$product->id}\n";

// 1. Clean up existing attributes for this test to have a clean state
ProductsAttribute::where('product_id', $product->id)->where('user_id', $user->id)->delete();
echo "Cleaned up existing attributes.\n";

// 2. Simulate first addition
$response1 = app(\App\Http\Controllers\Api\User\SellBookController::class)->store(new \Illuminate\Http\Request([
    'section_id' => $product->section_id,
    'category_id' => $product->category_id,
    'subcategory_id' => $product->subcategory_id,
    'product_name' => $product->product_name,
    'product_price' => $product->product_price,
    'old_book_condition_id' => 1, // Assume 1 exists
    'language_id' => $product->language_id,
    'product_isbn' => $product->product_isbn,
]));

echo "First addition status: " . $response1->getStatusCode() . "\n";
echo "Message: " . json_decode($response1->getContent())->message . "\n";

// 3. Simulate second addition (should fail)
$response2 = app(\App\Http\Controllers\Api\User\SellBookController::class)->store(new \Illuminate\Http\Request([
    'section_id' => $product->section_id,
    'category_id' => $product->category_id,
    'subcategory_id' => $product->subcategory_id,
    'product_name' => $product->product_name,
    'product_price' => $product->product_price,
    'old_book_condition_id' => 1,
    'language_id' => $product->language_id,
    'product_isbn' => $product->product_isbn,
]));

echo "Second addition (duplicate) status: " . $response2->getStatusCode() . "\n";
echo "Message: " . json_decode($response2->getContent())->message . "\n";

// 4. Mark as sold
$attr = ProductsAttribute::where('product_id', $product->id)->where('user_id', $user->id)->first();
$attr->is_sold = 1;
$attr->save();
echo "Marked first listing as sold.\n";

// 5. Simulate third addition (should succeed)
$response3 = app(\App\Http\Controllers\Api\User\SellBookController::class)->store(new \Illuminate\Http\Request([
    'section_id' => $product->section_id,
    'category_id' => $product->category_id,
    'subcategory_id' => $product->subcategory_id,
    'product_name' => $product->product_name,
    'product_price' => $product->product_price,
    'old_book_condition_id' => 1,
    'language_id' => $product->language_id,
    'product_isbn' => $product->product_isbn,
]));

echo "Third addition (after sold) status: " . $response3->getStatusCode() . "\n";
echo "Message: " . json_decode($response3->getContent())->message . "\n";

// 6. Verify row count
$count = ProductsAttribute::where('product_id', $product->id)->where('user_id', $user->id)->count();
echo "Total attributes for this user/product: $count (Expected 2)\n";

// 7. Check SKUs
$skus = ProductsAttribute::where('product_id', $product->id)->where('user_id', $user->id)->pluck('sku')->toArray();
echo "SKUs: " . implode(", ", $skus) . "\n";

// Clean up
// ProductsAttribute::where('product_id', $product->id)->where('user_id', $user->id)->delete();
