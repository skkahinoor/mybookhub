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
        Schema::create('sell_book_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('book_title');
            $table->string('author_name')->nullable();
            $table->text('request_message')->nullable(); // Initial request message
            $table->enum('request_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable(); // Admin's notes/feedback
            
            // Book details (filled after approval)
            $table->string('isbn')->nullable();
            $table->string('publisher')->nullable();
            $table->string('edition')->nullable();
            $table->integer('year_published')->nullable();
            $table->text('book_condition')->nullable(); // e.g., "Good", "Fair", "Excellent"
            $table->text('book_description')->nullable();
            $table->decimal('expected_price', 10, 2)->nullable();
            $table->string('book_image')->nullable();
            
            // Final status after book details submission
            $table->enum('book_status', ['pending_review', 'approved', 'rejected', 'sold'])->nullable();
            $table->text('final_admin_notes')->nullable();
            
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
            $table->index('request_status');
            $table->index('book_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sell_book_requests');
    }
};

