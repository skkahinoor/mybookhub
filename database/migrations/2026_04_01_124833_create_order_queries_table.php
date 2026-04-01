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
        Schema::create('order_queries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('order_product_id'); // Link to specific product in order
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('vendor_id')->nullable(); // For filtering by vendor
            $table->string('subject');
            $table->text('message');
            $table->text('admin_reply')->nullable();
            $table->enum('status', ['pending', 'ongoing', 'resolved', 'closed'])->default('pending');
            $table->timestamps();

            // Foreign keys
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('order_product_id')->references('id')->on('orders_products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_queries');
    }
};
