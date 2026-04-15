<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dynamic_modals', function (Blueprint $table) {
            $table->id();

            // All optional as requested
            $table->text('text')->nullable();
            $table->string('image')->nullable();
            $table->string('link')->nullable();

            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dynamic_modals');
    }
};

