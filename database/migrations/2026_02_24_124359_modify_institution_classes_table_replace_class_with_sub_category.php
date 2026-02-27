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
        Schema::table('institution_classes', function (Blueprint $table) {
            $table->dropColumn('class_name');
            $table->integer('sub_category_id')->after('institution_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institution_classes', function (Blueprint $table) {
            $table->string('class_name')->after('institution_id')->nullable();
            $table->dropColumn('sub_category_id');
        });
    }
};
