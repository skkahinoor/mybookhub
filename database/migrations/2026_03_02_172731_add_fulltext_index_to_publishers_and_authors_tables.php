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
        // Add FULLTEXT index to publishers.name
        Schema::table('publishers', function (Blueprint $table) {
            $table->fullText('name', 'fulltext_publisher_name');
        });

        // Add FULLTEXT index to authors.name
        Schema::table('authors', function (Blueprint $table) {
            $table->fullText('name', 'fulltext_author_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop FULLTEXT index from publishers
        Schema::table('publishers', function (Blueprint $table) {
            $table->dropFullText('fulltext_publisher_name');
        });

        // Drop FULLTEXT index from authors
        Schema::table('authors', function (Blueprint $table) {
            $table->dropFullText('fulltext_author_name');
        });
    }
};  
