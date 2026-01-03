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
        Schema::table('users', function (Blueprint $table) {
        $table->string('father_names')->after('name')->nullable();
        $table->unsignedBigInteger('institution_id')->nullable()->after('father_names');
        $table->string('class')->after('institution_id')->nullable();
        $table->string('roll_number')->after('class')->nullable();
        $table->string('gender')->after('roll_number')->nullable();
        $table->string('dob')->after('gender')->nullable();
        $table->unsignedBigInteger('added_by')->nullable()->after('dob');





        $table->foreign('institution_id')->references('id')->on('institution_managements')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
