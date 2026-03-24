<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('old_book_commissions', function (Blueprint $table) {
            $table->id();
            $table->decimal('percentage', 5, 2)->comment('Commission percentage for old books (e.g. 15.00 for 15%)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('old_book_commissions');
    }
};
