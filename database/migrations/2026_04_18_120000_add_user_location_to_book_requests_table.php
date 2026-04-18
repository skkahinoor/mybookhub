<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('book_requests', 'user_location')) {
                // Store "lat,lng" (e.g. "23.0225,72.5714")
                $table->string('user_location', 100)->nullable()->after('district_id');
            }

            if (!Schema::hasColumn('book_requests', 'user_location_name')) {
                $table->string('user_location_name', 255)->nullable()->after('user_location');
            }
        });
    }

    public function down(): void
    {
        Schema::table('book_requests', function (Blueprint $table) {
            if (Schema::hasColumn('book_requests', 'user_location_name')) {
                $table->dropColumn('user_location_name');
            }
            if (Schema::hasColumn('book_requests', 'user_location')) {
                $table->dropColumn('user_location');
            }
        });
    }
};

