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
        // Remove foreign keys referencing cities if any remnants
        if (Schema::hasTable('institution_managements')) {
            Schema::table('institution_managements', function (Blueprint $table) {
                if (Schema::hasColumn('institution_managements', 'city_id')) {
                    $table->dropForeign(['city_id']);
                    $table->dropIndex(['city_id']);
                    $table->dropColumn('city_id');
                }
            });
        }

        // Finally drop cities table if exists
        if (Schema::hasTable('cities')) {
            Schema::drop('cities');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate cities table minimal structure (not repopulating data)
        if (!Schema::hasTable('cities')) {
            Schema::create('cities', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('district_id');
                $table->boolean('status')->default(true);
                $table->timestamps();
                $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
                $table->index('district_id');
            });
        }

        // Add back city_id on institution_managements if needed
        if (Schema::hasTable('institution_managements') && !Schema::hasColumn('institution_managements', 'city_id')) {
            Schema::table('institution_managements', function (Blueprint $table) {
                $table->unsignedBigInteger('city_id')->nullable()->after('district_id');
                $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
                $table->index('city_id');
            });
        }
    }
};
