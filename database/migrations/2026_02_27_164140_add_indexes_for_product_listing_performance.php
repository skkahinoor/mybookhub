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
        // =========================
        // PRODUCTS TABLE
        // =========================
        Schema::table('products', function (Blueprint $table) {
            $table->index('status', 'idx_products_status');
            $table->index('category_id', 'idx_products_category');
            $table->index('subcategory_id', 'idx_products_subcategory');
            $table->index('product_price', 'idx_products_price');
        });

        // =========================
        // PRODUCTS_ATTRIBUTES TABLE
        // =========================
        Schema::table('products_attributes', function (Blueprint $table) {
            $table->index('status', 'idx_pa_status');
            $table->index('product_id', 'idx_pa_product');
            $table->index('vendor_id', 'idx_pa_vendor');
            $table->index('stock', 'idx_pa_stock');
        });

        // =========================
        // VENDORS TABLE
        // =========================
        Schema::table('vendors', function (Blueprint $table) {
            $table->index('status', 'idx_vendors_status');
            $table->index('plan', 'idx_vendors_plan');
        });

        // =========================
        // RATINGS TABLE
        // =========================
        Schema::table('ratings', function (Blueprint $table) {
            $table->index('product_attribute_id', 'idx_ratings_pa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // PRODUCTS
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_status');
            $table->dropIndex('idx_products_category');
            $table->dropIndex('idx_products_subcategory');
            $table->dropIndex('idx_products_price');
        });

        // PRODUCTS_ATTRIBUTES
        Schema::table('products_attributes', function (Blueprint $table) {
            $table->dropIndex('idx_pa_status');
            $table->dropIndex('idx_pa_product');
            $table->dropIndex('idx_pa_vendor');
            $table->dropIndex('idx_pa_stock');
        });

        // VENDORS
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropIndex('idx_vendors_status');
            $table->dropIndex('idx_vendors_plan');
        });

        // RATINGS
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropIndex('idx_ratings_pa');
        });
    }
};
