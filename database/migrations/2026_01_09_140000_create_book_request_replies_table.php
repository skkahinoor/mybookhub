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
        Schema::create('book_request_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('book_request_id');
            $table->enum('reply_by', ['user', 'admin'])->default('user');
            $table->text('message');
            $table->timestamps();

            $table->foreign('book_request_id')
                ->references('id')
                ->on('book_requests')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_request_replies');
    }
};

