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
            if (!Schema::hasColumn('book_requests', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('requested_by_user');
                $table->index('vendor_id');
                $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_requests', function (Blueprint $table) {
            if (Schema::hasColumn('book_requests', 'vendor_id')) {
                $table->dropForeign(['vendor_id']);
                $table->dropIndex(['vendor_id']);
                $table->dropColumn('vendor_id');
            }
        });
    }
};
