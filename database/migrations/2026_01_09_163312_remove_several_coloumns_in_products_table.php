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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('cbse');
            $table->dropColumn('operating_system');
            $table->dropColumn('screen_size');
            $table->dropColumn('occasion');
            $table->dropColumn('fit');
            $table->dropColumn('pattern');
            $table->dropColumn('sleeve');
            $table->dropColumn('ram');
            $table->dropColumn('fabric');
            $table->dropColumn('is_featured');
            $table->dropColumn('is_bestseller');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
