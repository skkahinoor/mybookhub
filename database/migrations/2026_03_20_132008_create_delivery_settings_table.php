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
        // Drop table if exists
        Schema::dropIfExists('delivery_settings');

        // Create fresh table
        Schema::create('delivery_settings', function (Blueprint $table) {
            $table->id();

            $table->decimal('min_order_amount', 10, 2)->default(499.00);
            $table->decimal('delivery_charge', 10, 2)->default(20.00);

            $table->boolean('is_free_delivery')->default(false);
            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_settings');
    }
};
