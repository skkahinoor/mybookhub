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
        Schema::create('institution_managements', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // school, college, university
            $table->string('type'); // school, college, university
            $table->string('board'); // board of the institution
            $table->string('class'); // class of the institution
            $table->string('contact_number'); // contact number of the institution
            $table->string('district'); // district of the institution
            $table->string('city'); // city of the institution
            $table->string('state'); // state of the institution
            $table->string('pincode'); // pincode of the institution
            $table->string('country'); // country of the institution
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institution_managements');
    }
};
