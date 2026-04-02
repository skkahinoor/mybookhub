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
        Schema::table('orders_products', function (Blueprint $table) {
            $table->string('return_payment_status')->nullable()->after('return_comments'); // Pending, Payment Initiated, Paid
            $table->text('return_payment_note')->nullable()->after('return_payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_products', function (Blueprint $table) {
            $table->dropColumn(['return_payment_status', 'return_payment_note']);
        });
    }
};
