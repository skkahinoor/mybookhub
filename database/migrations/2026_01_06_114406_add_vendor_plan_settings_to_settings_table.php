<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert plan settings if they don't exist
        $settings = [
            [
                'key' => 'pro_plan_price',
                'value' => '49900', // ₹499 in paise
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'free_plan_book_limit',
                'value' => '100',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'give_new_users_pro_plan',
                'value' => '0', // 0 = false, 1 = true
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'pro_plan_trial_duration_days',
                'value' => '30', // 30 days trial
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'pro_plan_price',
            'free_plan_book_limit',
            'give_new_users_pro_plan',
            'pro_plan_trial_duration_days'
        ])->delete();
    }
};
