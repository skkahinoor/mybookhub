<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('country');
            $table->unsignedBigInteger('state_id')->nullable()->after('state');
            $table->unsignedBigInteger('district_id')->nullable()->after('city');
            $table->unsignedBigInteger('block_id')->nullable()->after('district_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_addresses', function (Blueprint $table) {
            $table->dropColumn(['country_id', 'state_id', 'district_id', 'block_id']);
        });
    }
};
