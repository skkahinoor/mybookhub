<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | PRODUCTS TABLE
        |--------------------------------------------------------------------------
        */

        $this->dropIndexIfExists('products', 'idx_products_status');
        $this->dropIndexIfExists('products', 'idx_products_category');
        $this->dropIndexIfExists('products', 'idx_products_subcategory');
        $this->dropIndexIfExists('products', 'idx_products_price');

        Schema::table('products', function (Blueprint $table) {
            $table->index('status', 'idx_products_status');
            $table->index('category_id', 'idx_products_category');
            $table->index('subcategory_id', 'idx_products_subcategory');
            $table->index('product_price', 'idx_products_price');
        });


        /*
        |--------------------------------------------------------------------------
        | PRODUCTS_ATTRIBUTES TABLE
        |--------------------------------------------------------------------------
        */

        $this->dropIndexIfExists('products_attributes', 'idx_pa_status');
        $this->dropIndexIfExists('products_attributes', 'idx_pa_product');
        $this->dropIndexIfExists('products_attributes', 'idx_pa_vendor');
        $this->dropIndexIfExists('products_attributes', 'idx_pa_stock');

        Schema::table('products_attributes', function (Blueprint $table) {
            $table->index('status', 'idx_pa_status');
            $table->index('product_id', 'idx_pa_product');
            $table->index('vendor_id', 'idx_pa_vendor');
            $table->index('stock', 'idx_pa_stock');
        });


        /*
        |--------------------------------------------------------------------------
        | VENDORS TABLE
        |--------------------------------------------------------------------------
        */

        $this->dropIndexIfExists('vendors', 'idx_vendors_plan');

        Schema::table('vendors', function (Blueprint $table) {
            $table->index('plan', 'idx_vendors_plan');
        });


        /*
        |--------------------------------------------------------------------------
        | RATINGS TABLE
        |--------------------------------------------------------------------------
        */

        $this->dropIndexIfExists('ratings', 'idx_ratings_pa');

        Schema::table('ratings', function (Blueprint $table) {
            $table->index('product_attribute_id', 'idx_ratings_pa');
        });
    }


    public function down(): void
    {
        $this->dropIndexIfExists('products', 'idx_products_status');
        $this->dropIndexIfExists('products', 'idx_products_category');
        $this->dropIndexIfExists('products', 'idx_products_subcategory');
        $this->dropIndexIfExists('products', 'idx_products_price');

        $this->dropIndexIfExists('products_attributes', 'idx_pa_status');
        $this->dropIndexIfExists('products_attributes', 'idx_pa_product');
        $this->dropIndexIfExists('products_attributes', 'idx_pa_vendor');
        $this->dropIndexIfExists('products_attributes', 'idx_pa_stock');

        $this->dropIndexIfExists('vendors', 'idx_vendors_plan');

        $this->dropIndexIfExists('ratings', 'idx_ratings_pa');
    }


    /*
    |--------------------------------------------------------------------------
    | Helper Function
    |--------------------------------------------------------------------------
    */

    private function dropIndexIfExists($table, $index)
    {
        $exists = DB::select("
            SELECT COUNT(1) as count
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
            AND table_name = ?
            AND index_name = ?
        ", [$table, $index]);

        if ($exists[0]->count > 0) {
            DB::statement("ALTER TABLE `$table` DROP INDEX `$index`");
        }
    }
};
