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
        Schema::table('sell_book_requests', function (Blueprint $table) {
            $table->integer('subject_id')->nullable()->after('edition');
            $table->integer('language_id')->nullable()->after('subject_id');
            $table->integer('category_id')->nullable()->after('language_id');
            $table->integer('book_type_id')->nullable()->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sell_book_requests', function (Blueprint $table) {
            $table->dropColumn(['subject_id', 'language_id', 'category_id', 'book_type_id']);
        });
    }
};
