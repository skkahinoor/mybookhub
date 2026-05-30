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
        // First make sure coordinates are fully migrated from location string if they haven't been
        try {
            $addresses = DB::table('user_addresses')->whereNotNull('location')->get();
            foreach ($addresses as $addr) {
                if (empty($addr->latitude) || empty($addr->longitude)) {
                    if (!empty($addr->location)) {
                        $parts = explode(',', $addr->location);
                        if (count($parts) === 2) {
                            DB::table('user_addresses')->where('id', $addr->id)->update([
                                'latitude' => (float) trim($parts[0]),
                                'longitude' => (float) trim($parts[1]),
                            ]);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silence
        }

        // Now drop the location column
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->string('location')->nullable()->after('mobile');
        });

        try {
            $addresses = DB::table('user_addresses')->get();
            foreach ($addresses as $addr) {
                if ($addr->latitude !== null && $addr->longitude !== null) {
                    DB::table('user_addresses')->where('id', $addr->id)->update([
                        'location' => $addr->latitude . ',' . $addr->longitude
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Silence
        }
    }
};
