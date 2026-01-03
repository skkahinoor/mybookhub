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
        Schema::table('blocks', function (Blueprint $table) {
            if (!Schema::hasColumn('blocks', 'district_id')) {
                $table->unsignedBigInteger('district_id')->nullable()->after('name');
            }
        });

        // Backfill district_id from existing city relations if both columns exist
        if (Schema::hasColumn('blocks', 'district_id') && Schema::hasColumn('blocks', 'city_id')) {
            // Update blocks.district_id using cities.district_id via city_id
            DB::statement('UPDATE blocks b JOIN cities c ON b.city_id = c.id SET b.district_id = c.district_id WHERE b.district_id IS NULL');
        }

        Schema::table('blocks', function (Blueprint $table) {
            // Now drop city_id and add FKs/indexes for district_id
            if (Schema::hasColumn('blocks', 'city_id')) {
                $table->dropForeign(['city_id']);
                $table->dropIndex(['city_id']);
                $table->dropColumn('city_id');
            }

            // Ensure FK and index for district_id
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            $table->index('district_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            if (Schema::hasColumn('blocks', 'district_id')) {
                $table->dropForeign(['district_id']);
                $table->dropIndex(['district_id']);
                $table->dropColumn('district_id');
            }

            if (!Schema::hasColumn('blocks', 'city_id')) {
                $table->unsignedBigInteger('city_id')->nullable()->after('name');
                $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
                $table->index('city_id');
            }
        });
    }
};
