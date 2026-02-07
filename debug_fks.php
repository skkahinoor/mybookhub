<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tables = ['products_attributes', 'ebooks'];
foreach($tables as $table) {
    $res = DB::select("SHOW CREATE TABLE $table");
    if(!empty($res)) {
        echo "TABLE: $table\n";
        echo $res[0]->{'Create Table'} . "\n\n";
    }
}
