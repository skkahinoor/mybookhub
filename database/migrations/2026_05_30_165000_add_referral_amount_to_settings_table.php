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
        // Insert referral_amount key if it doesn't already exist
        if (!DB::table('settings')->where('key', 'referral_amount')->exists()) {
            DB::table('settings')->insert([
                'key' => 'referral_amount',
                'value' => '50',
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
        DB::table('settings')->where('key', 'referral_amount')->delete();
    }
};
