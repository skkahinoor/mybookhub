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
            $table->timestamp('item_delivered_at')->nullable()->after('item_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_products', function (Blueprint $table) {
            $table->dropColumn('item_delivered_at');
        });
    }
};
