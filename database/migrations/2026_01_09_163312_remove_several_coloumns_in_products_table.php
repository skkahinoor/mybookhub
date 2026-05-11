<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $columns = Schema::getColumnListing('products');
        $columnsToDrop = array_intersect([
            'cbse', 'operating_system', 'screen_size', 'occasion', 'fit', 
            'pattern', 'sleeve', 'ram', 'fabric', 'is_featured', 'is_bestseller'
        ], $columns);

        if (!empty($columnsToDrop)) {
            Schema::table('products', function (Blueprint $table) use ($columnsToDrop) {
                foreach ($columnsToDrop as $column) {
                    $table->dropColumn($column);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
