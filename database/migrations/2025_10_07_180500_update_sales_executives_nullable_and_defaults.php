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
        Schema::table('sales_executives', function (Blueprint $table) {
            $table->string('phone')->nullable()->change();
            $table->longText('address')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('district')->nullable()->change();
            $table->string('state')->nullable()->change();
            $table->string('pincode')->nullable()->change();
            $table->string('country')->nullable()->change();
            $table->string('bank_name')->nullable()->change();
            $table->string('account_number')->nullable()->change();
            $table->string('ifsc_code')->nullable()->change();
            $table->string('bank_branch')->nullable()->change();
            $table->string('upi_id')->nullable()->change();

            $table->integer('total_target')->default(0)->nullable()->change();
            $table->integer('completed_target')->default(0)->nullable()->change();
            $table->integer('income_per_target')->default(0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_executives', function (Blueprint $table) {
            $table->string('phone')->nullable(false)->change();
            $table->longText('address')->nullable(false)->change();
            $table->string('city')->nullable(false)->change();
            $table->string('district')->nullable(false)->change();
            $table->string('state')->nullable(false)->change();
            $table->string('pincode')->nullable(false)->change();
            $table->string('country')->nullable(false)->change();
            $table->string('bank_name')->nullable(false)->change();
            $table->string('account_number')->nullable(false)->change();
            $table->string('ifsc_code')->nullable(false)->change();
            $table->string('bank_branch')->nullable(false)->change();
            $table->string('upi_id')->nullable(false)->change();

            $table->integer('total_target')->default(null)->nullable(false)->change();
            $table->integer('completed_target')->default(null)->nullable(false)->change();
            $table->integer('income_per_target')->default(null)->nullable(false)->change();
        });
    }
};


