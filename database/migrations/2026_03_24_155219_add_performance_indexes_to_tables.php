<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Products table - missing indexes
        Schema::table('products', function (Blueprint $table) {
            $table->index('section_id', 'idx_products_section');
            $table->index('product_isbn', 'idx_products_isbn');
            $table->index('condition', 'idx_products_condition');
            $table->index('subject_id', 'idx_products_subject');
        });

        // Products attributes - composite index for whereHas + user_id
        Schema::table('products_attributes', function (Blueprint $table) {
            $table->index(['product_id', 'status', 'stock'], 'idx_pa_product_status_stock');
            $table->index('user_id', 'idx_pa_user');
        });

        // Carts - composite index for cart status lookups
        Schema::table('carts', function (Blueprint $table) {
            $table->index(['user_id', 'product_attribute_id'], 'idx_carts_user_attribute');
        });

        // Author product pivot - indexes for eager loading
        Schema::table('author_product', function (Blueprint $table) {
            $table->index('product_id', 'idx_author_product_product');
            $table->index('author_id', 'idx_author_product_author');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_section');
            $table->dropIndex('idx_products_isbn');
            $table->dropIndex('idx_products_condition');
            $table->dropIndex('idx_products_subject');
        });

        Schema::table('products_attributes', function (Blueprint $table) {
            $table->dropIndex('idx_pa_product_status_stock');
            $table->dropIndex('idx_pa_user');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex('idx_carts_user_attribute');
        });

        Schema::table('author_product', function (Blueprint $table) {
            $table->dropIndex('idx_author_product_product');
            $table->dropIndex('idx_author_product_author');
        });
    }
};
