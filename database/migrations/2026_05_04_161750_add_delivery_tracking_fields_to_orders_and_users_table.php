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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('district_id')->nullable()->after('pincode');
            $table->decimal('latitude', 10, 8)->nullable()->after('district_id');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('block_id');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });

        Schema::table('vendors_business_details', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('shop_pincode');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['district_id', 'latitude', 'longitude']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('vendors_business_details', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
