<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('old_book_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('percentage', 5, 2)->comment('Price percentage for this condition (e.g. 80 means 80% of original price)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('old_book_conditions');
    }
};
