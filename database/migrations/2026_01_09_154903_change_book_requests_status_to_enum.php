<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Change column to VARCHAR temporarily to allow string values
        DB::statement("ALTER TABLE book_requests MODIFY COLUMN status VARCHAR(20) DEFAULT 'pending'");

        // Step 2: Update existing integer values to string equivalents
        // 0 -> 'pending', 1 -> 'in_progress', 2 -> 'resolved'
        // Also handle any existing string values that might be invalid
        DB::statement("UPDATE book_requests SET status = CASE 
            WHEN status = '0' OR status = 0 THEN 'pending'
            WHEN status = '1' OR status = 1 THEN 'in_progress'
            WHEN status = '2' OR status = 2 THEN 'resolved'
            WHEN status IN ('pending', 'in_progress', 'resolved') THEN status
            ELSE 'pending'
        END");

        // Step 3: Change column type from VARCHAR to enum
        DB::statement("ALTER TABLE book_requests MODIFY COLUMN status ENUM('pending', 'in_progress', 'resolved') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Change enum to VARCHAR temporarily
        DB::statement("ALTER TABLE book_requests MODIFY COLUMN status VARCHAR(20) DEFAULT '0'");

        // Step 2: Convert enum values back to integers
        DB::statement("UPDATE book_requests SET status = CASE 
            WHEN status = 'pending' THEN '0'
            WHEN status = 'in_progress' THEN '1'
            WHEN status = 'resolved' THEN '2'
            ELSE '0'
        END");

        // Step 3: Change column type back to integer
        DB::statement("ALTER TABLE book_requests MODIFY COLUMN status INT DEFAULT 0");
    }
};
