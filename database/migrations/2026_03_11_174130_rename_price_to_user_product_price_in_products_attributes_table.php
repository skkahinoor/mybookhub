<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL for better compatibility with MariaDB versions
        DB::statement('ALTER TABLE products_attributes CHANGE price user_product_price DOUBLE(8,2)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE products_attributes CHANGE user_product_price price DOUBLE(8,2)');
    }
};
