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
        Schema::create('order_query_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_query_id');
            $table->unsignedBigInteger('user_id'); // Sender's user ID
            $table->text('message');
            $table->enum('sender_type', ['admin', 'vendor', 'student']);
            $table->timestamps();

            $table->foreign('order_query_id')->references('id')->on('order_queries')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_query_messages');
    }
};
