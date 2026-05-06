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
        Schema::table('vendors_business_details', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('shop_country');
            $table->unsignedBigInteger('state_id')->nullable()->after('shop_state');
            $table->unsignedBigInteger('district_id')->nullable()->after('shop_city');
            $table->unsignedBigInteger('block_id')->nullable()->after('district_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors_business_details', function (Blueprint $table) {
            $table->dropColumn(['country_id', 'state_id', 'district_id', 'block_id']);
        });
    }
};
