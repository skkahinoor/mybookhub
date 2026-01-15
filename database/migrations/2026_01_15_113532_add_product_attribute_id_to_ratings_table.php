<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->unsignedBigInteger('product_attribute_id')
                  ->after('product_id')
                  ->nullable();

            // Optional: add foreign key if needed
            // $table->foreign('product_attribute_id')
            //       ->references('id')
            //       ->on('products_attributes')
            //       ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            // $table->dropForeign(['product_attribute_id']);
            $table->dropColumn('product_attribute_id');
        });
    }
};
