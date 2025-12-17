<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('header_logos', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable(); // stores image filename
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('header_logos');
    }
};
