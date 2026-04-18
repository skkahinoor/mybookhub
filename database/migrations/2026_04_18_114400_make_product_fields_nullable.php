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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('section_id')->nullable()->change();
            $table->integer('category_id')->nullable()->change();
            $table->integer('publisher_id')->nullable()->change();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->integer('section_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('section_id')->nullable(false)->change();
            $table->integer('category_id')->nullable(false)->change();
            $table->integer('publisher_id')->nullable(false)->change();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->integer('section_id')->nullable(false)->change();
        });
    }
};
