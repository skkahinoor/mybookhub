<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->unsignedBigInteger('product_attribute_id')
                  ->nullable()
                  ->after('product_id');

            // Optional but recommended for data integrity
            // $table->foreign('product_attribute_id')
            //       ->references('id')
            //       ->on('products_attributes')
            //       ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropColumn('product_attribute_id');
        });
    }
};
