<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BookRequest;
use App\Models\BookRequestReply;

$br = BookRequest::find(13);
if ($br) {
    // Find the vendor who sent the "conform" message or any message
    $reply = BookRequestReply::where('book_request_id', 13)->whereNotNull('vendor_id')->latest()->first();
    if ($reply) {
        $br->vendor_id = $reply->vendor_id;
        $br->save();
        echo "Updated BookRequest 13 with Vendor ID: " . $reply->vendor_id . "\n";
    } else {
        echo "No vendor replies found for request 13\n";
    }
} else {
    echo "Request 13 not found\n";
}
