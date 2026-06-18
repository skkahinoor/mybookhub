<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert sell_book_concept_enabled key if it doesn't already exist
        if (!DB::table('settings')->where('key', 'sell_book_concept_enabled')->exists()) {
            DB::table('settings')->insert([
                'key' => 'sell_book_concept_enabled',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'sell_book_concept_enabled')->delete();
    }
};
