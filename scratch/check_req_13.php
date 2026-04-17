<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BookRequest;

$br = BookRequest::find(13);
if ($br) {
    echo "ID: " . $br->id . "\n";
    echo "Status: " . $br->status . "\n";
    echo "Vendor ID: " . ($br->vendor_id ?? 'NULL') . "\n";
} else {
    echo "Request 13 not found\n";
}
