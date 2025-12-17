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
            $table->dropColumn('product_color');
            $table->dropColumn('product_video');
            $table->dropColumn('group_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_color')->nullable()->after('product_code');
            $table->string('product_video')->nullable()->after('product_image');
            $table->string('group_code')->nullable()->after('product_video');
        });
    }
};
