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
        // Reset old class IDs since they referenced an obsolete table 
        // to prevent foreign key integrity violations.
        \Illuminate\Support\Facades\DB::table('academic_profiles')->update(['class_id' => null]);

        try {
            Schema::table('academic_profiles', function (Blueprint $table) {
                $table->dropForeign(['class_id']);
            });
        } catch (\Exception $e) {
            // Soft fail if the foreign key is already dropped
        }

        Schema::table('academic_profiles', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('subcategories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_profiles', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->foreign('class_id')->references('id')->on('institution_classes')->onDelete('cascade');
        });
    }
};
