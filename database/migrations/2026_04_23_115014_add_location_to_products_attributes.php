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
        Schema::table('products_attributes', function (Blueprint $table) {
            $table->string('user_location', 100)->nullable()->after('is_sold');
            $table->string('user_location_name', 255)->nullable()->after('user_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_attributes', function (Blueprint $table) {
            $table->dropColumn(['user_location', 'user_location_name']);
        });
    }
};
