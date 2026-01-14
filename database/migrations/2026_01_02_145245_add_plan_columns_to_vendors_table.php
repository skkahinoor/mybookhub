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
        Schema::table('vendors', function (Blueprint $table) {
            $table->enum('plan', ['free', 'pro'])->default('free')->after('status');
            $table->timestamp('plan_started_at')->nullable()->after('plan');
            $table->timestamp('plan_expires_at')->nullable()->after('plan_started_at');
            $table->string('razorpay_order_id')->nullable()->after('plan_expires_at');
            $table->string('razorpay_payment_id')->nullable()->after('razorpay_order_id');
            $table->string('razorpay_signature')->nullable()->after('razorpay_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'plan',
                'plan_started_at',
                'plan_expires_at',
                'razorpay_order_id',
                'razorpay_payment_id',
                'razorpay_signature'
            ]);
        });
    }
};
