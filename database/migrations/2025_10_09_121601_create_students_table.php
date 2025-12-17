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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->integer('phone');
            $table->string('district');
            $table->string('city');
            $table->string('state');
            $table->integer('pincode');
            $table->string('country');
            $table->unsignedBigInteger('institution_id')->nullable(); // institution_managements table id
            $table->string('class');
            $table->string('board');
            $table->string('section')->nullable();
            $table->string('gender');
            $table->string('dob');
            $table->string('admission_date')->nullable();
            $table->string('roll_number')->unique();

            $table->timestamps();

            $table->foreign('institution_id')->references('id')->on('institution_managements')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
