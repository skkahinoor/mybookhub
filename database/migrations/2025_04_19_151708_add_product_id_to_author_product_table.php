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
        Schema::table('author_product', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

            Schema::table('author_product', function (Blueprint $table) {
                $table->unsignedBigInteger('product_id')->after('author_id');

                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });
    }
};
