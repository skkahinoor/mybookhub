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
        // Clean up invalid '0000-00-00 00:00:00' timestamps in authors table
        DB::table('authors')->where('created_at', '0000-00-00 00:00:00')->update(['created_at' => null]);
        DB::table('authors')->where('updated_at', '0000-00-00 00:00:00')->update(['updated_at' => null]);

        // Clean up invalid '0000-00-00 00:00:00' timestamps in publishers table
        DB::table('publishers')->where('created_at', '0000-00-00 00:00:00')->update(['created_at' => null]);
        DB::table('publishers')->where('updated_at', '0000-00-00 00:00:00')->update(['updated_at' => null]);

        Schema::table('authors', function (Blueprint $table) {
            $table->text('name')->nullable()->change();
        });

        Schema::table('publishers', function (Blueprint $table) {
            $table->text('name')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
        });

        Schema::table('publishers', function (Blueprint $table) {
            $table->string('name')->change();
        });
    }
};
