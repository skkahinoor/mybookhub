<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vendor;

$v = Vendor::find(29);
if ($v) {
    echo "ID: " . $v->id . "\n";
    echo "Location: " . ($v->location ?? 'NULL') . "\n";
} else {
    echo "Vendor 29 not found\n";
}
