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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('wallet_balance', 10, 2)->default(0)->after('status');
            $table->tinyInteger('is_wallet_credited')->default(0)->after('wallet_balance');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('wallet_amount', 10, 2)->default(0)->after('coupon_amount');
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('order_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['credit', 'debit']);
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['wallet_balance', 'is_wallet_credited']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('wallet_amount');
        });

        Schema::dropIfExists('wallet_transactions');
    }
};
