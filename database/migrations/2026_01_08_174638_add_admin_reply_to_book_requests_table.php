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
        Schema::table('book_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('book_requests', 'admin_reply')) {
                $table->text('admin_reply')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_requests', function (Blueprint $table) {
            if (Schema::hasColumn('book_requests', 'admin_reply')) {
                $table->dropColumn('admin_reply');
            }
        });
    }
};
