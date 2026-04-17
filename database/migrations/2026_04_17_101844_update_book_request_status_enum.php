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
        // Step 1: Change column to VARCHAR temporarily
        DB::statement("ALTER TABLE book_requests MODIFY COLUMN status VARCHAR(30) DEFAULT 'awaiting_response'");

        // Step 2: Update existing values to new equivalents
        DB::statement("UPDATE book_requests SET status = 'awaiting_response' WHERE status = 'pending' OR status = '0'");
        DB::statement("UPDATE book_requests SET status = 'vendor_replied' WHERE status = 'in_progress' OR status = '1'");
        DB::statement("UPDATE book_requests SET status = 'available' WHERE status = 'resolved' OR status = '2'");

        // Step 3: Change column type to new enum
        DB::statement("ALTER TABLE book_requests MODIFY COLUMN status ENUM('awaiting_response', 'vendor_replied', 'available', 'not_available') DEFAULT 'awaiting_response'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE book_requests MODIFY COLUMN status VARCHAR(30) DEFAULT 'pending'");
        DB::statement("UPDATE book_requests SET status = 'pending' WHERE status = 'awaiting_response'");
        DB::statement("UPDATE book_requests SET status = 'in_progress' WHERE status = 'vendor_replied'");
        DB::statement("UPDATE book_requests SET status = 'resolved' WHERE status = 'available' OR status = 'not_available'");
        DB::statement("ALTER TABLE book_requests MODIFY COLUMN status ENUM('pending', 'in_progress', 'resolved') DEFAULT 'pending'");
    }
};
