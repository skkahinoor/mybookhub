<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('products_attributes', function (Blueprint $table) {
            $table->string('razorpay_order_id')->nullable()->after('platform_charge');
            $table->string('razorpay_payment_id')->nullable()->after('razorpay_order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_attributes', function (Blueprint $table) {
            $table->dropColumn(['razorpay_order_id', 'razorpay_payment_id']);
        });
    }
};
