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
        Schema::table('institution_managements', function (Blueprint $table) {
            // Add new foreign key columns
            $table->unsignedBigInteger('country_id')->nullable()->after('contact_number');
            $table->unsignedBigInteger('state_id')->nullable()->after('country_id');
            $table->unsignedBigInteger('district_id')->nullable()->after('state_id');
            $table->unsignedBigInteger('city_id')->nullable()->after('district_id');
            $table->unsignedBigInteger('block_id')->nullable()->after('city_id');

            // Add foreign key constraints
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('set null');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('set null');

            // Add indexes for better performance
            $table->index('country_id');
            $table->index('state_id');
            $table->index('district_id');
            $table->index('city_id');
            $table->index('block_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institution_managements', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['country_id']);
            $table->dropForeign(['state_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['block_id']);

            // Drop indexes
            $table->dropIndex(['country_id']);
            $table->dropIndex(['state_id']);
            $table->dropIndex(['district_id']);
            $table->dropIndex(['city_id']);
            $table->dropIndex(['block_id']);

            // Drop columns
            $table->dropColumn(['country_id', 'state_id', 'district_id', 'city_id', 'block_id']);
        });
    }
};
