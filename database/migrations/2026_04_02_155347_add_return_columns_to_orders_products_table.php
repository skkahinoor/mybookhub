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
            $table->string('return_reason')->nullable()->after('item_status');
            $table->string('return_status')->nullable()->after('return_reason');
            $table->text('return_comments')->nullable()->after('return_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_products', function (Blueprint $table) {
            $table->dropColumn(['return_reason', 'return_status', 'return_comments']);
        });
    }
};
