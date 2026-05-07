<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('delivery_agents', function (Blueprint $col) {
            $col->string('account_holder_name')->nullable();
            $col->string('bank_name')->nullable();
            $col->string('account_number')->nullable();
            $col->string('ifsc_code')->nullable();
            $col->string('upi_id')->nullable();
        });

        Schema::create('delivery_agent_payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_agent_id');
            $table->float('amount');
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('admin_remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('delivery_agents', function (Blueprint $col) {
            $col->dropColumn(['account_holder_name', 'bank_name', 'account_number', 'ifsc_code', 'upi_id']);
        });
        Schema::dropIfExists('delivery_agent_payouts');
    }
};
